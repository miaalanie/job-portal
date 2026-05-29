<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice_no }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        .header-table { width: 100%; margin-bottom: 40px; border-bottom: 2px solid {{ $pengaturan->primary_color ?? '#7f1d1d' }}; padding-bottom: 20px; }
        .logo { max-width: 150px; }
        .company-info { text-align: right; }
        .company-info h2 { margin: 0; color: {{ $pengaturan->primary_color ?? '#7f1d1d' }}; font-size: 24px; }
        .company-info p { margin: 2px 0; font-size: 11px; color: #666; }
        
        .client-info-table { width: 100%; margin-bottom: 40px; }
        .client-info-table td { vertical-align: top; }
        .invoice-title { font-size: 28px; font-weight: bold; color: #333; margin-bottom: 5px; }
        .invoice-meta { font-size: 13px; color: #777; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .items-table th { background: #f8f9fa; border-bottom: 2px solid #dee2e6; padding: 12px 15px; text-align: left; font-size: 14px; text-transform: uppercase; }
        .items-table td { padding: 12px 15px; border-bottom: 1px solid #eee; font-size: 13px; }
        .items-table .amount { text-align: right; }
        
        .total-table { width: 100%; }
        .total-table td { text-align: right; padding: 5px 0; }
        .total-row { font-size: 18px; font-weight: bold; color: {{ $pengaturan->primary_color ?? '#7f1d1d' }}; }
        
        .status-badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 12px; margin-top: 10px; }
        .status-paid { background: #e8f5e9; color: #2e7d32; }
        .status-pending { background: #fff8e1; color: #f57f17; }
        
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 20px; }
        .bank-info { background: #fdfdfd; padding: 15px; border: 1px dashed #ddd; margin-top: 20px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table class="header-table">
            <tr>
                <td>
                    @if($pengaturan->logo)
                        <img src="{{ public_path('storage/' . $pengaturan->logo) }}" class="logo">
                    @else
                        <h2>FindTalen</h2>
                    @endif
                </td>
                <td class="company-info">
                    <h2>{{ $pengaturan->nama_perusahaan ?? 'FindTalen Platform' }}</h2>
                    <p>{{ $pengaturan->alamat_lengkap ?? 'Alamat Tidak Tersedia' }}</p>
                    <p>Telp: {{ $pengaturan->telp ?? '-' }} | Email: {{ $pengaturan->email ?? '-' }}</p>
                </td>
            </tr>
        </table>

        <table class="client-info-table">
            <tr>
                <td width="50%">
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-meta">No: {{ $invoice_no }}</div>
                    <div class="invoice-meta">Tanggal: {{ \Carbon\Carbon::parse($register->tanggalregister)->format('d F Y') }}</div>
                </td>
                <td width="50%" style="text-align: right;">
                    <div style="font-weight: bold; color: #555;">DITAGIHKAN KEPADA:</div>
                    <div style="font-size: 15px; font-weight: bold; margin-top: 5px;">{{ $perusahaan->nama }}</div>
                    <div style="font-size: 11px; color: #666; width: 250px; float: right;">{{ $perusahaan->alamat }}</div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Deskripsi Layanan</th>
                    <th class="amount">Subtotal (IDR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Pendaftaran Event: {{ $even->namaperiode }}</strong><br>
                        <small>Paket: {{ $register->namapaket }}</small>
                    </td>
                    <td class="amount">{{ number_format($register->biaya, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <table class="total-table" align="right" style="width: 300px;">
            <tr>
                <td style="color: #666;">Pajak (0%):</td>
                <td>0</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL:</td>
                <td>Rp {{ number_format($register->biaya, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div style="clear: both;"></div>

        <div class="bank-info">
            <strong>Instruksi Pembayaran:</strong><br>
            Silakan lakukan transfer ke rekening resmi platform FindTalen.<br>
            Bank Mandiri: <strong>123-456-7890-123</strong> (A/N FindTalen Recruitment)<br>
            <small>*Cantumkan nomor invoice <strong>{{ $invoice_no }}</strong> pada berita transfer.</small>
        </div>

        <div style="margin-top: 30px;">
            <strong>Status Pendaftaran:</strong><br>
            @if($register->aktivasi == 1)
                <span class="status-badge status-paid">TERVERIFIKASI / AKTIF</span>
            @else
                <span class="status-badge status-pending">MENUNGGU VALIDASI PEMBAYARAN</span>
            @endif
        </div>

        <div class="footer">
            Invoice ini dihasilkan secara otomatis oleh sistem FindTalen Platform.<br>
            &copy; {{ date('Y') }} {{ $pengaturan->nama_perusahaan ?? 'FindTalen' }}. All rights reserved.
        </div>
    </div>
</body>
</html>
