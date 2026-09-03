<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentDownloadController extends Controller
{
    public function __invoke(Request $request, Document $document): StreamedResponse
    {
        // Strict Tenant Security Validation
        if ($document->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized access to this tenant document.');
        }

        // Try private storage first, fallback to public for backward compatibility
        $disk = Storage::disk('private')->exists($document->file_path) ? 'private' : 'public';

        if (!Storage::disk($disk)->exists($document->file_path)) {
            abort(404, 'Document file not found on storage server.');
        }

        $filename = $document->original_filename ?? $document->name;

        return Storage::disk($disk)->download($document->file_path, $filename);
    }
}
