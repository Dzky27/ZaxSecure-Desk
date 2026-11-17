<?php

namespace App\Services;

use App\Models\Incident;
use App\Models\IncidentUpdate;
use Illuminate\Http\Request;

class IncidentTimelineWriter
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function create(Incident $incident, string $oldStatus, string $newStatus): IncidentUpdate
    {
        $defaultMessage = $oldStatus === $newStatus ? 'Informasi diperbarui' : 'Status diubah';
        $customNote = $this->request ? trim((string) $this->request->input('update_note', '')) : '';
        $message = $customNote !== '' ? $customNote : $defaultMessage;

        $reqVis = $this->request ? (string) $this->request->input('visibility', 'public') : 'public';
        $visibility = in_array($reqVis, ['public', 'internal'], true) ? $reqVis : 'public';

        return IncidentUpdate::create([
            'incident_id' => $incident->id,
            'user_id' => $this->request->user()?->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'message' => $message,
            'visibility' => $visibility,
        ]);
    }
}
