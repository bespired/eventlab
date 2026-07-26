<?php

/**
 * EventLab Public Website Engine
 * Server-Side Rendered (PHP + Handlebars Templates with Partials & Pages)
 */

declare (strict_types = 1);

// Lightweight Handlebars template renderer with Partials support
class HandlebarsRenderer
{
    /**
     * Render a Handlebars template string with given data context and template directory
     */
    public static function render(string $template, array $context, string $templatesDir = ''): string
    {
        if ($templatesDir === '') {
            $templatesDir = __DIR__ . '/templates';
        }

        // 0. Resolve partial inclusions: {{> partialName }}
        $template = self::resolvePartials($template, $templatesDir);

        // 1. Process #each blocks: {{#each list}}...{{/each}}
        $template = preg_replace_callback(
            '/\{\{#each\s+([a-zA-Z0-9_\.]+)\}\}(.*?)\{\{\/each\}\}/s',
            function ($matches) use ($context) {
                $key   = trim($matches[1]);
                $inner = $matches[2];
                $items = self::getNestedValue($context, $key);

                if (! is_array($items)) {
                    return '';
                }

                $output = '';
                foreach ($items as $item) {
                    $itemContext  = is_array($item) ? array_merge($context, $item) : $context;
                    $output      .= self::renderVariablesAndIfs($inner, $itemContext, $item);
                }
                return $output;
            },
            $template
        );

        // 2. Process remaining variables and if blocks
        return self::renderVariablesAndIfs($template, $context, null);
    }

    /**
     * Recursively resolve {{> partialName }} tags using partial files from templates directory
     */
    private static function resolvePartials(string $template, string $templatesDir, int $depth = 0): string
    {
        if ($depth > 10) {
            return $template; // Safety guard against circular inclusion
        }

        return preg_replace_callback(
            '/\{\{>\s*([a-zA-Z0-9_\-\/]+)\s*\}\}/',
            function ($matches) use ($templatesDir, $depth) {
                $partialName = trim($matches[1]);
                $content     = self::loadPartialFile($partialName, $templatesDir);

                if ($content === null) {
                    return "<!-- Partial '$partialName' not found -->";
                }

                // Recursively resolve nested partials inside this partial
                return self::resolvePartials($content, $templatesDir, $depth + 1);
            },
            $template
        );
    }

    private static function loadPartialFile(string $name, string $templatesDir): ?string
    {
        $candidates = [];

        if (str_contains($name, '/')) {
            $candidates[] = $templatesDir . '/' . $name . '.hbs';
        }

        $candidates[] = $templatesDir . '/partials/' . $name . '.hbs';
        $candidates[] = $templatesDir . '/forms/' . $name . '.hbs';
        $candidates[] = $templatesDir . '/styles/' . $name . '.hbs';
        $candidates[] = $templatesDir . '/scripts/' . $name . '.hbs';
        $candidates[] = $templatesDir . '/pages/' . $name . '.hbs';
        $candidates[] = $templatesDir . '/' . $name . '.hbs';

        foreach ($candidates as $file) {
            if (file_exists($file)) {
                return file_get_contents($file);
            }
        }

        return null;
    }

    private static function renderVariablesAndIfs(string $template, array $context, $currentItem = null): string
    {
        // Process #if ... {{else}} ... /if
        $template = preg_replace_callback(
            '/\{\{#if\s+([a-zA-Z0-9_\.]+)\}\}(.*?)(?:\{\{else\}\}(.*?))?\{\{\/if\}\}/s',
            function ($matches) use ($context, $currentItem) {
                $key       = trim($matches[1]);
                $val       = self::resolveValue($key, $context, $currentItem);
                $ifBlock   = $matches[2];
                $elseBlock = $matches[3] ?? '';

                return $val ? $ifBlock : $elseBlock;
            },
            $template
        );

        // Process {{variable}}
        $template = preg_replace_callback(
            '/\{\{\s*([a-zA-Z0-9_\.]+)\s*\}\}/',
            function ($matches) use ($context, $currentItem) {
                $key = trim($matches[1]);
                $val = self::resolveValue($key, $context, $currentItem);
                return htmlspecialchars((string) ($val ?? ''), ENT_QUOTES, 'UTF-8');
            },
            $template
        );

        return $template;
    }

    private static function resolveValue(string $key, array $context, $currentItem = null)
    {
        if ($key === 'this' && $currentItem !== null) {
            return $currentItem;
        }

        if (is_array($currentItem) && array_key_exists($key, $currentItem)) {
            return $currentItem[$key];
        }

        return self::getNestedValue($context, $key);
    }

    private static function getNestedValue(array $arr, string $key)
    {
        $parts   = explode('.', $key);
        $current = $arr;
        foreach ($parts as $part) {
            if (is_array($current) && array_key_exists($part, $current)) {
                $current = $current[$part];
            } else {
                return null;
            }
        }
        return $current;
    }
}

// -------------------------------------------------------------
// Website Data Context Initialization
// -------------------------------------------------------------

$dataContext = [
    'siteTitle'    => 'EventLab Engine',
    'heroTitle'    => 'Empowering Hybrid Monorepo & Dynamic Event Architecture',
    'heroSubtitle' => 'Lightweight, framework-free PHP API engine paired with Handlebars SSR and high-performance Vue management GUI.',
    'currentYear'  => date('Y'),
    'stats'        => [
        ['value' => '99.99%', 'label' => 'Uptime Guarantee'],
        ['value' => '0.8ms', 'label' => 'API Routing Speed'],
        ['value' => '100k+', 'label' => 'Concurrent Attendees'],
        ['value' => '0 Bloat', 'label' => 'Framework Overhead'],
    ],
    'events'       => [
        [
            'title'       => 'Global Developer Summit 2026',
            'description' => 'Keynote presentations on lightweight monorepo architecture, micro-services, and zero-overhead routing.',
            'date'        => 'Aug 14, 2026',
            'status'      => 'Live Registration',
            'isLive'      => true,
            'location'    => 'San Francisco, CA',
            'attendees'   => '2,450',
        ],
        [
            'title'       => 'Tech & AI Expo 2026',
            'description' => 'Exhibition of automated coding agents, real-time telemetry pipelines, and dynamic event management.',
            'date'        => 'Sep 28, 2026',
            'status'      => 'Upcoming',
            'isLive'      => false,
            'location'    => 'Amsterdam, NL',
            'attendees'   => '5,100',
        ],
        [
            'title'       => 'EventLab Open Source Hackathon',
            'description' => 'Build custom packages and dynamic controller extensions on the EventLab PHP core engine.',
            'date'        => 'Oct 12, 2026',
            'status'      => 'Upcoming',
            'isLive'      => false,
            'location'    => 'Online Virtual',
            'attendees'   => '850',
        ],
    ],
];

// Determine page template path (defaulting to website/templates/pages/index.hbs)
$templatesDir = __DIR__ . '/templates';
$templatePath = $templatesDir . '/pages/index.hbs';

if (! file_exists($templatePath)) {
    // Fallback if index.hbs is at root of templates
    $templatePath = $templatesDir . '/index.hbs';
}

if (! file_exists($templatePath)) {
    http_response_code(404);
    echo "<h1>404 Template Not Found</h1>";
    exit;
}

$templateContent = file_get_contents($templatePath);
$html            = HandlebarsRenderer::render($templateContent, $dataContext, $templatesDir);

// Output HTML response
header('Content-Type: text/html; charset=utf-8');
echo $html;
