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
        .badge { display: inline-block; padding: 1px 3px; font-size: 10px; margin: 1px; color: #333; }
    </style>
</head>
<body>
    @include('admin.laporan.header_print')

    <table class="table">
        <thead>
            <tr>
                <th width="30">NO</th>
                <th>LOWONGAN & KATEGORI</th>
                <th>PERUSAHAAN</th>
                <th width="80">KUOTA</th>
                <th width="80">TOTAL PELAMAR</th>
                <th>RINCIAN PER SESI</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $i => $loker)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>
                    <div class="fw-bold">{{ $loker->namalowongan }}</div>
                    <div style="color: #666; font-size: 10px;">{{ $loker->kategori->nama ?? 'N/A' }}</div>
                </td>
                <td class="text-center">{{ $loker->register->perusahaan->nama ?? 'N/A' }}</td>
                <td class="text-center fw-bold">{{ $loker->kuota }}</td>
                <td class="text-center">{{ $loker->lamarans->count() }}</td>
                <td>
                    @if($loker->register->even && $loker->register->even->status_sesi == 1)
                        @foreach($loker->register->even->sesis as $s)
                            @php $cnt = $loker->lamarans->where('idsesi', $s->id)->count(); @endphp
                            <div style="font-size: 9px; margin-bottom: 2px; border-bottom: 1px dashed #ccc; padding-bottom: 2px;">
                                <span style="color: #555;">{{ $s->nama_sesi }}:</span>
                                <strong>{{ $cnt }} Pelamar</strong>
                            </div>
                        @endforeach
                    @else
                        Reguler (Tanpa Sesi)
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
