<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CV - {{ $applicant->namalengkap }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; line-height: 1.6; }
        .header { border-bottom: 2px solid #7f1d1d; padding-bottom: 10px; margin-bottom: 20px; }
        .name { font-size: 24px; font-weight: bold; color: #7f1d1d; text-transform: uppercase; }
        .section-title { font-size: 14px; font-weight: bold; background: #f3f4f6; padding: 5px 10px; margin: 15px 0 10px; border-left: 4px solid #7f1d1d; }
        .content-item { margin-bottom: 10px; }
        .date { color: #666; font-size: 11px; font-style: italic; }
        .label { font-weight: bold; width: 120px; display: inline-block; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; vertical-align: top; padding: 5px 0; }
        .photo { float: right; width: 100px; height: 120px; border: 1px solid #ddd; padding: 2px; }
    </style>
</head>
<body>

<div class="header">
    @if($applicant->foto && $applicant->foto != 'no-image')
        <img src="{{ public_path('storage/'.$applicant->foto) }}" class="photo">
    @endif
    <div class="name">{{ $applicant->namalengkap }}</div>
    <div>{{ $applicant->alamatlengkap }}</div>
    <div>Email: {{ $applicant->user->email ?? '-' }} | Telp: {{ $applicant->nohp }}</div>
</div>

<div class="section-title">PROFIL PRIBADI</div>
<table>
    <tr><td class="label">NIK</td><td>{{ $applicant->noktp }}</td></tr>
    <tr><td class="label">TTL</td><td>{{ $applicant->tempatlahir }}, {{ \Carbon\Carbon::parse($applicant->tanggallahir)->format('d F Y') }}</td></tr>
    <tr><td class="label">Jenis Kelamin</td><td>{{ $applicant->jeniskelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
    <tr><td class="label">Tinggi/Berat</td><td>{{ $applicant->tinggibadan }} cm / {{ $applicant->beratbadan }} kg</td></tr>
</table>

<div class="section-title">DESKRIPSI DIRI</div>
<div class="content-item italic">
    {{ $applicant->deskripsidiri ?? 'Belum ada deskripsi profil.' }}
</div>

<div class="section-title">RIWAYAT PENDIDIKAN</div>
@foreach($applicant->pendidikans as $edu)
<div class="content-item">
    <strong>{{ $edu->namasekolah }}</strong> ({{ $edu->kategori }})<br>
    {{ $edu->jurusan }} | <span class="date">{{ $edu->tahunawal }} - {{ $edu->tahunselesai }}</span>
</div>
@endforeach

<div class="section-title">PENGALAMAN KERJA</div>
@foreach($applicant->pengalamans as $exp)
<div class="content-item">
    <strong>{{ $exp->namaperusahaan }}</strong><br>
    {{ $exp->posisi }} | <span class="date">{{ $exp->tahunawal }} - {{ $exp->tahunselesai ?: 'Sekarang' }}</span>
</div>
@endforeach

<div class="section-title">KEAHLIAN & KOMPETENSI</div>
<ul>
    @foreach($applicant->skills as $skill)
        <li>{{ $skill->namaskill }} - <small>{{ $skill->level }}</small></li>
    @endforeach
</ul>

<div style="margin-top: 30px; font-size: 10px; text-align: center; color: #999;">
    Dicetak melalui FindTalen Recruitment System pada {{ date('d/m/Y H:i') }}
</div>

</body>
</html>
