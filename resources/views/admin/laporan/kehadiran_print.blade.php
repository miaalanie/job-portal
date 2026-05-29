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
        .text-success { color: #28a745; font-weight: bold; }
        .text-danger { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    @include('admin.laporan.header_print')

    <table class="table">
        <thead>
            <tr>
                <th width="30">NO</th>
                <th>PELAMAR</th>
                <th>LOWONGAN & PERUSAHAAN</th>
                <th>SESI</th>
                <th>STATUS</th>
                <th>TANGGAL & JAM PRESENSI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $lamaran)
            @php $hadir = $lamaran->kehadirans->isNotEmpty(); @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>
                    <div class="fw-bold">{{ $lamaran->pelamar->nama }}</div>
                    <div style="color: #666; font-size: 10px;">{{ $lamaran->pelamar->email }}</div>
                </td>
                <td>
                    <div class="fw-bold">{{ $lamaran->lowongan->namalowongan }}</div>
                    <div style="color: #666; font-size: 10px;">{{ $lamaran->lowongan->register->perusahaan->nama ?? 'N/A' }}</div>
                </td>
                <td class="text-center">{{ $lamaran->sesi->nama_sesi ?? 'Reguler' }}</td>
                <td class="text-center">
                    @if($hadir)
                        <span class="text-success">HADIR</span>
                    @else
                        <span class="text-danger">TIDAK HADIR</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($hadir)
                        {{ \Carbon\Carbon::parse($lamaran->kehadirans->first()->tanggal)->format('d-m-Y') }}
                        <div style="font-size: 9px;">{{ $lamaran->kehadirans->first()->jam }}</div>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
