<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\IncidentCategory;
use App\Models\IncidentUpdate;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IncidentsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Incident::class);

        $query = Incident::where('user_id', $request->user()->id)
            ->with(['category', 'reporter', 'assignedAdmin'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim((string) $request->input('search'));
                $q->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('ticket_number', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $status = $request->input('status');
                if ($status === 'pending') {
                    $q->whereIn('status', ['pending', 'Pending']);
                } elseif ($status === 'in_progress') {
                    $q->whereIn('status', ['in_progress', 'Diproses']);
                } elseif ($status === 'resolved') {
                    $q->whereIn('status', ['resolved', 'Selesai']);
                } else {
                    $q->where('status', $status);
                }
            })
            ->when($request->filled('priority'), function ($q) use ($request) {
                $q->where('priority', $request->input('priority'));
            })
            ->when($request->filled('category_id'), function ($q) use ($request) {
                $q->where('category_id', $request->input('category_id'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $categories = IncidentCategory::where('is_active', true)->get();

        return view('user.incidents.index', [
            'incidents' => $query,
            'categories' => $categories,
            'statuses' => $this->getStatuses(),
            'priorities' => $this->getPriorities(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Incident::class);

        return view('user.incidents.create', [
            'categories' => IncidentCategory::where('is_active', true)->get(),
            'priorities' => $this->getPriorities(),
            'statuses' => $this->getStatuses(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Incident::class);

        $validated = $request->validate([
            'category_id' => ['required', 'exists:incident_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:500'],
            'occurred_at' => ['nullable', 'date'],
            'related_url' => ['nullable', 'url', 'max:500'],
        ], [
            'category_id.required' => 'Kategori insiden wajib dipilih.',
            'title.required' => 'Judul laporan insiden wajib diisi.',
            'description.required' => 'Deskripsi kejadian wajib diisi.',
            'description.max' => 'Deskripsi laporan maksimal 500 karakter.',
            'related_url.url' => 'Format URL terkait tidak valid (gunakan awalan http:// atau https://).',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['ticket_number'] = $this->generateUniqueTicketNumber();
        $validated['priority'] = 'low';
        $validated['status'] = 'pending';

        $incident = Incident::create($validated);

        IncidentUpdate::create([
            'incident_id' => $incident->id,
            'user_id' => $request->user()->id,
            'old_status' => null,
            'new_status' => 'pending',
            'message' => 'Laporan insiden baru dibuat',
            'visibility' => 'public',
        ]);

        AuditLogger::logActivity(
            'incident.reported',
            'Incident',
            $incident->id,
            ['ticket_number' => $incident->ticket_number],
            $request
        );

        return redirect()->route('user.incidents.index')
            ->with('success', 'Laporan insiden berhasil dikirim.');
    }

    public function show(Incident $incident, Request $request)
    {
        $this->authorize('view', $incident);

        $incident->loadMissing(['category', 'reporter', 'assignedAdmin']);

        $updates = $incident->updates()
            ->with('user')
            ->latest()
            ->get();

        // Filter timeline entries - users cannot see internal entries (except admins)
        $currentUser = $request->user();
        if (! $currentUser->isAdmin()) {
            $updates = $updates->filter(function ($update) {
                return $update->visibility !== 'internal';
            });
        }

        $attachments = $incident->attachments()
            ->with('uploader')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.incidents.show', [
            'incident' => $incident,
            'updates' => $updates,
            'attachments' => $attachments,
            'user' => $currentUser,
        ]);
    }

    public function edit(Incident $incident)
    {
        $this->authorize('update', $incident);

        return view('user.incidents.edit', [
            'incident' => $incident,
            'categories' => IncidentCategory::where('is_active', true)->get(),
            'priorities' => $this->getPriorities(),
            'statuses' => $this->getStatuses(),
        ]);
    }

    public function update(Request $request, Incident $incident)
    {
        $this->authorize('update', $incident);

        $validated = $request->validate([
            'category_id' => ['required', 'exists:incident_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:500'],
            'occurred_at' => ['nullable', 'date'],
            'related_url' => ['nullable', 'url', 'max:500'],
        ], [
            'category_id.required' => 'Kategori insiden wajib dipilih.',
            'title.required' => 'Judul laporan insiden wajib diisi.',
            'description.required' => 'Deskripsi kejadian wajib diisi.',
            'description.max' => 'Deskripsi laporan maksimal 500 karakter.',
            'related_url.url' => 'Format URL terkait tidak valid.',
        ]);

        $oldStatus = $incident->status;

        $incident->update($validated);

        $visible = ($request->user()->isAdmin()) ? 'public' : 'internal';

        IncidentUpdate::create([
            'incident_id' => $incident->id,
            'user_id' => $request->user()->id,
            'old_status' => $oldStatus,
            'new_status' => $incident->status,
            'message' => $oldStatus === $incident->status ? 'Informasi diperbarui' : 'Status diubah',
            'visibility' => $visible,
        ]);

        AuditLogger::logActivity(
            'incident.user_updated',
            'Incident',
            $incident->id,
            ['ticket_number' => $incident->ticket_number],
            $request
        );

        return redirect()->route('user.incidents.index')
            ->with('success', 'Laporan insiden berhasil diperbarui.');
    }

    public function destroy(Incident $incident)
    {
        $this->authorize('delete', $incident);

        $incident->delete();

        return redirect()->route('user.incidents.index')
            ->with('success', 'Laporan insiden berhasil dihapus.');
    }

    protected function generateUniqueTicketNumber(): string
    {
        do {
            $ticket = 'INC-' . strtoupper(Str::random(8));
        } while (Incident::where('ticket_number', $ticket)->exists());

        return $ticket;
    }

    protected function getPriorities(): array
    {
        return [
            'low' => 'Rendah (Low)',
            'medium' => 'Sedang (Medium)',
            'high' => 'Tinggi (High)',
            'critical' => 'Kritis (Critical)',
        ];
    }

    protected function getStatuses(): array
    {
        return [
            'pending' => 'Menunggu Tinjauan (Open)',
            'investigating' => 'Dalam Investigasi (In Progress)',
            'in_progress' => 'Sedang Ditangani (Diproses)',
            'resolved' => 'Selesai Ditangani (Resolved)',
            'rejected' => 'Ditolak / Ditutup (Closed)',
        ];
    }
}
