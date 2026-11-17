@extends('layouts.admin')

@section('title', 'Detail Insiden ' . $incident->ticket_number . ' — ZaxSecure Desk')

@section('page-content')
<div class="page-head">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
            <a href="{{ route('admin.incidents.index') }}" class="hover:text-white">&larr; Daftar Insiden</a>
            <span>/</span>
            <span class="mono text-red-400">{{ $incident->ticket_number }}</span>
        </div>
        <h1>{{ $incident->title }}</h1>
    </div>
    <div class="actions">
        <a href="{{ route('admin.incidents.edit', $incident) }}" class="btn btn-primary btn-sm">
            <i data-lucide="edit-3"></i>
            <span>Ubah Status & Triase</span>
        </a>
        <form action="{{ route('admin.incidents.destroy', $incident) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan insiden {{ $incident->ticket_number }} secara permanen?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                <i data-lucide="trash-2"></i>
                <span>Hapus Insiden</span>
            </button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" role="status">
        <i data-lucide="check-circle-2"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-error" role="alert">
        <i data-lucide="alert-triangle"></i>
        <div>
            @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    </div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">
    <!-- Left Column: Incident Information, Description, Evidence & Timeline -->
    <div class="xl:col-span-2 space-y-6">
        <!-- Main Details Card -->
        <section class="glass card">
            <div class="card-h">
                <h2><i data-lucide="shield-alert"></i>Informasi & Deskripsi Insiden</h2>
                <div class="flex items-center gap-2">
                    @include('components.priority-badge', ['priority' => $incident->normalized_priority, 'text' => $incident->priority_label])
                    @include('components.status-badge', ['status' => $incident->normalized_status, 'text' => $incident->status_label])
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-white/[0.02] border border-white/10 mb-5 text-xs">
                <div>
                    <span class="text-slate-400 block mb-1">Nomor Tiket</span>
                    <strong class="mono text-white text-sm">{{ $incident->ticket_number }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block mb-1">Kategori Ancaman</span>
                    <strong class="text-white">{{ $incident->category->name ?? '—' }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block mb-1">Waktu Kejadian</span>
                    <strong class="text-white">{{ $incident->occurred_at ? $incident->occurred_at->format('d M Y') : 'Tidak disebutkan' }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block mb-1">Waktu Dilaporkan</span>
                    <strong class="text-white">{{ $incident->created_at->format('d M Y H:i') }}</strong>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Kronologi / Deskripsi Kejadian</h3>
                    <div class="p-4 rounded-xl bg-[#090d16] border border-white/10 text-slate-200 text-sm whitespace-pre-line leading-relaxed">{{ $incident->description }}</div>
                </div>

                @if($incident->related_url)
                    <div>
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">URL / Sistem Terkait</h3>
                        <div class="p-3 rounded-lg bg-red-500/10 border border-red-500/25 flex items-center justify-between gap-3">
                            <span class="mono text-xs text-red-300 break-all">{{ $incident->related_url }}</span>
                            <span class="b b-pri-high shrink-0">Hati-hati Tautan Eksternal</span>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Evidence Attachments Card -->
        <section class="glass card">
            <div class="card-h">
                <h2><i data-lucide="paperclip"></i>Lampiran Bukti Forensik ({{ $attachments ? $attachments->count() : 0 }})</h2>
                <span class="text-xs text-slate-400">Penyimpanan Privat Terisolasi</span>
            </div>

            <form action="{{ route('admin.incidents.attachments.store', $incident) }}" method="POST" enctype="multipart/form-data" class="p-4 rounded-xl bg-white/[0.02] border border-white/10 mb-5">
                @csrf
                <label class="label text-xs mb-2" for="evidenceFile">
                    <span>Unggah Berkas Bukti Baru (PDF, JPG, PNG, WEBP, TXT — Maks. 10 MB)</span>
                </label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="file" id="evidenceFile" name="file" required accept=".pdf,.jpg,.jpeg,.png,.webp,.txt" class="input sm flex-1">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i data-lucide="upload"></i>
                        <span>Unggah Bukti</span>
                    </button>
                </div>
                @error('file')
                    <p class="field-error mt-2">{{ $message }}</p>
                @enderror
            </form>

            @if($attachments && $attachments->count() > 0)
                <div class="space-y-3">
                    @foreach($attachments as $attachment)
                        <div class="p-3.5 rounded-xl bg-[#090d16] border border-white/10 flex flex-wrap items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="file-check-2" class="text-red-400 shrink-0" style="width:16px;height:16px"></i>
                                    <span class="mono text-xs font-semibold text-white truncate" title="{{ $attachment->original_name }}">{{ $attachment->original_name }}</span>
                                </div>
                                <div class="text-[11.5px] text-slate-400 mt-1">
                                    Tipe: {{ $attachment->mime_type }} • Ukuran: {{ max(1, round($attachment->file_size / 1024)) }} KB • Diunggah: {{ $attachment->created_at->format('d M Y H:i') }}
                                    @if($attachment->uploader)
                                        oleh <strong class="text-slate-300">{{ $attachment->uploader->name }}</strong>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.incidents.attachments.download', [$incident, $attachment]) }}" class="btn btn-ghost btn-sm">
                                    <i data-lucide="download"></i>
                                    <span>Unduh</span>
                                </a>
                                <form action="{{ route('admin.incidents.attachments.destroy', [$incident, $attachment]) }}" method="POST" onsubmit="return confirm('Hapus lampiran bukti {{ $attachment->original_name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i data-lucide="trash-2"></i>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-6 text-center text-xs text-slate-400">
                    Belum ada lampiran bukti digital untuk laporan insiden ini.
                </div>
            @endif
        </section>

        <!-- Incident Timeline & Activity History -->
        <section class="glass card">
            <div class="card-h">
                <h2><i data-lucide="history"></i>Linimasa Penanganan & Riwayat Aktivitas</h2>
            </div>

            @if($updates && $updates->count() > 0)
                <div class="space-y-3">
                    @foreach($updates as $update)
                        <div class="p-4 rounded-xl bg-[#090d16] border border-white/10 border-l-4 {{ $update->visibility === 'internal' ? 'border-l-amber-500' : 'border-l-red-500' }}">
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                                <div class="flex items-center gap-2 text-xs">
                                    <strong class="text-white">{{ $update->user->name ?? 'Sistem' }}</strong>
                                    <span class="text-slate-500">•</span>
                                    <span class="mono text-slate-400">{{ $update->created_at->format('d M Y H:i') }}</span>
                                    @if($update->visibility === 'internal')
                                        <span class="b b-st-pending">Internal SOC</span>
                                    @else
                                        <span class="b b-st-in_progress">Publik</span>
                                    @endif
                                </div>
                                <span class="mono text-[11px] px-2 py-0.5 rounded bg-white/[0.05] text-slate-300">
                                    {{ $update->old_status ?? 'Awal' }} &rarr; {{ $update->new_status ?? '-' }}
                                </span>
                            </div>
                            <p class="text-sm text-slate-200">{{ $update->message }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 py-4 text-center">Belum ada catatan aktivitas pada linimasa ini.</p>
            @endif
        </section>
    </div>

    <!-- Right Column: Reporter Info, Assignment & Quick Status Update -->
    <div class="space-y-6">
        <!-- Reporter & Assignment Metadata -->
        <section class="glass card">
            <div class="card-h">
                <h2><i data-lucide="users"></i>Personil Terkait</h2>
            </div>
            <div class="space-y-4 text-xs">
                <div class="p-3.5 rounded-xl bg-white/[0.02] border border-white/10">
                    <span class="text-slate-400 block mb-1">Pelapor Insiden</span>
                    <div class="font-semibold text-white text-sm">{{ $incident->reporter->name ?? 'Tidak diketahui' }}</div>
                    <div class="text-slate-400 mono mt-0.5">{{ $incident->reporter->email ?? '—' }}</div>
                </div>

                <div class="p-3.5 rounded-xl bg-white/[0.02] border border-white/10">
                    <span class="text-slate-400 block mb-1">Analis / Admin Penanggung Jawab</span>
                    @if($incident->assignedAdmin)
                        <div class="font-semibold text-white text-sm">{{ $incident->assignedAdmin->name }}</div>
                        <div class="text-slate-400 mono mt-0.5">{{ $incident->assignedAdmin->email }}</div>
                    @else
                        <div class="text-amber-400 font-medium">Belum ada analis yang ditugaskan</div>
                    @endif
                </div>

                @if($incident->resolved_at)
                    <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/25">
                        <span class="text-emerald-300 block mb-1">Diselesaikan Pada</span>
                        <div class="font-semibold text-white mono">{{ $incident->resolved_at->format('d M Y H:i') }}</div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Quick Triage & Status Update Form -->
        <section class="glass card">
            <div class="card-h">
                <h2><i data-lucide="sliders"></i>Pembaruan Triase Cepat</h2>
            </div>
            <form method="POST" action="{{ route('admin.incidents.update', $incident) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="category_id" value="{{ $incident->category_id }}">
                <input type="hidden" name="title" value="{{ $incident->title }}">
                <input type="hidden" name="description" value="{{ $incident->description }}">
                <input type="hidden" name="occurred_at" value="{{ $incident->occurred_at ? $incident->occurred_at->format('Y-m-d') : '' }}">
                <input type="hidden" name="related_url" value="{{ $incident->related_url }}">

                <div class="field">
                    <label class="label text-xs" for="quick_status">Status Penanganan</label>
                    <select id="quick_status" name="status" class="select sm" required>
                        @foreach($statuses ?? ['pending' => 'Menunggu', 'investigating' => 'Investigasi', 'in_progress' => 'Diproses', 'resolved' => 'Selesai', 'rejected' => 'Ditolak'] as $val => $lbl)
                            <option value="{{ $val }}" {{ $incident->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label class="label text-xs" for="quick_priority">Tingkat Prioritas</label>
                    <select id="quick_priority" name="priority" class="select sm" required>
                        @foreach($priorities ?? ['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi', 'critical' => 'Kritis'] as $val => $lbl)
                            <option value="{{ $val }}" {{ $incident->priority === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label class="label text-xs" for="quick_assigned">Tugaskan Analis SOC</label>
                    <select id="quick_assigned" name="assigned_to" class="select sm">
                        <option value="">-- Belum Ditugaskan --</option>
                        @foreach($admins ?? [] as $adm)
                            <option value="{{ $adm->id }}" {{ (int) $incident->assigned_to === (int) $adm->id ? 'selected' : '' }}>
                                {{ $adm->name }} ({{ $adm->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label class="label text-xs" for="update_note">
                        <span>Catatan Linimasa</span>
                        <span class="opt">Opsional</span>
                    </label>
                    <textarea id="update_note" name="update_note" rows="3" class="textarea text-xs" placeholder="Tulis temuan investigasi atau tindakan mitigasi..."></textarea>
                </div>

                <div class="field">
                    <label class="label text-xs" for="visibility">Visibilitas Catatan</label>
                    <select id="visibility" name="visibility" class="select sm">
                        <option value="public">Publik (Terlihat oleh Pelapor)</option>
                        <option value="internal">Internal SOC (Hanya Admin)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i data-lucide="save"></i>
                    <span>Simpan Pembaruan Triase</span>
                </button>
            </form>
        </section>
    </div>
</div>
@endsection
