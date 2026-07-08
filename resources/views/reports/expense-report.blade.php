<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengeluaran - {{ $year }}</title>
    <style>
        /* CSS Khusus Laporan Formal (Standar Instansi) */
        @page {
            size: A4 portrait; /* Lebih resmi menggunakan Portrait untuk laporan tabel */
            margin: 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            background-color: #fff;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
        }
        
        /* Kop Surat Resmi */
        .kop-surat {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .kop-logo {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
        }
        .kop-logo img {
            width: 80px;
            height: auto;
        }
        .kop-text {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }
        .kop-text h1 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-text h2 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .kop-text p {
            margin: 2px 0 0 0;
            font-size: 10pt;
        }
        
        /* Garis Ganda Kop Surat */
        .garis-kop {
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 2px;
            margin-bottom: 20px;
        }

        /* Judul Laporan */
        .judul-laporan {
            text-align: center;
            margin-bottom: 20px;
        }
        .judul-laporan h3 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .judul-laporan p {
            margin: 5px 0 0 0;
            font-size: 11pt;
        }

        /* Tabel Data */
        table.tabel-formal {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11pt;
        }
        table.tabel-formal th, table.tabel-formal td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        table.tabel-formal th {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }

        /* Tanda Tangan */
        .ttd-container {
            width: 100%;
            margin-top: 40px;
            display: table;
        }
        .ttd-kiri {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        .ttd-kanan {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        .ttd-nama {
            margin-top: 80px;
            font-weight: bold;
            text-decoration: underline;
        }

        /* UI Tombol Cetak (Sembunyi saat diprint) */
        @media print {
            .no-print { display: none !important; }
            body { background: none; }
            .container { box-shadow: none; max-width: none; border: none; padding: 0; }
        }
        /* Tampilan di Layar Monitor */
        @media screen {
            body { background-color: #f3f4f6; padding: 40px 0; }
            .container { 
                background: white; 
                padding: 40px; 
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); 
                border: 1px solid #e5e7eb;
            }
            .tombol-cetak {
                position: fixed;
                bottom: 30px;
                right: 30px;
                background-color: #111827;
                color: white;
                padding: 12px 24px;
                border: none;
                border-radius: 8px;
                font-size: 11pt;
                font-weight: bold;
                cursor: pointer;
                box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            }
            .tombol-cetak:hover { background-color: #374151; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Kop Surat -->
        <div class="kop-surat">
            <div class="kop-logo">
                @if($settingLogo)
                    @php
                        $logoSrc = $settingLogo;
                        if (!str_starts_with($settingLogo, 'http')) {
                            $relativePath = ltrim($settingLogo, '/');
                            $fullPath = public_path($relativePath);
                            if (file_exists($fullPath)) {
                                $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                                $data = file_get_contents($fullPath);
                                $logoSrc = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            } else {
                                $logoSrc = asset($relativePath);
                            }
                        }
                    @endphp
                    <img src="{{ $logoSrc }}" alt="Logo">
                @endif
            </div>
            <div class="kop-text">
                <h1>{{ $settingName ?? 'YAYASAN PENDIDIKAN SEKOLAH' }}</h1>
                <h2>UNIT KEUANGAN DAN BENDAHARA</h2>
                <p>{{ $settingAddress ?? 'Alamat Lengkap Sekolah Belum Diatur' }}</p>
                <p>Telepon: (021) 12345678 | Email: admin@sekolah.sch.id</p>
            </div>
        </div>
        <div class="garis-kop"></div>

        <!-- Judul -->
        <div class="judul-laporan">
            <h3>Laporan Rekapitulasi Pengeluaran</h3>
            <p>Periode: {{ $month ? Carbon\Carbon::create()->month($month)->translatedFormat('F') : 'Seluruh Bulan' }} Tahun {{ $year }}</p>
        </div>

        <!-- Tabel Data -->
        <table class="tabel-formal">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 20%;">Kategori</th>
                    <th style="width: 40%;">Uraian / Keterangan</th>
                    <th style="width: 20%;">Nominal (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $no = 1; 
                @endphp
                @forelse($expenses as $category => $items)
                    @foreach($items as $item)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ Carbon\Carbon::parse($item->transaction_date)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $category }}</td>
                        <td class="text-left">{{ $item->description }}</td>
                        <td class="text-right">{{ number_format($item->amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data pengeluaran pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">TOTAL PENGELUARAN KESELURUHAN</th>
                    <th class="text-right font-bold">Rp {{ number_format($totalExpense, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        <!-- Tanda Tangan -->
        <div class="ttd-container">
            <div class="ttd-kiri">
                <p>Mengetahui,<br><b>Kepala Sekolah</b></p>
                <p class="ttd-nama">( ....................................... )</p>
                <p>NIP: .......................................</p>
            </div>
            <div class="ttd-kanan">
                <p>Brebes, {{ now()->translatedFormat('d F Y') }}<br><b>Bendahara Sekolah</b></p>
                <p class="ttd-nama">{{ auth()->user()->name ?? '( ....................................... )' }}</p>
                <p>NIP: .......................................</p>
            </div>
        </div>
    </div>

    <button class="tombol-cetak no-print" onclick="window.print()">🖨️ Cetak Laporan</button>
</body>
</html>
