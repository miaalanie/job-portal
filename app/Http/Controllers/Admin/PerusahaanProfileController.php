<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Perusahaan;
use App\Models\PerusahaanDokumen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PerusahaanProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $perusahaan = $user->perusahaan;

        if (!$perusahaan) {
            return redirect()->route('admin.dashboard')->with('error', 'Data perusahaan tidak ditemukan.');
        }

        return view('admin.perusahaan.profile', compact('perusahaan'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $perusahaan = $user->perusahaan;

        $request->validate([
            'nama' => 'required',
            'alamatlengkap' => 'required',
            'telp' => 'required',
            'email' => 'required|email',
            'npwp' => 'nullable',
            'nib' => 'nullable',
            'website' => 'nullable',
            'tahunberdiri' => 'nullable',
            'gambaranumum' => 'nullable',
            'logo' => 'nullable|image|max:2048',
            'dokument_names.*' => 'nullable|string',
            'dokument_files.*' => 'nullable|file|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['_token', 'logo', 'dokument_names', 'dokument_files']);
            
            if ($request->hasFile('logo')) {
                if ($perusahaan->logo && Storage::disk('public')->exists($perusahaan->logo)) {
                    Storage::disk('public')->delete($perusahaan->logo);
                }
                $data['logo'] = $request->file('logo')->store('company_logos', 'public');
            }

            $perusahaan->update($data);

            if ($request->has('dokument_names')) {
                foreach ($request->dokument_names as $key => $name) {
                    if ($name && $request->hasFile("dokument_files.$key")) {
                        $filePath = $request->file("dokument_files.$key")->store('company_documents', 'public');
                        
                        PerusahaanDokumen::create([
                            'idperusahaan' => $perusahaan->id,
                            'nama_dokumen' => $name,
                            'file_path' => $filePath,
                            'status' => 0,
                        ]);
                    }
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Profil dan dokumen berhasil diperbarui!'
                ]);
            }

            return redirect()->back()->with('success', 'Profil perusahaan dan dokumen berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui profil: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }

    public function deleteDocument($id)
    {
        try {
            $decryptedId = \Illuminate\Support\Facades\Crypt::decrypt($id);
            $doc = PerusahaanDokumen::findOrFail($decryptedId);
            
            // Check ownership
            if ($doc->idperusahaan != Auth::user()->perusahaan->id) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
            }

            if (Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }

            $doc->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Dokumen berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus dokumen: ' . $e->getMessage()
            ], 500);
        }
    }
}
