@if($settings)
<div style="text-align: center; border-bottom: 3px double #333; padding-bottom: 10px; margin-bottom: 20px;">
    <table style="width: 100%; border: none;">
        <tr>
            <td style="width: 15%; text-align: left; vertical-align: middle; border: none;">
                @if($settings->logo)
                    <img src="{{ public_path('storage/'.$settings->logo) }}" alt="logo" style="max-height: 80px;">
                @endif
            </td>
            <td style="width: 85%; text-align: center; vertical-align: middle; border: none;">
                <h1 style="margin: 0; padding: 0; font-size: 24px;">{{ $settings->nama_perusahaan }}</h1>
                <p style="margin: 5px 0; font-size: 14px;">{{ $settings->alamat_lengkap }}</p>
                <p style="margin: 0; font-size: 12px;">Tel: {{ $settings->telp }} | Email: {{ $settings->email }} | Website: {{ $settings->website }}</p>
            </td>
        </tr>
    </table>
</div>
<h2 style="text-align: center; text-transform: uppercase;">{{ $title ?? 'LAPORAN' }}</h2>
<hr>
@endif
