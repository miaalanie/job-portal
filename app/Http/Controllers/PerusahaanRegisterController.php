<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Perusahaan;
use App\Models\Kategoriperusahaan;
use App\Models\Provinsi;
use App\Models\Kota;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Mail\UserActivationMail;
use App\Models\PengaturanPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PerusahaanRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        $kategoriPerusahaans = Kategoriperusahaan::all();
        $provinsis = Provinsi::all();
        return view('frontend.perusahaan_register', compact('kategoriPerusahaans', 'provinsis'));
    }

    public function registrationSuccess()
    {
        return view('frontend.register_success');
    }

    public function register(Request $request)
    {
        $request->validate([
            'namaperusahaan' => 'required|string|max:255',
            'email_perusahaan' => 'required|email|unique:perusahaans,email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'telp' => 'required|string|max:20',
            'idkategori' => 'required|exists:kategoriperusahaans,id',
            'alamatlengkap' => 'required|string',
            'idkelurahan' => 'required|exists:kelurahans,id',
            'npwp' => 'required|string|max:30',
            'nib' => 'required|string|max:30',
            'pic' => 'required|string|max:255',
            'namapimpinan' => 'required|string|max:255',
        ], [
            'email_perusahaan.unique' => 'Email ini sudah terdaftar di sistem kami.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'idkelurahan.required' => 'Wilayah alamat (Kelurahan) wajib dipilih.'
        ]);

        try {
            DB::beginTransaction();

            $perusahaan = Perusahaan::create([
                'nama' => $request->namaperusahaan,
                'alamatlengkap' => $request->alamatlengkap,
                'idkelurahan' => $request->idkelurahan,
                'idkategori' => $request->idkategori,
                'bentuk' => $request->bentuk ?? 'PT',
                'telp' => $request->telp,
                'email' => $request->email_perusahaan,
                'npwp' => $request->npwp,
                'nib' => $request->nib,
                'pic' => $request->pic,
                'namapimpinan' => $request->namapimpinan,
                'is_verified' => false,
            ]);

            $user = User::create([
                'name' => $request->pic,
                'email' => $request->email_perusahaan,
                'password' => Hash::make($request->password),
                'idperusahaan' => $perusahaan->id,
                'activation_token' => Str::random(64),
                'is_active' => false,
                'statusaktif' => 0,
                'statusvalidasi' => 0,
            ]);

            // Assign Roles
            try {
                $user->assignRole('Admin Perusahaan');
            } catch (\Exception $e) {
                // Optional: Fallback to 'Perusahaan' if 'Admin Perusahaan' fails
                \Illuminate\Support\Facades\Log::warning('Role Admin Perusahaan failed, trying Perusahaan: ' . $e->getMessage());
                $user->assignRole('Perusahaan');
            }

            // Send Activation Email
            Mail::to($user->email)->send(new UserActivationMail(
                $user, 
                $request->namaperusahaan, 
                $request->pic, 
                $user->email, 
                $request->password
            ));

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Registrasi berhasil! Silakan cek email Anda untuk aktivasi akun.'
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Registration DB Error: ' . $e->getMessage());
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email atau data unik lainnya sudah digunakan.'
                ], 422);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada database. Silakan coba lagi nanti.'
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Registration Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat mendaftarkan akun. Mohon hubungi admin.'
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

        // Create notification for Admin Aplikasi
        DB::table('system_notifications')->insert([
            'type' => 'company_activation',
            'title' => 'Perusahaan Baru Terverifikasi',
            'message' => 'Perusahaan "' . ($user->perusahaan->nama_perusahaan ?? 'Unknown') . '" telah melakukan aktivasi email.',
            'url' => route('admin.users.index'), // Or dedicated company verification page
            'is_read' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('login')->with('success', 'Akun berhasil diaktifkan! Silakan login untuk melengkapi dokumen legalitas.');
    }

    public function getKategoris(Request $request)
    {
        $query = Kategoriperusahaan::query();
        if ($request->has('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%');
        }
        $data = $query->limit(20)->get();
        return response()->json([
            'results' => $data->map(fn($item) => ['id' => $item->id, 'text' => $item->nama])
        ]);
    }

    public function getProvinsis(Request $request)
    {
        $query = Provinsi::query();
        if ($request->has('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%');
        }
        $data = $query->limit(20)->get();
        return response()->json([
            'results' => $data->map(fn($item) => ['id' => $item->id, 'text' => $item->nama])
        ]);
    }

    public function getKotas(Request $request, $provinsiId)
    {
        $query = Kota::where('idprovinsi', $provinsiId);
        if ($request->has('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%');
        }
        $data = $query->limit(20)->get();
        return response()->json([
            'results' => $data->map(fn($item) => ['id' => $item->id, 'text' => $item->nama])
        ]);
    }

    public function getKecamatans(Request $request, $kotaId)
    {
        $query = Kecamatan::where('idkota', $kotaId);
        if ($request->has('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%');
        }
        $data = $query->limit(20)->get();
        return response()->json([
            'results' => $data->map(fn($item) => ['id' => $item->id, 'text' => $item->nama])
        ]);
    }

    public function getKelurahans(Request $request, $kecamatanId)
    {
        $query = Kelurahan::where('idkecamatan', $kecamatanId);
        if ($request->has('q')) {
            $query->where('nama', 'like', '%' . $request->q . '%');
        }
        $data = $query->limit(20)->get();
        return response()->json([
            'results' => $data->map(fn($item) => ['id' => $item->id, 'text' => $item->nama])
        ]);
    }
}
