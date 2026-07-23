<?php

namespace EventLab\Prospect\Repositories;

use EventLab\Core\Services\HandleFactory;
use PDO;

class ContactRepository
{
    private PDO $globalPdo;
    private PDO $tenantPdo;
    private HandleFactory $handleFactory;

    public function __construct(PDO $globalPdo, PDO $tenantPdo, HandleFactory $handleFactory)
    {
        $this->globalPdo     = $globalPdo;
        $this->tenantPdo     = $tenantPdo;
        $this->handleFactory = $handleFactory;
    }

    /**
     * Import an array of contact arrays into the tenant DB.
     *
     * @param  string $tenant
     * @param  array  $contacts  Each element is an associative array of field => value.
     * @return array  ['imported' => int, 'errors' => array]
     */
    public function importContacts(string $tenant, array $contacts): array
    {
        // Load accumulator attribute map from the global DB, keyed by handle.
        // e.g. ['firstname' => ['accu' => 'word', 'slot' => 3, ...], ...]
        $attributes = $this->loadAttributes();

        $imported = 0;
        $errors   = [];

        foreach ($contacts as $contact) {
            try {
                $email = $contact['email'] ?? null;
                if (! $email) {
                    $errors[] = ['contact' => $contact, 'error' => 'Missing email — skipped'];
                    continue;
                }

                // 1. Upsert prospect row
                $prospectHandle = $this->upsertProspect($tenant, $contact);

                // 2. Build column maps for each accumulator table
                $words = [];  // ['word_3' => 'Joeri', ...]
                $bits  = [];  // ['bit_1' => 1, 'time_1' => '2026-01-01', ...]
                $tupps = [];  // ['tupp_1' => 'opt-in', 'time_1' => '2026-01-01', ...]

                foreach ($contact as $key => $rawValue) {
                    if (! isset($attributes[$key])) {
                        continue; // Not a known attribute — skip
                    }

                    $attr = $attributes[$key];
                    $accu = $attr['accu'];
                    $slot = (int) $attr['slot'];

                    switch ($accu) {
                        case 'word':
                            $words["word_{$slot}"] = (string) $rawValue;
                            break;

                        case 'bit':
                            [$bitVal, $timeVal] = $this->parseBit($rawValue);
                            $bits["bit_{$slot}"]  = $bitVal;
                            $bits["time_{$slot}"] = $timeVal;
                            break;

                        case 'tupp':
                            [$tuppVal, $timeVal] = $this->parseTupp($rawValue);
                            $tupps["tupp_{$slot}"] = $tuppVal;
                            $tupps["time_{$slot}"] = $timeVal;
                            break;
                    }
                }

                // 3. Upsert accumulator tables (only if data is present)
                if (! empty($words)) {
                    $this->upsertAccumulator('accu_words', $prospectHandle, $words);
                }
                if (! empty($bits)) {
                    $this->upsertAccumulator('accu_bits', $prospectHandle, $bits);
                }
                if (! empty($tupps)) {
                    $this->upsertAccumulator('accu_tupples', $prospectHandle, $tupps);
                }

                // 4. Post-compute pass: derive 'system' attributes from their rules
                $this->computeSystemAttributes($prospectHandle, $attributes, $words);

                $imported++;
            } catch (\Throwable $e) {
                $errors[] = ['contact' => $contact['email'] ?? '?', 'error' => $e->getMessage()];
            }
        }

        return ['imported' => $imported, 'errors' => $errors];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Load all accu_attributes from the global DB, keyed by handle.
     */
    private function loadAttributes(): array
    {
        $stmt = $this->globalPdo->query('SELECT handle, accu, slot, type, owner, rules, sane FROM accu_attributes');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $map = [];
        foreach ($rows as $row) {
            $map[$row['handle']] = $row;
        }

        return $map;
    }

    /**
     * Post-compute pass: evaluate all 'system'-type word attributes whose rules
     * reference other word handles, then write the results back into accu_words.
     *
     * Rules syntax: space-separated list of handles, each optionally suffixed with
     * ':<n>' to take the first n characters.  Empty/null resolved values are omitted.
     *
     * Examples:
     *   'firstname infix lastname'  → join non-empty values with a space
     *   'firstname:1 lastname:1'    → first char of each (initials), e.g. "JK"
     *
     * @param string $prospectHandle
     * @param array  $attributes     Full attribute map (from loadAttributes)
     * @param array  $writtenWords   ['word_3' => 'Joeri', ...] from the current import
     */
    private function computeSystemAttributes(string $prospectHandle, array $attributes, array $writtenWords): void
    {
        // Collect all attributes that have rules defined
        $systemAttrs = array_filter(
            $attributes,
            fn ($a) => $a['accu'] === 'word' && ! empty($a['rules'])
        );

        if (empty($systemAttrs)) {
            return;
        }

        // Build a handle→value lookup from what was just written (words already in memory)
        // Key: attribute handle, Value: the string value
        $handleToValue = [];
        foreach ($attributes as $handle => $attr) {
            if ($attr['accu'] !== 'word') {
                continue;
            }
            $col = 'word_' . (int) $attr['slot'];
            if (isset($writtenWords[$col])) {
                $handleToValue[$handle] = $writtenWords[$col];
            }
        }

        $computed        = []; // ['word_6' => 'Joeri Kassenaar', ...]
        $prospectUpdates = []; // ['username' => 'joeri-kassenaar', ...]

        foreach ($systemAttrs as $handle => $attr) {
            $rule = trim($attr['rules']);
            if (str_contains($rule, ';')) {
                $glue   = '';
                $tokens = explode(';', $rule);
            } elseif (str_contains($rule, '-')) {
                $glue   = '-';
                $tokens = explode('-', $rule);
            } else {
                $glue   = ' ';
                $tokens = preg_split('/\s+/', $rule);
            }

            $parts = [];

            foreach ($tokens as $token) {
                $token = trim($token);
                if ($token === '') {
                    continue;
                }

                // Token may be 'Handle' or 'Handle:n'
                if (str_contains($token, ':')) {
                    [$refHandle, $modifier] = explode(':', $token, 2);
                    $cleanHandle = strtolower($refHandle);
                    $val         = $handleToValue[$cleanHandle] ?? null;
                    if ($val !== null && $val !== '') {
                        $val     = $this->applyTokenModifier($val, $modifier);
                        $parts[] = $this->applyTokenCasing($val, $refHandle);
                    }
                } else {
                    $cleanHandle = strtolower($token);
                    $val         = $handleToValue[$cleanHandle] ?? null;
                    if ($val !== null && $val !== '') {
                        $parts[] = $this->applyTokenCasing($val, $token);
                    }
                }
            }

            if (! empty($parts)) {
                $col            = 'word_' . (int) $attr['slot'];
                $val            = implode($glue, $parts);
                $computed[$col] = $val;

                // If owner or type is 'prospect', copy value to the prospects table
                if (($attr['owner'] ?? '') === 'prospect' || ($attr['type'] ?? '') === 'prospect') {
                    $prospectUpdates[$handle] = $val;
                }
            }
        }

        if (! empty($computed)) {
            $this->upsertAccumulator('accu_words', $prospectHandle, $computed);
        }

        if (! empty($prospectUpdates)) {
            $setClause = implode(', ', array_map(fn ($c) => "`{$c}` = :{$c}", array_keys($prospectUpdates)));
            $sql       = "UPDATE `prospects` SET {$setClause}, `updated_at` = NOW() WHERE `handle` = :prospect_handle";
            $params    = [':prospect_handle' => $prospectHandle];
            foreach ($prospectUpdates as $c => $v) {
                $params[":{$c}"] = $v;
            }
            try {
                $this->tenantPdo->prepare($sql)->execute($params);
            } catch (\PDOException $e) {
                // Ignore if column doesn't exist on prospects table
            }
        }
    }

    /**
     * Apply letter casing based on how the token handle is written in the rule.
     *
     * Rules:
     *   ALL UPPERCASE (e.g. FIRSTNAME)   -> mb_strtoupper (all caps)
     *   Title / Ucfirst (e.g. Firstname) -> mb_convert_case (capitalized)
     *   all lowercase (e.g. firstname)   -> mb_strtolower (undercast)
     *
     * @param  string $value
     * @param  string $rawHandle Token as written in rule (e.g. "Firstname", "firstname", "FIRSTNAME")
     * @return string
     */
    private function applyTokenCasing(string $value, string $rawHandle): string
    {
        if (empty($value)) {
            return $value;
        }

        // ALL UPPERCASE check (e.g. FIRSTNAME or F)
        if (mb_strtoupper($rawHandle) === $rawHandle && mb_strtolower($rawHandle) !== $rawHandle) {
            return mb_strtoupper($value, 'UTF-8');
        }

        // Title / Ucfirst check (e.g. Firstname or F)
        $firstChar = mb_substr($rawHandle, 0, 1);
        if (mb_strtoupper($firstChar) === $firstChar && mb_strtolower($firstChar) !== $firstChar) {
            return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
        }

        // All lowercase check (e.g. firstname or f)
        if (mb_strtolower($rawHandle) === $rawHandle) {
            return mb_strtolower($value, 'UTF-8');
        }

        return $value;
    }

    /**
     * Apply a modifier string to a field value.
     *
     * Supported modifiers:
     *   '<n>'  (integer) — take first n characters, e.g. ':1' → first character
     *
     * @param  string $value
     * @param  string $modifier
     * @return string
     */
    private function applyTokenModifier(string $value, string $modifier): string
    {
        if (ctype_digit($modifier)) {
            return mb_substr($value, 0, (int) $modifier);
        }

        // Unknown modifier — return value unchanged
        return $value;
    }

    /**
     * Upsert a prospect row, returns the prospect's handle.
     */
    private function upsertProspect(string $tenant, array $contact): string
    {
        $email = $contact['email'];

        // Check if prospect already exists
        $stmt = $this->tenantPdo->prepare('SELECT handle FROM prospects WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();

        if ($row) {
            // Update existing prospect
            $this->tenantPdo->prepare('UPDATE prospects SET is_contact = 1, updated_at = NOW() WHERE email = :email')
                ->execute([':email' => $email]);

            return $row['handle'];
        }

        // Insert new prospect
        $handle = $this->handleFactory->create('prospects', $tenant);

        $this->tenantPdo->prepare('
            INSERT INTO prospects (handle, email, is_contact)
            VALUES (:handle, :email, 1)
        ')->execute([
            ':handle' => $handle,
            ':email'  => $email,
        ]);

        return $handle;
    }

    /**
     * Upsert a single row into an accumulator table for the given prospect.
     * Only the columns present in $columns are included in the statement.
     *
     * @param string $table         e.g. 'accu_words'
     * @param string $prospectHandle
     * @param array  $columns       ['word_3' => 'Joeri', ...]
     */
    private function upsertAccumulator(string $table, string $prospectHandle, array $columns): void
    {
        $colNames     = array_keys($columns);
        $colList      = implode(', ', array_map(fn ($c) => "`{$c}`", $colNames));
        $placeholders = implode(', ', array_map(fn ($c) => ":{$c}", $colNames));

        // ON DUPLICATE KEY UPDATE only the data columns, not the PK
        $updates = implode(', ', array_map(fn ($c) => "`{$c}` = VALUES(`{$c}`)", $colNames));

        $sql = "INSERT INTO `{$table}` (prospect, {$colList})
                VALUES (:prospect, {$placeholders})
                ON DUPLICATE KEY UPDATE {$updates}";

        $params = [':prospect' => $prospectHandle];
        foreach ($columns as $col => $val) {
            $params[":{$col}"] = $val;
        }

        $this->tenantPdo->prepare($sql)->execute($params);
    }

    /**
     * Parse a bit value.
     *
     * Supported formats:
     *   true / false (boolean)
     *   "true" / "false" (string)
     *   "true::2026-01-01"  → bit=1, time='2026-01-01'
     *   "false::2026-01-01" → bit=0, time='2026-01-01'
     *
     * @return array [int $bitValue, string|null $timeValue]
     */
    private function parseBit(mixed $raw): array
    {
        if (is_bool($raw)) {
            return [$raw ? 1 : 0, null];
        }

        $str = (string) $raw;

        if (str_contains($str, '::')) {
            [$boolPart, $timePart] = explode('::', $str, 2);
            $bitVal  = in_array(strtolower(trim($boolPart)), ['1', 'true', 'yes'], true) ? 1 : 0;
            $timeVal = trim($timePart) ?: null;

            return [$bitVal, $timeVal];
        }

        return [in_array(strtolower($str), ['1', 'true', 'yes'], true) ? 1 : 0, null];
    }

    /**
     * Parse a tupple value.
     *
     * Supported formats:
     *   "opt-in::2026-01-01" → tupp='opt-in', time='2026-01-01'
     *   "opt-out"            → tupp='opt-out', time=null
     *
     * @return array [string $tuppValue, string|null $timeValue]
     */
    private function parseTupp(mixed $raw): array
    {
        $str = (string) $raw;

        if (str_contains($str, '::')) {
            [$tuppPart, $timePart] = explode('::', $str, 2);

            return [trim($tuppPart), trim($timePart) ?: null];
        }

        return [$str, null];
    }
}
