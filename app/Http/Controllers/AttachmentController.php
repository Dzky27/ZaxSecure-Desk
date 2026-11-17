<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentAttachment;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentController extends Controller
{
    public function store(Request $request, Incident $incident)
    {
        $this->authorize('view', $incident);

        $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp,txt'],
        ], [
            'file.required' => 'Pilih berkas bukti terlebih dahulu.',
            'file.file' => 'Berkas yang diunggah tidak valid.',
            'file.max' => 'Ukuran berkas maksimal adalah 10 MB.',
            'file.mimes' => 'Format berkas harus berupa PDF, JPG, JPEG, PNG, WEBP, atau TXT.',
        ]);

        $file = $request->file('file');
        $originalName = basename($file->getClientOriginalName());
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();

        // Validate MIME type against strict allowlist
        $allowedMimes = [
            'pdf' => ['application/pdf'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'webp' => ['image/webp'],
            'txt' => ['text/plain'],
        ];

        $allowedForExtension = $allowedMimes[$extension] ?? [];
        if (! in_array($mimeType, $allowedForExtension, true)) {
            return back()->withErrors(['file' => 'Tipe konten berkas (MIME) tidak sesuai dengan ekstensi berkas yang diizinkan.']);
        }

        // Compute SHA-256 integrity hash
        $fileHash = @hash_file('sha256', $file->getRealPath()) ?: null;

        // Generate a safe random filename
        $filename = Str::random(40) . '.' . $extension;
        $disk = Storage::disk('private');

        // Store with a safe path in private storage
        $savedPath = $disk->putFileAs('incidents/' . $incident->id, $file, $filename);

        if (! $savedPath) {
            return back()->withErrors(['file' => 'Gagal menyimpan berkas bukti ke penyimpanan aman.']);
        }

        if (! $disk->mimeType($savedPath)) {
            $disk->delete($savedPath);
            return back()->withErrors(['file' => 'Gagal memverifikasi tipe berkas bukti.']);
        }

        if ($disk->size($savedPath) !== $fileSize) {
            $disk->delete($savedPath);
            return back()->withErrors(['file' => 'Ukuran berkas tidak cocok saat proses penyimpanan.']);
        }

        try {
            $attachment = IncidentAttachment::create([
                'incident_id' => $incident->id,
                'uploaded_by' => $request->user()->id,
                'original_name' => $originalName,
                'storage_path' => $savedPath,
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'file_hash' => $fileHash,
            ]);
        } catch (\Exception $e) {
            $disk->delete($savedPath);

            Log::warning('Attachment metadata creation failed; stored file deleted', [
                'incident_id' => $incident->id,
            ]);

            throw $e;
        }

        AuditLogger::logActivity(
            'incident.attachment_uploaded',
            'Incident',
            $incident->id,
            [
                'ticket_number' => $incident->ticket_number,
                'attachment_id' => $attachment->id,
                'original_name' => $originalName,
                'file_size' => $fileSize,
            ],
            $request
        );

        $redirectRoute = $request->user()->isAdmin() ? 'admin.incidents.show' : 'user.incidents.show';

        return redirect()->route($redirectRoute, $incident)
            ->with('success', 'Lampiran bukti berhasil diunggah secara aman.');
    }

    public function download(Request $request, Incident $incident, IncidentAttachment $attachment)
    {
        // Prevent IDOR: ensure attachment actually belongs to the route's incident
        if ((int) $attachment->incident_id !== (int) $incident->id) {
            abort(404, 'Lampiran tidak ditemukan pada insiden ini.');
        }

        $user = $request->user();

        if ($user->isAdmin()) {
            return $this->serveDownload($attachment, $user);
        }

        if ((int) $incident->user_id !== (int) $user->id) {
            abort(403, 'Hak akses ditolak.');
        }

        return $this->serveDownload($attachment, $user);
    }

    protected function serveDownload(IncidentAttachment $attachment, $user)
    {
        if (! $user->isAdmin() && (int) $attachment->incident->user_id !== (int) $user->id) {
            abort(403, 'Hak akses ditolak.');
        }

        $disk = Storage::disk('private');

        if (! $disk->exists($attachment->storage_path)) {
            abort(404, 'Berkas fisik tidak ditemukan di penyimpanan.');
        }

        $mimeType = $disk->mimeType($attachment->storage_path) ?: ($attachment->mime_type ?: 'application/octet-stream');
        $originalName = $attachment->original_name;

        return response()->stream(
            function () use ($disk, $attachment) {
                $stream = $disk->readStream($attachment->storage_path);
                if (is_resource($stream)) {
                    fpassthru($stream);
                    fclose($stream);
                }
            },
            200,
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . rawurlencode($originalName) . '"',
                'Content-Length' => $disk->size($attachment->storage_path),
                'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    public function destroy(Request $request, Incident $incident, IncidentAttachment $attachment)
    {
        if ((int) $attachment->incident_id !== (int) $incident->id) {
            abort(404, 'Lampiran tidak ditemukan pada insiden ini.');
        }

        $user = $request->user();

        if ($user->isAdmin()) {
            return $this->performDelete($request, $incident, $attachment, 'admin.incidents.show');
        }

        if ((int) $incident->user_id !== (int) $user->id || (int) $attachment->uploaded_by !== (int) $user->id) {
            abort(403, 'Hak akses ditolak.');
        }

        return $this->performDelete($request, $incident, $attachment, 'user.incidents.show');
    }

    protected function performDelete(Request $request, Incident $incident, IncidentAttachment $attachment, string $redirectRoute)
    {
        $disk = Storage::disk('private');
        $storagePath = $attachment->storage_path;
        $attachmentId = $attachment->id;
        $originalName = $attachment->original_name;

        DB::transaction(function () use ($attachment) {
            $attachment->delete();
        });

        $fileDeleted = true;
        if ($storagePath && $disk->exists($storagePath)) {
            $fileDeleted = $disk->delete($storagePath);
        }

        if (! $fileDeleted) {
            Log::warning('Attachment file deletion failed after DB commit', [
                'attachment_id' => $attachmentId,
            ]);
        }

        AuditLogger::logActivity(
            'incident.attachment_deleted',
            'Incident',
            $incident->id,
            [
                'ticket_number' => $incident->ticket_number,
                'attachment_id' => $attachmentId,
                'original_name' => $originalName,
            ],
            $request
        );

        return redirect()->route($redirectRoute, $incident)
            ->with('success', 'Lampiran bukti berhasil dihapus.');
    }
}
