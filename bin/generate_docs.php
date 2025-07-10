<?php

declare(strict_types=1);

/**
 * Simple documentation generator for Kinopoisk.dev PHP client
 *
 * Scans the src/ directory, extracts namespace, class/trait/interface/enum
 * definitions together with their PHPDoc blocks and public method signatures
 * and writes a corresponding markdown file into the docs/ directory, preserving
 * the original folder structure (e.g. src/Models/Movie.php -> docs/Models/Movie.md).
 *
 * Usage:
 *   php bin/generate_docs.php
 */

$projectRoot = dirname(__DIR__);
$srcDir      = $projectRoot . '/src';
$docsDir     = $projectRoot . '/docs';

if (!is_dir($srcDir)) {
    fwrite(STDERR, "[ERROR] src/ directory not found.\n");
    exit(1);
}

require_once $projectRoot . '/vendor/autoload.php';

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($srcDir));

$classesProcessed = 0;

foreach ($rii as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $relativePath = str_replace($srcDir . '/', '', $file->getPathname());

    try {
        $classDocs = parsePhpFile($file->getPathname());
    } catch (Throwable $e) {
        // Skip files that cannot be parsed
        fwrite(STDERR, "[WARN] Failed to parse {$relativePath}: {$e->getMessage()}\n");
        continue;
    }

    foreach ($classDocs as $doc) {
        $outPath = $docsDir . '/' . dirname($relativePath);
        if (!is_dir($outPath)) {
            mkdir($outPath, 0777, true);
        }

        $fileName = $outPath . '/' . $doc['name'] . '.md';
        file_put_contents($fileName, buildMarkdown($doc));
        $classesProcessed++;
    }
}

updateProgressFile($projectRoot, $classesProcessed);

echo "[OK] Generated documentation for {$classesProcessed} classes to {$docsDir}.\n";

/******************** Helper functions ************************/ 

/**
 * Parse a PHP file and return an array with documentation information about each
 * class/trait/interface/enum contained within.
 *
 * @param string $filePath
 * @return array<int, array<string, mixed>>
 */
function parsePhpFile(string $filePath): array
{
    $code    = file_get_contents($filePath);
    $tokens  = token_get_all($code);
    $classes = [];

    $namespace    = '';
    $currentDoc   = null;
    $collectNs    = false;
    $nsParts      = [];
    $collectClass = false;
    $classInfo    = [];
    $braceLevel   = 0;

    for ($i = 0, $len = count($tokens); $i < $len; $i++) {
        $token = $tokens[$i];

        if (is_array($token)) {
            [$id, $text] = $token;

            switch ($id) {
                case T_NAMESPACE:
                    $collectNs = true;
                    $nsParts   = [];
                    break;

                case T_DOC_COMMENT:
                    $currentDoc = $text;
                    break;

                case T_STRING:
                    if ($collectNs) {
                        $nsParts[] = $text;
                    } elseif ($collectClass) {
                        $classInfo['name']      = $text;
                        $classInfo['namespace'] = $namespace;
                        $classInfo['docblock']  = $currentDoc ?? '';
                        $classInfo['methods']   = [];
                        $collectClass           = false;
                    }
                    break;

                case T_CURLY_OPEN:
                case T_DOLLAR_OPEN_CURLY_BRACES:
                case ord('{'):
                    $braceLevel++;
                    break;

                case ord('}'):
                    $braceLevel--;
                    // End of class definition
                    if ($braceLevel === 0 && !empty($classInfo)) {
                        $classes[]   = $classInfo;
                        $classInfo   = [];
                        $currentDoc  = null;
                    }
                    break;

                case T_CLASS:
                case T_INTERFACE:
                case T_TRAIT:
                case T_ENUM:
                    // Ensure it's not anonymous class (keyword 'class' followed by '('
                    $nextToken = $tokens[$i + 1] ?? null;
                    if (is_array($nextToken) && $nextToken[0] === T_WHITESPACE) {
                        $nextToken = $tokens[$i + 2] ?? null;
                    }
                    if ($nextToken === '(') {
                        // anonymous, skip
                        break;
                    }
                    $collectClass = true;
                    break;

                case T_FUNCTION:
                    if (empty($classInfo)) {
                        break; // function outside class
                    }
                    // Capture function name
                    $j = $i + 1;
                    while (isset($tokens[$j]) && (is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE)) {
                        $j++;
                    }
                    // Skip & symbol for reference
                    if (is_array($tokens[$j]) && $tokens[$j][0] === T_BITWISE_AND) {
                        $j++;
                        while (isset($tokens[$j]) && (is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE)) {
                            $j++;
                        }
                    }
                    if (isset($tokens[$j]) && is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                        $methodName               = $tokens[$j][1];
                        $classInfo['methods'][]   = $methodName;
                    }
                    break;

                default:
                    // no-op
            }
        } else {
            // Simple string tokens '{' or '}' handled above
            if ($token === '{') {
                $braceLevel++;
            } elseif ($token === '}') {
                $braceLevel--;
                if ($braceLevel === 0 && !empty($classInfo)) {
                    $classes[]  = $classInfo;
                    $classInfo  = [];
                    $currentDoc = null;
                }
            }
        }

        // Capture completed namespace
        if ($collectNs && !is_array($token) && $token === ';') {
            $namespace = implode('\\', $nsParts);
            $collectNs = false;
        }
    }

    return $classes;
}

/**
 * Build a markdown string from parsed class info
 *
 * @param array<string, mixed> $classInfo
 */
function buildMarkdown(array $classInfo): string
{
    $fqcn   = $classInfo['namespace'] ? $classInfo['namespace'] . '\\' . $classInfo['name'] : $classInfo['name'];
    $doc    = cleanDocblock($classInfo['docblock'] ?? '') ?: 'Описание отсутствует.';
    $md  = "# {$classInfo['name']}\n\n";
    $md .= "**Полное имя:** `{$fqcn}`\n\n";
    $md .= "## Описание\n\n{$doc}\n\n";

    if (!empty($classInfo['methods'])) {
        $md .= "## Методы\n\n";
        foreach ($classInfo['methods'] as $method) {
            $md .= "- `{$method}()`\n";
        }
        $md .= "\n";
    }

    return $md;
}

/**
 * Remove comment markers from a PHPDoc block
 */
function cleanDocblock(string $doc): string
{
    $lines = explode("\n", $doc);
    $clean = [];
    foreach ($lines as $line) {
        $line = trim($line, "\t *\/");
        if ($line === '' || str_starts_with($line, '@')) {
            continue; // Skip empty and annotation lines
        }
        $clean[] = $line;
    }
    return trim(implode("\n", $clean));
}

/**
 * Append a simple summary into DOCUMENTATION_PROGRESS.md
 */
function updateProgressFile(string $projectRoot, int $classesProcessed): void
{
    $progressFile = $projectRoot . '/DOCUMENTATION_PROGRESS.md';
    if (!is_file($progressFile)) {
        return;
    }

    $summary = "\n---\n\n### Автоматическая генерация документации\n" .
               "Документация была сгенерирована для **{$classesProcessed}** классов: " . date('Y-m-d H:i:s') . "\n";

    file_put_contents($progressFile, $summary, FILE_APPEND | LOCK_EX);
}