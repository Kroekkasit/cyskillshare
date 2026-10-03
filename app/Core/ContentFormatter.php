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
    public static function render(string $markdown): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $markdown);

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
        $text = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $text) ?? $text;

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

        // Autolink bare https URLs
        $text = preg_replace_callback(
            '/(?<!["\'>])(https?:\/\/[^\s<]+)/',
            static function (array $m): string {
                $href = htmlspecialchars($m[1], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                return '<a href="' . $href . '" rel="noopener noreferrer" target="_blank">' . $href . '</a>';
            },
            $text
        ) ?? $text;

        // Unordered lists
        $text = preg_replace_callback(
            '/(?:^|\n)(?:- .+(?:\n|$))+/',
            static function (array $m): string {
                $items = preg_split('/\n/', trim($m[0])) ?: [];
                $html = "<ul class=\"md-list\">\n";
                foreach ($items as $item) {
                    $item = preg_replace('/^- /', '', trim($item)) ?? '';
                    if ($item !== '') {
                        $html .= '<li>' . $item . '</li>';
                    }
                }
                return "\n" . $html . "</ul>\n";
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
            if (str_starts_with($part, '<ul') || str_starts_with($part, '@@CODEBLOCK')) {
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

    public static function excerpt(string $text, int $length = 180): string
    {
        $plain = trim(preg_replace('/\s+/', ' ', strip_tags(self::render($text))) ?? '');
        if (mb_strlen($plain) <= $length) {
            return $plain;
        }
        return rtrim(mb_substr($plain, 0, $length - 1)) . '…';
    }
}
