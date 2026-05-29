<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Even;
use App\Models\Perusahaan;
use App\Models\Lowongan;
use App\Models\Lamaran;
use App\Models\Kehadiran;
use App\Models\PengaturanPerusahaan;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    private function getSettings()
    {
        return PengaturanPerusahaan::first();
    }

    private function getFilterData(Request $request, $includeVacancies = false)
    {
        $user = auth()->user();
        $isAdminEvent = $user->hasRole('Admin Event');

        if ($isAdminEvent) {
            $idperiode = $user->ideven;
            $events = Even::select('id', 'namaperiode')->where('id', $idperiode)->get();
            $companies = Perusahaan::select('id', 'nama')->whereHas('registers', function($q) use ($idperiode) {
                $q->where('idperiode', $idperiode);
            })->get();
            $vacancies = collect();
            
            if ($includeVacancies) {
                $vacancies = Lowongan::select('id', 'namalowongan')->whereHas('register', function($q) use ($idperiode) {
                    $q->where('idperiode', $idperiode);
                })->get();
            }
        } else {
            $idperiode = $request->idperiode;
            $events = Even::select('id', 'namaperiode')->get();
            $companies = Perusahaan::select('id', 'nama')->get();
            $vacancies = $includeVacancies ? Lowongan::select('id', 'namalowongan')->get() : collect();
        }

        return compact('isAdminEvent', 'idperiode', 'events', 'companies', 'vacancies');
    }

    // 1. Report: Pelamar per Lowongan
    public function pelamarLoker(Request $request)
    {
        extract($this->getFilterData($request));
        
        $idperusahaan = $request->idperusahaan;
        $format = $request->format ?? 'html';

        $data = [];
        if ($idperiode) {
            $query = Lowongan::select('id', 'namalowongan', 'idregister', 'kuota')
                ->with([
                    'register:id,idperusahaan,idperiode',
                    'register.perusahaan:id,nama',
                    'lamarans' => function($q) {
                        $q->select('id', 'idlowongan', 'idpelamar', 'idsesi');
                    },
                    'lamarans.pelamar:id,nama',
                    'lamarans.sesi:id,nama_sesi'
                ])
                ->whereHas('register', function($q) use ($idperiode) {
                    $q->where('idperiode', $idperiode);
                });

            if ($idperusahaan) {
                $query->whereHas('register', function($q) use ($idperusahaan) {
                    $q->where('idperusahaan', $idperusahaan);
                });
            }

            $data = $query->get();
        }

        if ($format == 'pdf' || $format == 'excel') {
             $settings = $this->getSettings();
             $title = "Laporan Nama Pelamar per Lowongan";
             $view = 'admin.laporan.pelamar_loker_print';
             $filename = "laporan-pelamar-lowongan-" . now()->format('YmdHis');

             if ($format == 'pdf') {
                 $pdf = Pdf::loadView($view, compact('data', 'settings', 'title'))->setPaper('a4', 'landscape');
                 return $pdf->download($filename . ".pdf");
             } else {
                 return Excel::download(new class($view, $data, $settings, $title) implements \Maatwebsite\Excel\Concerns\FromView {
                    public function __construct($view, $data, $settings, $title) { $this->view = $view; $this->data = $data; $this->settings = $settings; $this->title = $title; }
                    public function view(): \Illuminate\Contracts\View\View { return view($this->view, ['data' => $this->data, 'settings' => $this->settings, 'title' => $this->title]); }
                 }, $filename . ".xlsx");
             }
        }

        return view('admin.laporan.pelamar_loker', compact('events', 'companies', 'data', 'idperiode', 'idperusahaan', 'isAdminEvent'));
    }

    // 2. Report: Lowongan per Event (with session counts)
    public function lokerEvent(Request $request)
    {
        extract($this->getFilterData($request));
        
        $idperusahaan = $request->idperusahaan;
        $format = $request->format ?? 'html';

        $data = [];
        if ($idperiode) {
            $query = Lowongan::select('id', 'namalowongan', 'idregister', 'kuota', 'kategoriid')
                ->with([
                    'register:id,idperusahaan,idperiode',
                    'register.perusahaan:id,nama',
                    'register.even:id,namaperiode,status_sesi',
                    'register.even.sesis:id,even_id,nama_sesi',
                    'kategori:id,nama'
                ])
                ->withCount('lamarans')
                ->whereHas('register', function($q) use ($idperiode) {
                    $q->where('idperiode', $idperiode);
                });

            if ($idperusahaan) {
                $query->whereHas('register', function($q) use ($idperusahaan) {
                    $q->where('idperusahaan', $idperusahaan);
                });
            }

            $data = $query->get();
            
            $data->load(['lamarans' => function($q) { $q->select('id', 'idlowongan', 'idsesi'); }]);
        }

        if ($format == 'pdf' || $format == 'excel') {
             $settings = $this->getSettings();
             $title = "Laporan Daftar Lowongan per Event";
             $view = 'admin.laporan.loker_event_print';
             $filename = "laporan-loker-event-" . now()->format('YmdHis');

             if ($format == 'pdf') {
                 $pdf = Pdf::loadView($view, compact('data', 'settings', 'title'))->setPaper('a4', 'landscape');
                 return $pdf->download($filename . ".pdf");
             } else {
                 return Excel::download(new class($view, $data, $settings, $title) implements \Maatwebsite\Excel\Concerns\FromView {
                    public function __construct($view, $data, $settings, $title) { $this->view = $view; $this->data = $data; $this->settings = $settings; $this->title = $title; }
                    public function view(): \Illuminate\Contracts\View\View { return view($this->view, ['data' => $this->data, 'settings' => $this->settings, 'title' => $this->title]); }
                 }, $filename . ".xlsx");
             }
        }

        return view('admin.laporan.loker_event', compact('events', 'companies', 'data', 'idperiode', 'idperusahaan', 'isAdminEvent'));
    }

    // 3. Report: Kehadiran Pelamar
    public function kehadiran(Request $request)
    {
        extract($this->getFilterData($request, true));
        
        $idperusahaan = $request->idperusahaan;
        $idlowongan = $request->idlowongan;
        $format = $request->format ?? 'html';

        $data = [];
        if ($idperiode) {
            $query = Lamaran::select('id', 'idpelamar', 'idlowongan', 'ideven', 'idsesi')
                ->with([
                    'pelamar:id,nama,email',
                    'lowongan:id,namalowongan,idregister',
                    'lowongan.register:id,idperusahaan',
                    'lowongan.register.perusahaan:id,nama',
                    'even:id,namaperiode',
                    'sesi:id,nama_sesi',
                    'kehadirans:id,idlamaran,tanggal,jam'
                ])
                ->where('ideven', $idperiode);

            if ($idperusahaan) {
                $query->whereHas('lowongan.register', function($q) use ($idperusahaan) {
                    $q->where('idperusahaan', $idperusahaan);
                });
            }

            if ($idlowongan) {
                $query->where('idlowongan', $idlowongan);
            }

            $data = $query->get();
        }

        if ($format == 'pdf' || $format == 'excel') {
             $settings = $this->getSettings();
             $title = "Laporan Kehadiran Pelamar";
             $view = 'admin.laporan.kehadiran_print';
             $filename = "laporan-kehadiran-" . now()->format('YmdHis');

             if ($format == 'pdf') {
                 $pdf = Pdf::loadView($view, compact('data', 'settings', 'title'))->setPaper('a4', 'landscape');
                 return $pdf->download($filename . ".pdf");
             } else {
                 return Excel::download(new class($view, $data, $settings, $title) implements \Maatwebsite\Excel\Concerns\FromView {
                    public function __construct($view, $data, $settings, $title) { $this->view = $view; $this->data = $data; $this->settings = $settings; $this->title = $title; }
                    public function view(): \Illuminate\Contracts\View\View { return view($this->view, ['data' => $this->data, 'settings' => $this->settings, 'title' => $this->title]); }
                 }, $filename . ".xlsx");
             }
        }

        return view('admin.laporan.kehadiran', compact('events', 'companies', 'vacancies', 'data', 'idperiode', 'idperusahaan', 'idlowongan', 'isAdminEvent'));
    }

    // 4. Report: Detail Data Pelamar per Lowongan
    public function pelamarDetail(Request $request)
    {
        extract($this->getFilterData($request, true));
        
        $idperusahaan = $request->idperusahaan;
        $idlowongan = $request->idlowongan;
        $format = $request->format ?? 'html';

        $data = [];
        if ($idperiode) {
            $query = Lamaran::select('id', 'idpelamar', 'idlowongan', 'ideven', 'idsesi')
                ->with([
                    'pelamar:id,noktp,namalengkap,tanggallahir,tempatlahir,nohp,alamatlengkap',
                    'lowongan:id,namalowongan,idregister',
                    'lowongan.register:id,idperusahaan',
                    'lowongan.register.perusahaan:id,nama',
                    'even:id,namaperiode',
                    'sesi:id,nama_sesi'
                ])
                ->where('ideven', $idperiode);

            if ($idperusahaan) {
                $query->whereHas('lowongan.register', function($q) use ($idperusahaan) {
                    $q->where('idperusahaan', $idperusahaan);
                });
            }

            if ($idlowongan) {
                $query->where('idlowongan', $idlowongan);
            }

            $data = $query->get();
        }

        if ($format == 'pdf' || $format == 'excel') {
             $settings = $this->getSettings();
             $title = "Laporan Detail Data Pelamar per Lowongan";
             $view = 'admin.laporan.pelamar_detail_print';
             $filename = "laporan-detail-pelamar-" . now()->format('YmdHis');

             if ($format == 'pdf') {
                 $pdf = Pdf::loadView($view, compact('data', 'settings', 'title'))->setPaper('a4', 'landscape');
                 return $pdf->download($filename . ".pdf");
             } else {
                 return Excel::download(new class($view, $data, $settings, $title) implements \Maatwebsite\Excel\Concerns\FromView {
                    public function __construct($view, $data, $settings, $title) { $this->view = $view; $this->data = $data; $this->settings = $settings; $this->title = $title; }
                    public function view(): \Illuminate\Contracts\View\View { return view($this->view, ['data' => $this->data, 'settings' => $this->settings, 'title' => $this->title]); }
                 }, $filename . ".xlsx");
             }
        }

        return view('admin.laporan.pelamar_detail', compact('events', 'companies', 'vacancies', 'data', 'idperiode', 'idperusahaan', 'idlowongan', 'isAdminEvent'));
    }
}
