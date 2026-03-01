<?php

namespace Idno\Core;

/**
 * Extracts translatable strings from PHP source files and generates
 * gettext .pot (Portable Object Template) files.
 *
 * Uses PHP's built-in token_get_all() for reliable parsing, looking
 * for ->_() and ->esc_() method calls used by the Idno i18n system.
 *
 * Replaces the external mapkyca/known-language-tools package.
 */
class PotGenerator
{

    /**
     * Extracted strings: msgid => array of {file, line} references.
     * @var array<string, array<array{file: string, line: int}>>
     */
    private array $strings = [];

    /**
     * Method names to extract translations from.
     * @var string[]
     */
    private array $methodNames = ['_', 'esc_'];

    /**
     * Extract translatable strings from a single PHP file.
     *
     * Tokenizes the file and scans for the pattern:
     *   -> _( 'string' )  or  -> esc_( 'string' )
     *
     * @param string $filePath Absolute path to the PHP file
     * @param string $basePath Base path to compute relative file references
     */
    public function extractFromFile(string $filePath, string $basePath = ''): void
    {
        $code = @file_get_contents($filePath);
        if ($code === false || $code === '') {
            return;
        }

        $relativePath = $filePath;
        if (!empty($basePath)) {
            $basePath = rtrim($basePath, '/') . '/';
            if (str_starts_with($filePath, $basePath)) {
                $relativePath = './' . substr($filePath, strlen($basePath));
            }
        }

        $this->extractFromSource($code, $relativePath);
    }

    /**
     * Extract translatable strings from PHP source code.
     *
     * @param string $source PHP source code
     * @param string $fileReference File path to use in #: references
     */
    public function extractFromSource(string $source, string $fileReference = 'unknown'): void
    {
        try {
            $tokens = token_get_all($source);
        } catch (\ParseError $e) {
            return;
        }

        $count = count($tokens);
        $i = 0;

        while ($i < $count) {
            $token = $tokens[$i];

            // Look for T_OBJECT_OPERATOR (->)
            if (is_array($token) && $token[0] === T_OBJECT_OPERATOR) {
                $nextIdx = $this->skipWhitespace($tokens, $i + 1, $count);
                if ($nextIdx === null) {
                    $i++;
                    continue;
                }

                $nextToken = $tokens[$nextIdx];

                // Check if it's a method name we're interested in (_ or esc_)
                if (is_array($nextToken) && $nextToken[0] === T_STRING
                    && in_array($nextToken[1], $this->methodNames, true)) {

                    $parenIdx = $this->skipWhitespace($tokens, $nextIdx + 1, $count);
                    if ($parenIdx === null) {
                        $i++;
                        continue;
                    }

                    // Check for opening parenthesis
                    if (is_string($tokens[$parenIdx]) && $tokens[$parenIdx] === '(') {
                        $stringIdx = $this->skipWhitespace($tokens, $parenIdx + 1, $count);
                        if ($stringIdx === null) {
                            $i++;
                            continue;
                        }

                        $stringToken = $tokens[$stringIdx];

                        // Check if the first argument is a constant string
                        if (is_array($stringToken) && $stringToken[0] === T_CONSTANT_ENCAPSED_STRING) {
                            $rawString = $stringToken[1];
                            $line = $stringToken[2];
                            $decoded = $this->decodePhpString($rawString);

                            if ($decoded !== null) {
                                $this->addString($decoded, $fileReference, $line);
                            }
                        }
                    }
                }
            }

            $i++;
        }
    }

    /**
     * Scan a directory recursively for PHP files and extract strings.
     *
     * @param string $directory Directory to scan
     * @param string $basePath Base path for relative references
     */
    public function extractFromDirectory(string $directory, string $basePath = ''): void
    {
        if (empty($basePath)) {
            $basePath = $directory;
        }

        $directory = rtrim($directory, '/');

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        $files = [];
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        sort($files);

        foreach ($files as $filePath) {
            $this->extractFromFile($filePath, $basePath);
        }
    }

    /**
     * Extract from specific file paths.
     *
     * @param string[] $files Array of file paths
     * @param string $basePath Base path for relative references
     */
    public function extractFromFiles(array $files, string $basePath = ''): void
    {
        sort($files);
        foreach ($files as $filePath) {
            $this->extractFromFile($filePath, $basePath);
        }
    }

    /**
     * Get all extracted strings.
     *
     * @return array<string, array<array{file: string, line: int}>>
     */
    public function getStrings(): array
    {
        return $this->strings;
    }

    /**
     * Reset extracted strings.
     */
    public function reset(): void
    {
        $this->strings = [];
    }

