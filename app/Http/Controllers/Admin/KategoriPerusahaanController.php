<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategoriperusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KategoriPerusahaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Kategoriperusahaan::latest()->get();
        return view('admin.kategori_perusahaan.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|unique:kategoriperusahaans,nama|max:255',
        ], [
            'nama.unique' => 'Kategori ini sudah terdaftar.',
        ]);

        try {
            Kategoriperusahaan::create([
                'nama' => $request->nama,
                'useradd' => Auth::id(),
            ]);

            return back()->with('success', 'Kategori perusahaan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|max:255|unique:kategoriperusahaans,nama,' . $id,
        ], [
            'nama.unique' => 'Kategori ini sudah terdaftar.',
        ]);

        try {
            $category = Kategoriperusahaan::findOrFail($id);
            $category->update([
                'nama' => $request->nama,
                'userupdate' => Auth::id(),
            ]);

            return back()->with('success', 'Kategori perusahaan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $category = Kategoriperusahaan::findOrFail($id);
            
            // Check if still used by companies
            if ($category->perusahaans()->count() > 0) {
                return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh perusahaan.');
            }

            $category->delete();
            return back()->with('success', 'Kategori perusahaan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
}
