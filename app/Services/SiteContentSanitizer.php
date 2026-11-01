<?php

namespace App\Services;

final class SiteContentSanitizer
{
    public function clean(string $html): string
    {
        $doc = new \DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div>'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $this->children($doc);
    }

    private function children(\DOMNode $node): string
    {
        $result = '';
        foreach ($node->childNodes as $child) {
            if ($child instanceof \DOMText) {
                $result .= htmlspecialchars($child->textContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

                continue;
            }
            if (! $child instanceof \DOMElement) {
                continue;
            }
            $tag = strtolower($child->tagName);
            if (in_array($tag, ['script', 'style', 'iframe', 'object', 'svg', 'math', 'template'], true)) {
                continue;
            }
            $inner = $this->children($child);
            if (! in_array($tag, ['strong', 'b', 'em', 'i', 'a', 'ul', 'li', 'p', 'br'], true)) {
                $result .= $inner;

                continue;
            }
            $attr = '';
            if ($tag === 'a' && preg_match('~^(https?://|mailto:|/[^/]|#[a-z0-9_-])~i', $child->getAttribute('href'))) {
                $attr = ' href="'.htmlspecialchars($child->getAttribute('href'), ENT_QUOTES, 'UTF-8').'" rel="noopener noreferrer"';
            }
            $result .= '<'.$tag.$attr.'>'.$inner.($tag === 'br' ? '' : '</'.$tag.'>');
        }

        return $result;
    }
}
