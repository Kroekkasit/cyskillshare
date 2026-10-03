<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Restricted Markdown → safe HTML.
 * Escape first, then apply a small allowlist of formatting.
 * Never allows raw HTML or script execution.
 */
final class ContentFormatter
{
    /**
     * @return array{html:string,toc:list<array{level:int,id:string,text:string}>}
     */
    public static function renderWithToc(string $markdown): array
    {
        $toc = [];
        $html = self::render($markdown, $toc);
        return ['html' => $html, 'toc' => $toc];
    }

    /**
     * @param list<array{level:int,id:string,text:string}>|null $tocOut
     */
    public static function render(string $markdown, ?array &$tocOut = null): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $markdown);
        $tocOut = $tocOut ?? [];

        // Extract fenced code blocks before escaping/formatting
        $blocks = [];
        $text = preg_replace_callback(
            '/```([a-zA-Z0-9_-]+)?\n(.*?)```/s',
            static function (array $m) use (&$blocks): string {
                $lang = trim((string) ($m[1] ?? ''));
                $code = rtrim((string) $m[2], "\n");
                $key = '@@CODEBLOCK' . count($blocks) . '@@';
                $blocks[$key] = [
                    'lang' => $lang,
                    'code' => $code,
                ];
                return $key;
            },
            $text
        ) ?? $text;

        $text = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        // Inline code
        $text = preg_replace_callback(
            '/`([^`\n]+)`/',
            static fn(array $m): string => '<code class="inline-code">' . $m[1] . '</code>',
            $text
        ) ?? $text;

        // Bold / italic
        $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text) ?? $text;
        $text = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $text) ?? $text;

        // Safe links — http(s) only
        $text = preg_replace_callback(
            '/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/',
            static function (array $m): string {
                $label = $m[1];
                $href = htmlspecialchars($m[2], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                return '<a href="' . $href . '" rel="noopener noreferrer" target="_blank">' . $label . '</a>';
            },
            $text
        ) ?? $text;

        // Headings ## / ### → collect TOC
        $headingIds = [];
        $text = preg_replace_callback(
            '/^(#{2,3})\s+(.+)$/m',
            static function (array $m) use (&$tocOut, &$headingIds): string {
                $level = strlen($m[1]);
                $raw = trim($m[2]);
                $plain = trim(strip_tags(html_entity_decode($raw, ENT_QUOTES, 'UTF-8')));
                $id = self::headingId($plain, $headingIds);
                $tocOut[] = ['level' => $level, 'id' => $id, 'text' => $plain];
                $tag = 'h' . $level;
                return '<' . $tag . ' id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '" class="md-heading">'
                    . $raw . '</' . $tag . '>';
            },
            $text
        ) ?? $text;

        // Blockquotes
        $text = preg_replace_callback(
            '/(?:^|\n)(?:> .+(?:\n|$))+/',
            static function (array $m): string {
                $lines = preg_split('/\n/', trim($m[0])) ?: [];
                $body = '';
                foreach ($lines as $line) {
                    $body .= (preg_replace('/^>\s?/', '', $line) ?? '') . '<br>';
                }
                return "\n<blockquote class=\"md-quote\">" . $body . "</blockquote>\n";
            },
            $text
        ) ?? $text;

        // HR
        $text = preg_replace('/(?:^|\n)---+(?:\n|$)/', "\n<hr class=\"md-hr\">\n", $text) ?? $text;

        // Unordered lists
        $text = preg_replace_callback(
            '/(?:^|\n)(?:[-*] .+(?:\n|$))+/',
            static function (array $m): string {
                $items = preg_split('/\n/', trim($m[0])) ?: [];
                $html = "<ul class=\"md-list\">\n";
                foreach ($items as $item) {
                    $item = preg_replace('/^[-*] /', '', trim($item)) ?? '';
                    if ($item !== '') {
                        $html .= '<li>' . $item . '</li>';
                    }
                }
                return "\n" . $html . "</ul>\n";
            },
            $text
        ) ?? $text;

        // Ordered lists
        $text = preg_replace_callback(
            '/(?:^|\n)(?:\d+\. .+(?:\n|$))+/',
            static function (array $m): string {
                $items = preg_split('/\n/', trim($m[0])) ?: [];
                $html = "<ol class=\"md-list\">\n";
                foreach ($items as $item) {
                    $item = preg_replace('/^\d+\. /', '', trim($item)) ?? '';
                    if ($item !== '') {
                        $html .= '<li>' . $item . '</li>';
                    }
                }
                return "\n" . $html . "</ol>\n";
            },
            $text
        ) ?? $text;

        // Simple tables (header | row)
        $text = preg_replace_callback(
            '/(?:^|\n)(\|.+\|(?:\n\|[-:| ]+\|)?(?:\n\|.+\|)+)/',
            static function (array $m): string {
                $lines = array_values(array_filter(array_map('trim', explode("\n", trim($m[1])))));
                if (count($lines) < 2) {
                    return $m[0];
                }
                $parse = static function (string $line): array {
                    $cells = array_map('trim', explode('|', trim($line, '|')));
                    return $cells;
                };
                $header = $parse($lines[0]);
                $start = 1;
                if (isset($lines[1]) && preg_match('/^[\s|:-]+$/', $lines[1])) {
                    $start = 2;
                }
                $html = "<div class=\"md-table-wrap\"><table class=\"md-table\"><thead><tr>";
                foreach ($header as $cell) {
                    $html .= '<th>' . $cell . '</th>';
                }
                $html .= '</tr></thead><tbody>';
                for ($i = $start; $i < count($lines); $i++) {
                    $html .= '<tr>';
                    foreach ($parse($lines[$i]) as $cell) {
                        $html .= '<td>' . $cell . '</td>';
                    }
                    $html .= '</tr>';
                }
                return "\n" . $html . '</tbody></table></div>
';
            },
            $text
        ) ?? $text;

        // Paragraphs / line breaks
        $parts = preg_split('/\n{2,}/', trim($text)) ?: [];
        $html = '';
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            if (preg_match('/^<(ul|ol|h2|h3|blockquote|hr|div|table)/', $part)
                || str_starts_with($part, '@@CODEBLOCK')
            ) {
                $html .= $part;
                continue;
            }
            $html .= '<p>' . nl2br($part, false) . '</p>';
        }

        // Restore code blocks
        foreach ($blocks as $key => $block) {
            $lang = $block['lang'] !== '' ? ' data-lang="' . htmlspecialchars($block['lang'], ENT_QUOTES, 'UTF-8') . '"' : '';
            $code = htmlspecialchars($block['code'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $langLabel = $block['lang'] !== ''
                ? htmlspecialchars($block['lang'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                : 'code';
            $replacement = '<div class="code-block">'
                . '<div class="code-block-toolbar"><span class="code-lang">' . $langLabel . '</span>'
                . '<button type="button" class="btn-copy-code" data-copy-code aria-label="Copy code">Copy</button></div>'
                . '<pre' . $lang . '><code>' . $code . '</code></pre></div>';
            $html = str_replace($key, $replacement, $html);
        }

        return $html;
    }

    /**
     * @param array<string, true> $used
     */
    private static function headingId(string $text, array &$used): string
    {
        $id = strtolower(trim($text));
        $id = preg_replace('/[^a-z0-9]+/', '-', $id) ?? 'section';
        $id = trim($id, '-') ?: 'section';
        $id = substr($id, 0, 80);
        $base = $id;
        $n = 2;
        while (isset($used[$id])) {
            $id = $base . '-' . $n;
            $n++;
        }
        $used[$id] = true;
        return $id;
    }

    public static function excerpt(string $text, int $length = 180): string
    {
        $plain = trim(preg_replace('/\s+/', ' ', strip_tags(self::render($text))) ?? '');
        if (mb_strlen($plain) <= $length) {
            return $plain;
        }
        return rtrim(mb_substr($plain, 0, $length - 1)) . '…';
    }

    public static function readingTimeMinutes(string $markdown): int
    {
        $plain = trim(strip_tags(self::render($markdown)));
        $words = str_word_count($plain);
        $wpm = max(100, (int) config('writeups.words_per_minute', 200));
        return max(1, (int) ceil($words / $wpm));
    }
}
