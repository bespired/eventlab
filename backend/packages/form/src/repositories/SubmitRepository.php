<?php
namespace EventLab\Form\Repositories;

use EventLab\Core\Services\HandleFactory;
use EventLab\Prospect\Repositories\ContactRepository;
use PDO;

class SubmitRepository
{
    private PDO $globalPdo;
    private PDO $tenantPdo;
    private HandleFactory $handleFactory;
    private ContactRepository $contactRepository;

    public function __construct(
        PDO $globalPdo,
        PDO $tenantPdo,
        HandleFactory $handleFactory,
        ContactRepository $contactRepository
    ) {
        $this->globalPdo         = $globalPdo;
        $this->tenantPdo         = $tenantPdo;
        $this->handleFactory     = $handleFactory;
        $this->contactRepository = $contactRepository;
    }

    /**
     * Store incoming form submission args into tenant's puls_forms table.
     *
     * @param  string $tenant
     * @param  object $args
     * @return string Generated handle
     */
    public function saveFormSubmission(string $tenant, object $args): string
    {
        $handle   = $this->handleFactory->create('puls_forms', $tenant);
        $formdata = json_encode($args);
        $prospect = $args->prospect ?? null;
        $name     = $args->sys ?? null;

        $stmt = $this->tenantPdo->prepare(
            'INSERT INTO puls_forms (handle, prospect, name, formdata) VALUES (:handle, :prospect, :name, :formdata)'
        );

        $stmt->execute([
            ':handle'   => $handle,
            ':prospect' => $prospect,
            ':name'     => $name,
            ':formdata' => $formdata,
        ]);

        return $handle;
    }

    /**
     * Get a form submission by handle from tenant database.
     *
     * @param  string $handle
     * @return array|null
     */
    public function getFormSubmission(string $handle): ?array
    {
        $select = 'SELECT handle, prospect, name, formdata FROM puls_forms WHERE handle = :handle LIMIT 1';
        $stmt   = $this->tenantPdo->prepare($select);
        $stmt->execute([':handle' => $handle]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * Process a stored form submission by handle.
     *
     * 1. Load submission from puls_forms.
     * 2. Look up builder_forms.validate by the 'sys' (form name) to get mnemonic → attribute map.
     * 3. Remap the mnemonic form fields to real attribute handles.
     * 4. Call ContactRepository::importContacts() to upsert prospect + accu_* data.
     *
     * @param  string $tenant
     * @param  string $handle
     * @return array
     */
    public function processFormSubmission(string $tenant, string $handle): array
    {
        // 1. Load stored submission
        $submission = $this->getFormSubmission($handle);
        if (! $submission) {
            return [
                'status'  => 'error',
                'message' => "Form submission '{$handle}' not found.",
            ];
        }

        $formdata = json_decode($submission['formdata'], true) ?? [];
        $sys      = $formdata['sys'] ?? null;
        $form     = $formdata['form'] ?? [];

        if (empty($sys)) {
            return [
                'status'  => 'error',
                'message' => "Form submission '{$handle}' is missing 'sys' (form name).",
            ];
        }

        // 2. Load the form schema (validate column) from builder_forms by name
        $mnemonicMap = $this->loadFormValidate($sys);
        if ($mnemonicMap === null) {
            return [
                'status'  => 'error',
                'message' => "Form definition '{$sys}' not found in builder_forms.",
            ];
        }

        // 3. Remap mnemonic keys → real attribute handles
        //    builder_forms.validate: { "yui16": { "attribute": "tag-firstname", ... }, ... }
        $contact = [];
        foreach ($form as $mnemonic => $value) {
            if (isset($mnemonicMap[$mnemonic]['attribute'])) {
                $attributeHandle        = $mnemonicMap[$mnemonic]['attribute'];
                $contact[$attributeHandle] = $value;
            }
        }

        if (empty($contact)) {
            return [
                'status'  => 'error',
                'message' => "No mappable fields found in form submission '{$handle}'.",
            ];
        }

        // 4. Delegate to ContactRepository to upsert prospect + accu_* tables
        $result = $this->contactRepository->importContacts($tenant, [$contact]);

        return [
            'status'   => 'success',
            'message'  => "Form submission '{$handle}' processed successfully.",
            'handle'   => $handle,
            'tenant'   => $tenant,
            'imported' => $result['imported'],
            'errors'   => $result['errors'],
        ];
    }

    /**
     * Load the validate JSON from builder_forms for the given form name (sys).
     * Returns an associative array keyed by mnemonic, or null if not found.
     *
     * @param  string $name  e.g. 'newsletter-form'
     * @return array|null
     */
    private function loadFormValidate(string $name): ?array
    {
        $stmt = $this->tenantPdo->prepare(
            'SELECT validate FROM builder_forms WHERE name = :name LIMIT 1'
        );
        $stmt->execute([':name' => $name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (! $row || empty($row['validate'])) {
            return null;
        }

        $map = json_decode($row['validate'], true);

        return is_array($map) ? $map : null;
    }
}
