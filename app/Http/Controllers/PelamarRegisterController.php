<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pelamar;
use App\Models\Provinsi;
use App\Models\Kota;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Mail\UserActivationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PelamarRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('frontend.pelamar_register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ], [
            'email.unique' => 'Email ini sudah terdaftar di sistem kami.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal harus 8 karakter.',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'activation_token' => Str::random(64),
                'is_active' => false,
                'statusaktif' => 0,
            ]);

            // Role ID 4 is Pelamar
            $user->assignRole('Pelamar');

            // Send Activation Email
            Mail::to($user->email)->send(new UserActivationMail(
                $user,
                $request->name,
                $request->name,
                $user->email,
                $request->password
            ));

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Registrasi berhasil! Silakan cek email Anda untuk aktivasi akun.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Applicant Registration Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat mendaftarkan akun. Mohon hubungi admin.'
            ], 500);
        }
    }

    public function showCompleteDataForm()
    {
        $user = auth()->user();
        $pelamar = $user->pelamar()->with(['pendidikans', 'pengalamans', 'dokumens', 'skills'])->first();
        $provinsis = Provinsi::all();

        return view('frontend.pelamar_complete_data', compact('provinsis', 'pelamar'));
    }

    public function storeCompleteData(Request $request)
    {
        $user = auth()->user();
        $idPelamar = $user->idpelamar;

        $request->validate([
            // Identitas
            'noktp' => 'required|string|size:16|unique:pelamars,noktp,' . ($idPelamar ?? 'NULL'),
            'namalengkap' => 'required|string|max:255',
            'nohp' => 'required|string|max:20',
            'alamatlengkap' => 'required|string',
            'idkelurahan' => 'required|exists:kelurahans,id',
            'tempatlahir' => 'required|string|max:255',
            'tanggallahir' => 'required|date',
            'jeniskelamin' => 'required|in:Laki-laki,Perempuan',
            'tinggibadan' => 'nullable|integer',
            'beratbadan' => 'nullable|integer',
            'foto_profil' => ($idPelamar ? 'nullable' : 'required') . '|image|mimes:jpg,jpeg,png|max:1024',

            // Pendidikan (Arrays)
            'edu_kategori.*' => 'required|string',
            'edu_nama.*' => 'required|string',
            'edu_awal.*' => 'required|integer',
            'edu_akhir.*' => 'required|integer',

            // Keahlian/Skills (Arrays)
            'skill_nama.*' => 'required|string|max:255',
            'skill_ket.*' => 'required|in:Kurang,Cukup,Baik,Sangat Baik',

            // Pengalaman (Arrays) - Conditional
            'exp_nama.*' => 'required_without:no_experience|nullable|string',
            'exp_posisi.*' => 'required_without:no_experience|nullable|string',
            'exp_awal.*' => 'required_without:no_experience|nullable|integer',
            'exp_bulan_awal.*' => 'required_without:no_experience|nullable|integer|min:1|max:12',
            'exp_bulan_akhir.*' => 'nullable|integer|min:1|max:12',

            // Dokumen
            'doc_file_ktp' => ($idPelamar ? 'nullable' : 'required') . '|mimes:pdf,jpg,jpeg,png|max:2048',
            'doc_file_kuning' => ($idPelamar ? 'nullable' : 'required') . '|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'noktp.unique' => 'Nomor KTP ini sudah terdaftar di akun lain.',
            'doc_file_ktp.required' => 'File KTP wajib diunggah.',
            'doc_file_kuning.required' => 'File Kartu Kuning (AK-1) wajib diunggah.',
            'exp_nama.*.required_without' => 'Nama perusahaan wajib diisi jika Anda memiliki pengalaman.',
            'exp_posisi.*.required_without' => 'Posisi wajib diisi jika Anda memiliki pengalaman.',
            'exp_awal.*.required_without' => 'Tahun masuk wajib diisi jika Anda memiliki pengalaman.',
        ]);

        try {
            DB::beginTransaction();

            // 1. Save Base Pelamar
            $pelamarData = [
                'noktp' => $request->noktp,
                'namalengkap' => $request->namalengkap,
                'nohp' => $request->nohp,
                'alamatlengkap' => $request->alamatlengkap,
                'idkelurahan' => $request->idkelurahan,
                'tempatlahir' => $request->tempatlahir,
                'tanggallahir' => $request->tanggallahir,
                'jeniskelamin' => $request->jeniskelamin,
                'tinggibadan' => $request->tinggibadan,
                'beratbadan' => $request->beratbadan,
                'userupdate' => auth()->id(),
            ];

            if ($request->hasFile('foto_profil')) {
                $pelamarData['foto'] = $request->file('foto_profil')->store('pelamar/foto', 'public');
            }

            if (!$idPelamar) {
                $pelamarData['useradd'] = auth()->id();
            }

            $pelamar = Pelamar::updateOrCreate(
                ['id' => $idPelamar],
                $pelamarData
            );

            // Clear old Pendidikan, Pengalaman, & Skills if updating
            if ($idPelamar) {
                $pelamar->pendidikans()->delete();
                $pelamar->pengalamans()->delete();
                $pelamar->skills()->delete();
            }

            // 2. Save Education
            if ($request->has('edu_kategori')) {
                foreach ($request->edu_kategori as $index => $kat) {
                    \App\Models\Pelamarpendidikan::create([
                        'idpelamar' => $pelamar->id,
                        'kategori' => $kat,
                        'namasekolah' => $request->edu_nama[$index],
                        'jurusan' => $request->edu_jurusan[$index] ?? null,
                        'tahunawal' => $request->edu_awal[$index],
                        'tahunselesai' => $request->edu_akhir[$index],
                        'useradd' => auth()->id(),
                    ]);
                }
            }

            // 3. Save Skills
            if ($request->has('skill_nama')) {
                foreach ($request->skill_nama as $index => $nama) {
                    \App\Models\Pelamarskill::create([
                        'idpelamar' => $pelamar->id,
                        'namaskill' => $nama,
                        'keterangan' => $request->skill_ket[$index],
                        'useradd' => auth()->id(),
                    ]);
                }
            }

            // 4. Save Experience
            if (!$request->has('no_experience') && $request->has('exp_nama')) {
                foreach ($request->exp_nama as $index => $nama) {
                    if (empty($nama)) continue;
                    $aktif = (int)($request->exp_aktif[$index] ?? 0);

                    \App\Models\Pelamarpengalaman::create([
                        'idpelamar' => $pelamar->id,
                        'namaperusahaan' => $nama,
                        'posisi' => $request->exp_posisi[$index],
                        'tahunawal' => $request->exp_awal[$index],
                        'bulanawal'      => $request->exp_bulan_awal[$index] ?? 0,
                        'tahunselesai' => $request->exp_akhir[$index] ?? null,
                        'bulanselesai' => (int)($request->exp_bulan_akhir[$index] ?? 0),
                        'aktif'        => $aktif,
                        'useradd' => auth()->id(),
                    ]);
                }
            }

            // 4. Save Mandatory Documents
            $mandatoryDocs = [
                'doc_file_ktp' => 'KTP',
                'doc_file_kuning' => 'Kartu Kuning (AK-1)'
            ];

            foreach ($mandatoryDocs as $inputName => $docName) {
                if ($request->hasFile($inputName)) {
                    $path = $request->file($inputName)->store('pelamar/docs', 'public');
                    \App\Models\Pelamardokumen::create([
                        'idpelamar' => $pelamar->id,
                        'namadokumen' => $docName,
                        'filedokumen' => $path,
                        'useradd' => auth()->id(),
                    ]);
                }
            }

            // 5. Save Additional Documents
            if ($request->has('doc_name') && $request->hasFile('doc_file')) {
                foreach ($request->doc_name as $index => $name) {
                    if (isset($request->file('doc_file')[$index])) {
                        $path = $request->file('doc_file')[$index]->store('pelamar/docs', 'public');
                        \App\Models\Pelamardokumen::create([
                            'idpelamar' => $pelamar->id,
                            'namadokumen' => $name,
                            'filedokumen' => $path,
                            'useradd' => auth()->id(),
                        ]);
                    }
                }
            }

            auth()->user()->update(['idpelamar' => $pelamar->id]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Profil berhasil divalidasi dan dilengkapi! Akses pencarian kerja telah aktif.',
                'redirect' => route('pelamar.dashboard')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Applicant Portfolio Fulfillment Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses portofolio Anda: ' . $e->getMessage()
            ], 500);
        }
    }

    public function activate($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Token aktivasi tidak valid.');
        }

        $user->update([
            'activation_token' => null,
            'is_active' => true,
            'statusaktif' => 1,
            'activated_at' => Carbon::now()
        ]);

        return redirect()->route('login')->with('success', 'Akun berhasil diaktifkan! Silakan login untuk melengkapi profil Anda.');
    }
}