    /**
     * Generate a .pot file content string.
     *
     * @param string $domain Translation domain name
     * @return string Valid gettext .pot file content
     */
    public function generatePot(string $domain = ''): string
    {
        $output = '';

        // POT header
        $date = date('Y-m-d H:iO');
        $output .= "# Translation template for {$domain}.\n";
        $output .= "#\n";
        $output .= "#, fuzzy\n";
        $output .= "msgid \"\"\n";
        $output .= "msgstr \"\"\n";
        $output .= "\"Project-Id-Version: {$domain}\\n\"\n";
        $output .= "\"Report-Msgid-Bugs-To: \\n\"\n";
        $output .= "\"POT-Creation-Date: {$date}\\n\"\n";
        $output .= "\"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n\"\n";
        $output .= "\"Last-Translator: \\n\"\n";
        $output .= "\"Language-Team: \\n\"\n";
        $output .= "\"MIME-Version: 1.0\\n\"\n";
        $output .= "\"Content-Type: text/plain; charset=UTF-8\\n\"\n";
        $output .= "\"Content-Transfer-Encoding: 8bit\\n\"\n";
        $output .= "\n";

        // Sort strings by their first reference for deterministic output
        $sorted = $this->strings;
        uksort($sorted, function ($a, $b) use ($sorted) {
            $refA = $sorted[$a][0];
            $refB = $sorted[$b][0];
            $cmp = strcmp($refA['file'], $refB['file']);
            if ($cmp !== 0) {
                return $cmp;
            }
            return $refA['line'] <=> $refB['line'];
        });

        foreach ($sorted as $msgid => $references) {
            // Output file references
            foreach ($references as $ref) {
                $output .= "#: {$ref['file']}:{$ref['line']}\n";
            }

            // Output msgid with proper escaping
            $escaped = $this->escapeForPot($msgid);
            $output .= "msgid \"{$escaped}\"\n";
            $output .= "msgstr \"\"\n";
            $output .= "\n";
        }

        return $output;
    }

    /**
     * Generate a .pot file without the header (legacy-compatible format).
     *
     * This produces output compatible with the old buildpot.php script,
     * which did not include a POT header.
     *
     * @return string POT entries without header
     */
    public function generatePotWithoutHeader(): string
    {
        $output = '';

        // Sort strings by their first reference for deterministic output
        $sorted = $this->strings;
        uksort($sorted, function ($a, $b) use ($sorted) {
            $refA = $sorted[$a][0];
            $refB = $sorted[$b][0];
            $cmp = strcmp($refA['file'], $refB['file']);
            if ($cmp !== 0) {
                return $cmp;
            }
            return $refA['line'] <=> $refB['line'];
        });

        foreach ($sorted as $msgid => $references) {
            // Use only the first reference (like the old buildpot.php)
            $ref = $references[0];
            $escaped = $this->escapeForPot($msgid);

            $output .= "#: {$ref['file']}:{$ref['line']}\n";
            $output .= "msgid \"{$escaped}\"\n";
            $output .= "msgstr \"\"\n";
            $output .= "\n";
        }

        return $output;
    }

    /**
     * Add a translatable string with its file reference.
     *
     * @param string $string The translatable string
     * @param string $file File path reference
     * @param int $line Line number
     */
    private function addString(string $string, string $file, int $line): void
    {
        if (!isset($this->strings[$string])) {
            $this->strings[$string] = [];
        }

        $this->strings[$string][] = [
            'file' => $file,
            'line' => $line,
        ];
    }

    /**
     * Skip whitespace/comment tokens and return the index of the next meaningful token.
     *
     * @param array $tokens Token array
     * @param int $start Start index
     * @param int $count Total token count
     * @return int|null Index of next meaningful token, or null if none found
     */
    private function skipWhitespace(array $tokens, int $start, int $count): ?int
    {
        for ($i = $start; $i < $count; $i++) {
            $token = $tokens[$i];
            if (is_array($token)) {
                if ($token[0] === T_WHITESPACE || $token[0] === T_COMMENT || $token[0] === T_DOC_COMMENT) {
                    continue;
                }
            }
            return $i;
        }
        return null;
    }

    /**
     * Decode a PHP string token value (strip quotes, process escape sequences).
     *
     * @param string $raw Raw token value including quotes
     * @return string|null Decoded string value, or null if invalid
     */
    private function decodePhpString(string $raw): ?string
    {
        if (strlen($raw) < 2) {
            return null;
        }

        $quote = $raw[0];

        if ($quote === "'") {
            // Single-quoted string: only \\ and \' are escape sequences
            $inner = substr($raw, 1, -1);
            $result = str_replace("\\'", "'", $inner);
            $result = str_replace("\\\\", "\\", $result);
            return $result;
        }

        if ($quote === '"') {
            // Double-quoted string: full PHP escape sequence processing
            $inner = substr($raw, 1, -1);
            return stripcslashes($inner);
        }

        return null;
    }

    /**
     * Escape a string for inclusion in a .pot file msgid.
     *
     * @param string $string The string to escape
     * @return string Escaped string suitable for .pot file
     */
    private function escapeForPot(string $string): string
    {
        $string = str_replace('\\', '\\\\', $string);
        $string = str_replace('"', '\\"', $string);
        $string = str_replace("\n", '\\n', $string);
        $string = str_replace("\r", '\\r', $string);
        $string = str_replace("\t", '\\t', $string);
        return $string;
    }
}
