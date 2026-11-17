<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\IncidentCategory;
use App\Models\IncidentUpdate;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\IncidentTimelineWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IncidentsController extends Controller
{
    protected IncidentTimelineWriter $timelineWriter;

    public function __construct(IncidentTimelineWriter $timelineWriter)
    {
        $this->authorizeResource(Incident::class, 'incident');
        $this->timelineWriter = $timelineWriter;
    }

    public function index(Request $request)
    {
        $query = Incident::with(['category', 'reporter', 'assignedAdmin'])
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

        return view('admin.incidents.index', [
            'incidents' => $query,
            'categories' => $categories,
            'statuses' => $this->getStatuses(),
            'priorities' => $this->getPriorities(),
        ]);
    }

    public function show(Incident $incident)
    {
        $this->authorize('view', $incident);

        $incident->loadMissing(['category', 'reporter', 'assignedAdmin']);

        $updates = $incident->updates()
            ->with('user')
            ->latest()
            ->get();

        $attachments = $incident->attachments()
            ->with('uploader')
            ->orderBy('created_at', 'desc')
            ->get();

        $admins = User::where('role', 'admin')->where('is_active', true)->orderBy('name')->get();

        return view('admin.incidents.show', [
            'incident' => $incident,
            'updates' => $updates,
            'attachments' => $attachments,
            'categories' => IncidentCategory::where('is_active', true)->get(),
            'statuses' => $this->getStatuses(),
            'priorities' => $this->getPriorities(),
            'admins' => $admins,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Incident::class);

        $admins = User::where('role', 'admin')->where('is_active', true)->orderBy('name')->get();

        return view('admin.incidents.create', [
            'categories' => IncidentCategory::where('is_active', true)->get(),
            'priorities' => $this->getPriorities(),
            'statuses' => $this->getStatuses(),
            'admins' => $admins,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Incident::class);

        $validated = $request->validate([
            'category_id' => ['required', 'exists:incident_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'status' => ['required', 'in:pending,investigating,in_progress,resolved,rejected'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'occurred_at' => ['nullable', 'date'],
            'related_url' => ['nullable', 'url', 'max:500'],
        ], [
            'category_id.required' => 'Kategori insiden wajib dipilih.',
            'title.required' => 'Judul insiden wajib diisi.',
            'description.required' => 'Deskripsi lengkap insiden wajib diisi.',
            'priority.required' => 'Tingkat prioritas wajib dipilih.',
            'status.required' => 'Status awal insiden wajib dipilih.',
            'related_url.url' => 'Format URL terkait tidak valid (gunakan awalan http:// atau https://).',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['ticket_number'] = $this->generateUniqueTicketNumber();

        if ($validated['status'] === 'resolved') {
            $validated['resolved_at'] = now();
        }

        $incident = Incident::create($validated);

        IncidentUpdate::create([
            'incident_id' => $incident->id,
            'user_id' => $request->user()->id,
            'old_status' => null,
            'new_status' => $validated['status'],
            'message' => 'Laporan insiden baru dibuat',
            'visibility' => 'public',
        ]);

        AuditLogger::logActivity(
            'incident.created',
            'Incident',
            $incident->id,
            [
                'ticket_number' => $incident->ticket_number,
                'priority' => $incident->priority,
                'status' => $incident->status,
            ],
            $request
        );

        return redirect()->route('admin.incidents.index')
            ->with('success', 'Insiden ' . $incident->ticket_number . ' berhasil dibuat.');
    }

    public function edit(Incident $incident)
    {
        $this->authorize('update', $incident);

        $admins = User::where('role', 'admin')->where('is_active', true)->orderBy('name')->get();

        return view('admin.incidents.edit', [
            'incident' => $incident,
            'categories' => IncidentCategory::where('is_active', true)->get(),
            'priorities' => $this->getPriorities(),
            'statuses' => $this->getStatuses(),
            'admins' => $admins,
        ]);
    }

    public function update(Request $request, Incident $incident)
    {
        $this->authorize('update', $incident);

        $validated = $request->validate([
            'category_id' => ['required', 'exists:incident_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'status' => ['required', 'in:pending,investigating,in_progress,resolved,rejected'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'occurred_at' => ['nullable', 'date'],
            'related_url' => ['nullable', 'url', 'max:500'],
            'update_note' => ['nullable', 'string', 'max:1000'],
            'visibility' => ['nullable', 'in:public,internal'],
        ], [
            'category_id.required' => 'Kategori insiden wajib dipilih.',
            'title.required' => 'Judul insiden wajib diisi.',
            'description.required' => 'Deskripsi insiden wajib diisi.',
            'priority.required' => 'Tingkat prioritas wajib dipilih.',
            'status.required' => 'Status insiden wajib dipilih.',
            'related_url.url' => 'Format URL terkait tidak valid.',
        ]);

        $oldStatus = $incident->status;
        $newStatus = $validated['status'];
        $updateNote = $validated['update_note'] ?? null;
        $visibility = $validated['visibility'] ?? 'public';
        unset($validated['update_note'], $validated['visibility']);

        try {
            DB::transaction(function () use ($incident, $validated, $oldStatus, $newStatus, $updateNote, $visibility) {
                $incident->update($validated);

                if ($newStatus === 'resolved' && $oldStatus !== 'resolved') {
                    $incident->update(['resolved_at' => now()]);
                } elseif ($newStatus !== 'resolved' && $oldStatus === 'resolved') {
                    $incident->update(['resolved_at' => null]);
                }

                // Use the timeline writer service to create the IncidentUpdate record
                $this->timelineWriter->create($incident, $oldStatus, $newStatus);
            });
        } catch (\Throwable $e) {
            throw $e;
        }

        AuditLogger::logActivity(
            $oldStatus !== $newStatus ? 'incident.status_changed' : 'incident.updated',
            'Incident',
            $incident->id,
            [
                'ticket_number' => $incident->ticket_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'priority' => $incident->priority,
            ],
            $request
        );

        return redirect()->route('admin.incidents.index')
            ->with('success', 'Insiden berhasil diperbarui.');
    }

    public function destroy(Request $request, Incident $incident)
    {
        $this->authorize('delete', $incident);

        $ticketNumber = $incident->ticket_number;
        $incidentId = $incident->id;

        $incident->delete();

        AuditLogger::logActivity(
            'incident.deleted',
            'Incident',
            $incidentId,
            ['ticket_number' => $ticketNumber],
            $request
        );

        return redirect()->route('admin.incidents.index')
            ->with('success', 'Insiden berhasil dihapus.');
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
