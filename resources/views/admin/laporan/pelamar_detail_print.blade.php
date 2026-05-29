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
    </style>
</head>
<body>
    @include('admin.laporan.header_print')

    <table class="table">
        <thead>
            <tr>
                <th width="30">NO</th>
                <th width="120">NIK / NO KTP</th>
                <th>NAMA LENGKAP</th>
                <th>TEMPAT, TGL LAHIR</th>
                <th>NO HP</th>
                <th>ALAMAT LENGKAP</th>
                <th>LOWONGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $lamaran)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="fw-bold">{{ $lamaran->pelamar->noktp }}</td>
                <td>{{ $lamaran->pelamar->namalengkap }}</td>
                <td>{{ $lamaran->pelamar->tempatlahir }}, {{ \Carbon\Carbon::parse($lamaran->pelamar->tanggallahir)->format('d-m-Y') }}</td>
                <td>{{ $lamaran->pelamar->nohp }}</td>
                <td>{{ $lamaran->pelamar->alamatlengkap }}</td>
                <td>
                    <div class="fw-bold">{{ $lamaran->lowongan->namalowongan }}</div>
                    <div style="font-size: 9px; color: #666;">{{ $lamaran->lowongan->register->perusahaan->nama ?? 'N/A' }}</div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
