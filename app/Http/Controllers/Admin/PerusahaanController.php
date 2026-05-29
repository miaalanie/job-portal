<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kategoriperusahaan;
use App\Models\Even;
use App\Models\Register;
use App\Models\PerusahaanDokumen;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\CompanyRegistrationMail;

class PerusahaanController extends Controller
{
    /**
     * Display a listing of corporate partners.
     */
    public function index()
    {
        $perusahaans = Perusahaan::with(['kategori', 'registers', 'user'])->latest()->get();
        $kategoris = Kategoriperusahaan::all();
        $evens = Even::where('statusaktif', 1)->latest()->get();
        return view('admin.perusahaan.index_data', compact('perusahaans', 'kategoris', 'evens'));
    }

    /**
     * Display the specified corporate audit profile.
     */
    public function show($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $perusahaan = Perusahaan::with([
                'kategori', 
                'registers.even', 
                'registers.lowongans.kategori',
                'user'
            ])->findOrFail($decryptedId);

            return view('admin.perusahaan.show_data', compact('perusahaan'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data perusahaan tidak ditemukan atau ID tidak valid.');
        }
    }

    /**
     * Store a newly created corporate partner.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'idkategori' => 'required|exists:kategoriperusahaans,id',
            'telp' => 'nullable|string|max:20',
            'alamatlengkap' => 'nullable|string',
            'pic' => 'nullable|string',
            'npwp' => 'nullable|string',
            'nib' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create Perusahaan
            $data = $request->only([
                'nama', 'email', 'idkategori', 'telp', 'alamatlengkap', 
                'pic', 'npwp', 'nib', 'namapimpinan', 'website', 
                'tahunberdiri', 'jumlah_karyawan', 'gambaranumum'
            ]);
            
            if ($request->hasFile('logo')) {
                $data['logo'] = $request->file('logo')->store('company_logos', 'public');
            }

            $perusahaan = Perusahaan::create(array_merge($data, [
                'idkelurahan' => $request->idkelurahan ?? 3272020003, // Default fallback (Sukabumi)
                'bentuk' => $request->bentuk ?? 'PT',
                'is_verified' => true,
                'verified_at' => now(),
                'useradd' => auth()->id(),
            ]));

            // 2. Create User account for the company
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make('password'),
                'idperusahaan' => $perusahaan->id,
                'is_active' => true,
                'statusaktif' => 1,
                'statusvalidasi' => 1,
                'activated_at' => now(),
                'useradd' => auth()->id(),
            ]);

            $user->assignRole('Admin Perusahaan');

            DB::commit();

            // 3. Send Notification Email
            try {
                Mail::to($perusahaan->email)->send(new CompanyRegistrationMail($perusahaan, $user, 'password'));
            } catch (\Exception $e) {
                // Log the error but don't fail the registration
                \Illuminate\Support\Facades\Log::error('Gagal mengirim email registrasi: ' . $e->getMessage());
            }

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Perusahaan dan akun pengguna berhasil ditambahkan!'
                ]);
            }

            return redirect()->route('admin.perusahaan-data.index')->with('success', 'Perusahaan dan akun pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal menambahkan perusahaan: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Gagal menambahkan perusahaan: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form for corporate partner.
     */
    public function edit($id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $perusahaan = Perusahaan::with(['kategori', 'dokumen'])->findOrFail($decryptedId);
            $kategoris = Kategoriperusahaan::all();
            
            return view('admin.perusahaan.edit_data', compact('perusahaan', 'kategoris'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data perusahaan tidak ditemukan.');
        }
    }

    /**
     * Update corporate partner data and documents.
     */
    public function update(Request $request, $id)
    {
        try {
            $decryptedId = Crypt::decrypt($id);
            $perusahaan = Perusahaan::findOrFail($decryptedId);

            $request->validate([
                'nama' => 'required|string|max:255',
                'idkategori' => 'required|exists:kategoriperusahaans,id',
                'email' => 'required|email|unique:users,email,' . $perusahaan->user->id,
                'logo' => 'nullable|image|max:2048',
                'dokument_names.*' => 'nullable|string',
                'dokument_files.*' => 'nullable|file|max:5120',
            ]);

            DB::beginTransaction();

            $data = $request->only([
                'nama', 'idkategori', 'alamatlengkap', 'telp', 'email', 'npwp', 'nib', 
                'website', 'jumlah_karyawan', 'tahunberdiri', 'namapimpinan', 'pic', 'gambaranumum'
            ]);

            if ($request->hasFile('logo')) {
                if ($perusahaan->logo && Storage::disk('public')->exists($perusahaan->logo)) {
                    Storage::disk('public')->delete($perusahaan->logo);
                }
                $data['logo'] = $request->file('logo')->store('company_logos', 'public');
            }

            $perusahaan->update($data);

            // Update user name/email too
            $perusahaan->user->update([
                'name' => $request->nama,
                'email' => $request->email,
            ]);

            // Handle Documents
            if ($request->has('dokument_names')) {
                foreach ($request->dokument_names as $key => $name) {
                    if ($name && $request->hasFile("dokument_files.$key")) {
                        $filePath = $request->file("dokument_files.$key")->store('company_documents', 'public');
                        
                        PerusahaanDokumen::create([
                            'idperusahaan' => $perusahaan->id,
                            'nama_dokumen' => $name,
                            'file_path' => $filePath,
                            'status' => 1, // Auto-approve by Admin
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.perusahaan-data.index')->with('success', 'Data perusahaan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Quick register company to an event.
     */
    public function registerEvent(Request $request)
    {
        $request->validate([
            'idperusahaan' => 'required|exists:perusahaans,id',
            'ideven' => 'required|exists:evens,id',
            'idpaket' => 'sometimes|nullable',
        ]);

        try {
            // Check if already registered
            $exists = Register::where('idperusahaan', $request->idperusahaan)
                             ->where('idperiode', $request->ideven)
                             ->exists();

            if ($exists) {
                if ($request->ajax()) {
                    return response()->json(['status' => 'error', 'message' => 'Perusahaan sudah terdaftar pada event ini.'], 422);
                }
                return redirect()->back()->with('warning', 'Perusahaan sudah terdaftar pada event ini.');
            }

            $even = Even::findOrFail($request->ideven);
            $namapaket = "0";

            if ($even->statuspaket == 1) {
                $namapaket = $request->idpaket ?? "0";
            }

            Register::create([
                'idperusahaan' => $request->idperusahaan,
                'idperiode' => $request->ideven,
                'namapaket' => $namapaket,
                'biaya' => $even->biaya ?? 0,
                'tanggalregister' => now(),
                'aktivasi' => 1, // Admin registration is auto-active
                'useradd' => auth()->id(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Perusahaan berhasil didaftarkan ke event: ' . $even->namaperiode
                ]);
            }

            return redirect()->back()->with('success', 'Perusahaan berhasil didaftarkan ke event.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal mendaftarkan ke event: ' . $e->getMessage());
        }
    }

    /**
     * Get active events the company has not registered for yet.
     */
    public function getAvailableEvents($idperusahaan)
    {
        $user = auth()->user();
        
        $query = Even::where('statusaktif', 1);

        // Filter by role if Admin Event
        if ($user->hasRole('Admin Event')) {
            $query->where('useradd', $user->id);
        }

        // Exclude events already registered by this company
        $registeredEventIds = Register::where('idperusahaan', $idperusahaan)->pluck('idperiode')->toArray();
        $query->whereNotIn('id', $registeredEventIds);

        $events = $query->with('pakets')->orderBy('tanggalawal', 'DESC')->get(['id', 'namaperiode', 'tanggalawal', 'statuspaket']);

        return response()->json($events);
    }
}
