<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser as PdfParser;

class LectureTextExtractor
{
    /**
     * Extract plain text from a stored file (path on the configured disk).
     * Returns sanitized, length-capped text. Empty string on extraction failure.
     */
    public function extract(string $disk, string $relativePath): string
    {
        if (! Storage::disk($disk)->exists($relativePath)) {
            return '';
        }

        $absolute = Storage::disk($disk)->path($relativePath);
        $ext      = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));

        try {
            $text = match ($ext) {
                'pdf'        => $this->extractPdf($absolute),
                'docx'       => $this->extractDocx($absolute),
                'txt'        => file_get_contents($absolute) ?: '',
                default      => '',
            };
        } catch (\Throwable $e) {
            Log::warning('LectureTextExtractor failed', [
                'path'  => $relativePath,
                'ext'   => $ext,
                'error' => $e->getMessage(),
            ]);
            return '';
        }

        return $this->sanitize($text);
    }

    public function sanitize(string $text): string
    {
        // Strip control chars except \n and \t
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $text) ?? $text;
        // Collapse 3+ newlines into 2
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;
        // Collapse runs of spaces/tabs
        $text = preg_replace('/[ \t]{2,}/', ' ', $text) ?? $text;
        // Hard cap at 60k chars
        $text = mb_substr($text, 0, (int) config('ai.max_context_chars', 60000));
        return trim($text);
    }

    private function extractPdf(string $absolutePath): string
    {
        $parser = new PdfParser();
        $pdf    = $parser->parseFile($absolutePath);
        return $pdf->getText();
    }

    private function extractDocx(string $absolutePath): string
    {
        $phpWord = IOFactory::load($absolutePath);
        $text    = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $text .= $this->extractDocxElement($element);
            }
        }

        return $text;
    }

    private function extractDocxElement(object $element): string
    {
        // Plain text-bearing elements
        if (method_exists($element, 'getText')) {
            $raw = $element->getText();
            if (is_string($raw)) {
                return $raw . "\n";
            }
        }

        $buffer = '';

        // TextRun: container of inline texts
        if ($element instanceof TextRun) {
            foreach ($element->getElements() as $child) {
                if (method_exists($child, 'getText')) {
                    $childText = $child->getText();
                    if (is_string($childText)) {
                        $buffer .= $childText;
                    }
                }
            }
            $buffer .= "\n";
        }

        // Tables: walk rows → cells → elements
        if (method_exists($element, 'getRows')) {
            foreach ($element->getRows() as $row) {
                foreach ($row->getCells() as $cell) {
                    foreach ($cell->getElements() as $cellElement) {
                        $buffer .= $this->extractDocxElement($cellElement);
                    }
                }
                $buffer .= "\n";
            }
        }

        return $buffer;
    }
}
