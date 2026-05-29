<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #333; padding: 10px; font-size: 11px; }
        .table th { background-color: #f2f2f2; text-transform: uppercase; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .badge { display: inline-block; padding: 2px 5px; border: 1px solid #ccc; font-size: 10px; margin: 1px; }
    </style>
</head>
<body>
    @include('admin.laporan.header_print')

    <table class="table">
        <thead>
            <tr>
                <th width="30">NO</th>
                <th>LOWONGAN & PERUSAHAAN</th>
                <th width="100">QUOTA</th>
                <th width="100">TOTAL PELAMAR</th>
                <th>DAFTAR NAMA PELAMAR (SESI)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $loker)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>
                    <div class="fw-bold">{{ $loker->namalowongan }}</div>
                    <div style="color: #666; font-size: 10px;">{{ $loker->register->perusahaan->nama ?? 'N/A' }}</div>
                </td>
                <td class="text-center">{{ $loker->kuota }}</td>
                <td class="text-center">{{ $loker->lamarans->count() }}</td>
                <td>
                    @foreach($loker->lamarans as $lamaran)
                        <span class="badge">{{ $lamaran->pelamar->nama }} @if($lamaran->sesi) ({{ $lamaran->sesi->nama_sesi }}) @endif</span>
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
