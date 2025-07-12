<?php

namespace App\Http\Controllers;

use App\Models\MessageAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpPresentation\IOFactory as PresentationIOFactory;

class FileUploadController extends Controller
{
    const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB

    const ALLOWED_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        'text/csv',
        'text/markdown',
        'text/x-markdown',
    ];

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
            'message_id' => 'nullable|exists:messages,id',
        ]);

        $file = $request->file('file');
        $mimeType = $file->getMimeType();
        $fileName = $file->getClientOriginalName();
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Check by MIME type or file extension for markdown files
        $isAllowedType = in_array($mimeType, self::ALLOWED_TYPES);
        $isMarkdownFile = in_array($fileExtension, ['md', 'markdown']);
        
        if (!$isAllowedType && !$isMarkdownFile) {
            return response()->json(['error' => 'File type not allowed'], 422);
        }
        
        // Override MIME type for markdown files if needed
        if ($isMarkdownFile && !$isAllowedType) {
            $mimeType = 'text/markdown';
        }

        // Generate unique filename
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = 'attachments/' . date('Y/m/d');
        
        // Store file
        $storedPath = $file->storeAs($path, $filename, 'public');

        // Extract content based on file type
        $extractedContent = $this->extractContent($file, $mimeType);

        // If message_id is provided, create attachment record
        if ($request->message_id) {
            $attachment = MessageAttachment::create([
                'message_id' => $request->message_id,
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $mimeType,
                'size' => $file->getSize(),
                'path' => $storedPath,
                'extracted_content' => $extractedContent,
                'metadata' => [
                    'uploaded_at' => now()->toIso8601String(),
                ],
            ]);

            return response()->json([
                'id' => $attachment->id,
                'filename' => $attachment->original_name,
                'url' => $attachment->public_url,
                'mime_type' => $attachment->mime_type,
                'size' => $attachment->size,
                'is_image' => $attachment->isImage(),
            ]);
        }

        // Return temporary file info without creating attachment
        return response()->json([
            'temp_path' => $storedPath,
            'filename' => $file->getClientOriginalName(),
            'url' => asset('storage/' . $storedPath),
            'mime_type' => $mimeType,
            'size' => $file->getSize(),
            'is_image' => str_starts_with($mimeType, 'image/'),
            'extracted_content' => $extractedContent,
        ]);
    }

    private function extractContent($file, $mimeType): ?string
    {
        try {
            switch ($mimeType) {
                case 'text/plain':
                case 'text/csv':
                case 'text/markdown':
                case 'text/x-markdown':
                    return file_get_contents($file->getRealPath());

                case 'application/msword':
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                    return $this->extractWordContent($file);

                case 'application/vnd.ms-excel':
                case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                    return $this->extractExcelContent($file);

                case 'application/vnd.ms-powerpoint':
                case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
                    return $this->extractPowerPointContent($file);

                case 'application/pdf':
                    return $this->extractPdfContent($file);

                default:
                    return null;
            }
        } catch (\Exception $e) {
            \Log::error('Error extracting content from file', [
                'mime_type' => $mimeType,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function extractWordContent($file): string
    {
        $phpWord = WordIOFactory::load($file->getRealPath());
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }

        return trim($text);
    }

    private function extractExcelContent($file): string
    {
        $spreadsheet = SpreadsheetIOFactory::load($file->getRealPath());
        $text = '';

        foreach ($spreadsheet->getActiveSheet()->toArray() as $row) {
            $text .= implode("\t", array_filter($row)) . "\n";
        }

        return trim($text);
    }

    private function extractPowerPointContent($file): string
    {
        $presentation = PresentationIOFactory::load($file->getRealPath());
        $text = '';

        foreach ($presentation->getAllSlides() as $slide) {
            foreach ($slide->getShapeCollection() as $shape) {
                if (method_exists($shape, 'getText')) {
                    $text .= $shape->getText() . "\n";
                }
            }
        }

        return trim($text);
    }

    private function extractPdfContent($file): string
    {
        // For PDF extraction, we'll need a library like smalot/pdfparser
        // For now, return empty string
        return '';
    }
}