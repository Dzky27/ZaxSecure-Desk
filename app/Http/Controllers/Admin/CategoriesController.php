<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncidentCategory;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $categories = IncidentCategory::withCount('incidents')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim((string) $request->input('search'));
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:incident_categories,name'],
            'description' => ['required', 'string', 'min:8', 'max:1000'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.',
            'description.required' => 'Deskripsi kategori wajib diisi.',
            'description.min' => 'Deskripsi kategori minimal 8 karakter.',
        ]);

        $slug = Str::slug($validated['name']);
        $baseSlug = $slug;
        $counter = 1;
        while (IncidentCategory::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $category = IncidentCategory::create([
            'name' => trim($validated['name']),
            'slug' => $slug,
            'description' => trim($validated['description']),
            'is_active' => true,
        ]);

        AuditLogger::logActivity(
            'category.created',
            'IncidentCategory',
            $category->id,
            ['name' => $category->name, 'slug' => $category->slug],
            $request
        );

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori "' . $category->name . '" berhasil ditambahkan.');
    }

    public function update(Request $request, IncidentCategory $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', Rule::unique('incident_categories', 'name')->ignore($category->id)],
            'description' => ['required', 'string', 'min:8', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Nama kategori sudah digunakan.',
            'description.required' => 'Deskripsi kategori wajib diisi.',
        ]);

        $category->update([
            'name' => trim($validated['name']),
            'description' => trim($validated['description']),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $category->is_active,
        ]);

        AuditLogger::logActivity(
            'category.updated',
            'IncidentCategory',
            $category->id,
            ['name' => $category->name, 'is_active' => $category->is_active],
            $request
        );

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori "' . $category->name . '" berhasil diperbarui.');
    }

    public function toggleStatus(Request $request, IncidentCategory $category)
    {
        $category->update(['is_active' => ! $category->is_active]);

        AuditLogger::logActivity(
            $category->is_active ? 'category.activated' : 'category.deactivated',
            'IncidentCategory',
            $category->id,
            ['name' => $category->name, 'is_active' => $category->is_active],
            $request
        );

        return redirect()->route('admin.categories.index')
            ->with('success', 'Status kategori "' . $category->name . '" diubah menjadi ' . ($category->is_active ? 'Aktif' : 'Nonaktif') . '.');
    }

    public function destroy(Request $request, IncidentCategory $category)
    {
        if ($category->incidents()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Kategori "' . $category->name . '" sedang digunakan oleh laporan insiden dan tidak dapat dihapus. Nonaktifkan kategori jika tidak ingin digunakan lagi.');
        }

        $name = $category->name;
        $id = $category->id;
        $category->delete();

        AuditLogger::logActivity(
            'category.deleted',
            'IncidentCategory',
            $id,
            ['name' => $name],
            $request
        );

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori "' . $name . '" berhasil dihapus.');
    }
}
