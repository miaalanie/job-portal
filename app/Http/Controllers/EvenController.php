<?php

namespace App\Http\Controllers;

use App\Models\Even;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EvenController extends Controller
{
    public function index()
    {
        $events = Even::orderBy('tanggalawal', 'desc')->get();
        return view('admin.even.index', compact('events'));
    }

    public function create()
    {
        return view('admin.even.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'namaperiode' => 'required|string|max:255',
            'visi' => 'nullable|string',
            'tanggalawal' => 'required|date',
            'tanggalselesai' => 'required|date|after_or_equal:tanggalawal',
            'lokasi' => 'required|string|max:255',
            'alamat_lengkap' => 'nullable|string',
            'latitude' => 'nullable|string|max:100',
            'longitude' => 'nullable|string|max:100',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gambar_layout' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kuota_maksimum' => 'nullable|integer|min:0',
            'maksimum_apply' => 'nullable|integer|min:0',
            'statusaktif' => 'boolean',
            'statusheadline' => 'boolean',
            'statuspaket' => 'boolean',
            'biaya' => 'required_if:statuspaket,0|nullable|numeric|min:0',
            'status_sesi' => 'boolean',
            'sesi' => 'required_if:status_sesi,1|array',
            'sponsors' => 'nullable|array',
            'sponsors.*.nama' => 'required_with:sponsors|string|max:255',
            'sponsors.*.logo' => 'nullable|image|max:1024'
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function() use ($request) {
            $data = $request->only([
                'namaperiode', 'visi', 'tanggalawal', 'tanggalselesai', 
                'lokasi', 'alamat_lengkap', 'latitude', 'longitude',
                'kuota_maksimum', 'maksimum_apply', 'biaya'
            ]);
            
            $data['useradd'] = Auth::id();
            $data['statusaktif'] = $request->has('statusaktif');
            $data['statusheadline'] = $request->has('statusheadline');
            $data['statuspaket'] = $request->has('statuspaket');
            $data['status_sesi'] = $request->input('status_sesi', 0);

            if ($request->hasFile('gambar')) {
                $path = $request->file('gambar')->store('events', 'public');
                $data['gambar'] = $path;
            }

            if ($request->hasFile('gambar_layout')) {
                $path = $request->file('gambar_layout')->store('events/layouts', 'public');
                $data['gambar_layout'] = $path;
            }

            $event = Even::create($data);

            // Handle sessions
            if ($data['status_sesi'] && $request->has('sesi')) {
                foreach ($request->sesi as $s) {
                    $event->sesis()->create([
                        'nama_sesi' => $s['nama_sesi'],
                        'jam_mulai' => $s['jam_mulai'],
                        'jam_selesai' => $s['jam_selesai'],
                        'kuota' => $s['kuota'],
                    ]);
                }
            }

            // Handle sponsors
            if ($request->has('sponsors')) {
                foreach ($request->sponsors as $index => $sponsorData) {
                    $logoPath = null;
                    if ($request->hasFile("sponsors.$index.logo")) {
                        $logoPath = $request->file("sponsors.$index.logo")->store('events/sponsors', 'public');
                    }
                    $event->sponsors()->create([
                        'nama' => $sponsorData['nama'],
                        'logo' => $logoPath
                    ]);
                }
            }

            // Handle packages
            if ($data['statuspaket'] && $request->has('pakets')) {
                foreach ($request->pakets as $p) {
                    $event->pakets()->create([
                        'nama_paket' => $p['nama_paket'],
                        'fasilitas' => $p['fasilitas'],
                        'harga' => $p['harga'],
                    ]);
                }
            }

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Event berhasil ditambahkan.']);
            }

            return redirect()->route('admin.event')->with('success', 'Event berhasil ditambahkan.');
        });
    }

    public function show($id)
    {
        try {
            $realId = \Illuminate\Support\Facades\Crypt::decrypt($id);
        } catch (\Exception $e) {
            $realId = $id;
        }

        $event = Even::with(['sesis', 'sponsors', 'pakets', 'registers' => function($q) {
            $q->where('aktivasi', 1);
        }])->findOrFail($realId);

        $stats = [
            'total_perusahaan' => $event->registers()->where('aktivasi', 1)->count(),
            'total_lowongan' => $event->lowongans()->count(),
            'total_pelamar' => $event->lamarans()->count(),
            'applicants_per_sesi' => []
        ];

        if ($event->status_sesi) {
            foreach ($event->sesis as $sesi) {
                $stats['applicants_per_sesi'][] = [
                    'nama_sesi' => $sesi->nama_sesi,
                    'count' => $event->lamarans()->where('idsesi', $sesi->id)->count()
                ];
            }
        }

        return view('admin.even.show', compact('event', 'stats'));
    }

    public function edit($id)
    {
        try {
            $realId = \Illuminate\Support\Facades\Crypt::decrypt($id);
        } catch (\Exception $e) {
            $realId = $id;
        }

        $event = Even::with(['sponsors', 'pakets'])->findOrFail($realId);
        return view('admin.even.edit', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $event = Even::findOrFail($id);

        $request->validate([
            'namaperiode' => 'required|string|max:255',
            'visi' => 'nullable|string',
            'tanggalawal' => 'required|date',
            'tanggalselesai' => 'required|date|after_or_equal:tanggalawal',
            'lokasi' => 'required|string|max:255',
            'alamat_lengkap' => 'nullable|string',
            'latitude' => 'nullable|string|max:100',
            'longitude' => 'nullable|string|max:100',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gambar_layout' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'kuota_maksimum' => 'nullable|integer|min:0',
            'maksimum_apply' => 'nullable|integer|min:0',
            'statusaktif' => 'boolean',
            'statusheadline' => 'boolean',
            'statuspaket' => 'boolean',
            'biaya' => 'required_if:statuspaket,0|nullable|numeric|min:0',
            'pakets' => 'required_if:statuspaket,1|array',
            'pakets.*.nama_paket' => 'required_with:pakets|string|max:255',
            'pakets.*.harga' => 'required_with:pakets|numeric|min:0'
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function() use ($request, $event) {
            $data = $request->only([
                'namaperiode', 'visi', 'tanggalawal', 'tanggalselesai', 
                'lokasi', 'alamat_lengkap', 'latitude', 'longitude',
                'kuota_maksimum', 'maksimum_apply', 'biaya'
            ]);
            $data['userupdate'] = Auth::id();
            $data['statusaktif'] = $request->has('statusaktif');
            $data['statusheadline'] = $request->has('statusheadline');
            $data['statuspaket'] = $request->has('statuspaket');
            $data['status_sesi'] = $request->input('status_sesi', 0);

            if ($request->hasFile('gambar')) {
                if ($event->gambar) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($event->gambar);
                }
                $path = $request->file('gambar')->store('events', 'public');
                $data['gambar'] = $path;
            }

            if ($request->hasFile('gambar_layout')) {
                if ($event->gambar_layout) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($event->gambar_layout);
                }
                $path = $request->file('gambar_layout')->store('events/layouts', 'public');
                $data['gambar_layout'] = $path;
            }

            $event->update($data);

            // Handle sessions
            $event->sesis()->delete();
            if ($data['status_sesi'] && $request->has('sesi')) {
                foreach ($request->sesi as $s) {
                    $event->sesis()->create([
                        'nama_sesi' => $s['nama_sesi'],
                        'jam_mulai' => $s['jam_mulai'],
                        'jam_selesai' => $s['jam_selesai'],
                        'kuota' => $s['kuota'],
                    ]);
                }
            }

            // Handle sponsors
            if ($request->has('sponsors')) {
                $event->sponsors()->delete(); // Re-sync sponsors
                foreach ($request->sponsors as $index => $sponsorData) {
                    $logoPath = $sponsorData['existing_logo'] ?? null;
                    if ($request->hasFile("sponsors.$index.logo")) {
                        $logoPath = $request->file("sponsors.$index.logo")->store('events/sponsors', 'public');
                    }
                    $event->sponsors()->create([
                        'nama' => $sponsorData['nama'],
                        'logo' => $logoPath
                    ]);
                }
            }

            // Handle packages
            $event->pakets()->delete();
            if ($data['statuspaket'] && $request->has('pakets')) {
                foreach ($request->pakets as $p) {
                    $event->pakets()->create([
                        'nama_paket' => $p['nama_paket'],
                        'fasilitas' => $p['fasilitas'],
                        'harga' => $p['harga'],
                    ]);
                }
            }

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Event berhasil diperbarui.']);
            }

            return redirect()->route('admin.event')->with('success', 'Event berhasil diperbarui.');
        });
    }

    public function destroy($id)
    {
        try {
            $realId = \Illuminate\Support\Facades\Crypt::decrypt($id);
        } catch (\Exception $e) {
            $realId = $id;
        }
        
        $event = Even::findOrFail($realId);
        if ($event->gambar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($event->gambar);
        }
        if ($event->gambar_layout) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($event->gambar_layout);
        }
        $event->delete();

        return response()->json(['success' => true, 'message' => 'Event berhasil dihapus.']);
    }

    public function sponsor($id)
    {
        try {
            $realId = \Illuminate\Support\Facades\Crypt::decrypt($id);
        } catch (\Exception $e) {
            $realId = $id;
        }

        $event = Even::with('sponsors')->findOrFail($realId);
        return view('admin.even.sponsor', compact('event'));
    }

    public function storeSponsor(Request $request, $id)
    {
        try {
            $realId = \Illuminate\Support\Facades\Crypt::decrypt($id);
        } catch (\Exception $e) {
            $realId = $id;
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'logo' => 'nullable|image|max:1024'
        ]);

        $event = Even::findOrFail($realId);

        $data = ['nama' => $request->nama];
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('events/sponsors', 'public');
        }

        $event->sponsors()->create($data);

        return response()->json(['success' => true, 'message' => 'Sponsor berhasil ditambahkan.']);
    }

    public function toggleStatus($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            // Keep original if not encrypted
        }
        $event = Even::findOrFail($id);
        $event->statusaktif = !$event->statusaktif;
        $event->save();

        return response()->json([
            'success' => true, 
            'message' => 'Status aktif event ' . $event->namaperiode . ' berhasil diubah.',
            'statusaktif' => $event->statusaktif
        ]);
    }

    public function toggleHeadline($id)
    {
        try {
            $id = decrypt($id);
        } catch (\Exception $e) {
            // Keep original if not encrypted
        }
        $event = Even::findOrFail($id);
        $event->statusheadline = !$event->statusheadline;
        $event->save();

        return response()->json([
            'success' => true, 
            'message' => 'Status headline event ' . $event->namaperiode . ' berhasil diubah.',
            'statusheadline' => $event->statusheadline
        ]);
    }
}
