<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Gaji SPJ - {{ $monthName }} {{ $year }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
            background: #f5f5f5;
            padding: 20px;
        }

        .page {
            background: #fff;
            width: 297mm;
            min-height: 210mm;
            margin: 0 auto;
            padding: 12mm 15mm;
            box-shadow: 0 2px 20px rgba(0,0,0,0.15);
        }

        /* === KOP SURAT === */
        .kop {
            display: flex;
            align-items: center;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 6px;
        }
        .kop-logo {
            width: 65px;
            height: 65px;
            object-fit: contain;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .kop-logo-placeholder {
            width: 65px;
            height: 65px;
            border: 1px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #999;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .kop-text { flex: 1; text-align: center; }
        .kop-text .nama-sekolah {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            line-height: 1.3;
        }
        .kop-text .alamat-sekolah {
            font-size: 9.5px;
            margin-top: 2px;
            color: #222;
        }
        .kop-text .nss {
            font-size: 9px;
            color: #444;
        }

        /* === JUDUL LAPORAN === */
        .judul-wrap {
            text-align: center;
            margin: 10px 0 6px 0;
        }
        .judul-wrap .judul-utama {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }
        .judul-wrap .judul-periode {
            font-size: 10px;
            font-weight: bold;
            margin-top: 2px;
        }

        /* === TABEL SPJ === */
        table.spj {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-top: 8px;
        }
        table.spj th, table.spj td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: middle;
        }
        table.spj thead tr th {
            background-color: #d9e1f2;
            font-weight: bold;
            text-align: center;
            font-size: 8px;
            text-transform: uppercase;
        }
        table.spj thead tr.sub-header th {
            background-color: #e9edf8;
            font-size: 7.5px;
        }
        table.spj tbody tr:nth-child(even) {
            background-color: #f7f9ff;
        }
        table.spj tbody tr td.no {
            text-align: center;
            font-weight: bold;
        }
        table.spj tbody tr td.nama {
            font-weight: bold;
        }
        table.spj tbody tr td.jabatan {
            font-size: 7.5px;
            color: #333;
        }
        table.spj tbody tr td.angka {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-size: 8px;
            white-space: nowrap;
        }
        table.spj tbody tr td.angka-bruto {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-size: 8px;
            font-weight: bold;
            background-color: #e8f5e9;
            white-space: nowrap;
        }
        table.spj tbody tr td.angka-netto {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-size: 8px;
            font-weight: bold;
            background-color: #fff3e0;
            white-space: nowrap;
        }
        table.spj tbody tr td.angka-potongan {
            text-align: right;
            font-family: 'Courier New', monospace;
            font-size: 8px;
            color: #b71c1c;
            white-space: nowrap;
        }
        table.spj tfoot tr td {
            font-weight: bold;
            background-color: #003B73;
            color: #fff;
            text-align: right;
            font-family: 'Courier New', monospace;
            font-size: 8px;
            border-color: #002a5c;
        }
        table.spj tfoot tr td.label-total {
            text-align: center;
            font-size: 8.5px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        table.spj tfoot tr td.total-bruto {
            background-color: #1b5e20;
            color: #fff;
        }
        table.spj tfoot tr td.total-netto {
            background-color: #e65100;
            color: #fff;
        }
        table.spj tfoot tr td.total-potongan {
            color: #ffcdd2;
        }

        /* === TERBILANG & TANDATANGAN === */
        .bottom-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 12px;
        }
        .terbilang-box {
            flex: 1;
            border: 1px solid #000;
            padding: 6px 10px;
            font-size: 8.5px;
            max-width: 60%;
        }
        .terbilang-box .tb-label { font-weight: bold; text-transform: uppercase; margin-bottom: 3px; }
        .terbilang-box .tb-value { font-style: italic; }
        .terbilang-box .tb-netto { font-weight: bold; font-size: 10px; margin-bottom: 2px; }

        .ttd-area {
            display: flex;
            gap: 30px;
            margin-left: 20px;
        }
        .ttd-col {
            text-align: center;
            font-size: 8.5px;
        }
        .ttd-col .ttd-title { font-weight: bold; margin-bottom: 50px; }
        .ttd-col .ttd-name { font-weight: bold; text-decoration: underline; }
        .ttd-col .ttd-nip { font-size: 7.5px; color: #444; }

        /* === FOOTER === */
        .footer-print {
            margin-top: 10px;
            border-top: 1px dashed #aaa;
            padding-top: 4px;
            display: flex;
            justify-content: space-between;
            font-size: 7.5px;
            color: #666;
            font-style: italic;
        }

        /* === NO PRINT === */
        .no-print {
            text-align: center;
            padding: 20px;
        }
        .btn-print {
            background: #003B73;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-print:hover { background: #002a5c; }
        .btn-back {
            background: #555;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            margin-right: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        @media print {
            @page {
                size: A3 landscape;
                margin: 10mm 12mm;
            }
            html, body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .page {
                box-shadow: none;
                width: 100%;
                padding: 0;
                margin: 0;
            }
            .no-print { display: none !important; }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

@php
    $fmt = new \NumberFormatter("id", \NumberFormatter::SPELLOUT);

    $totalGajiPokok     = $reportData->sum('gaji_pokok');
    $totalTunjJabatan   = $reportData->sum('tunjangan_jabatan');
    $totalTunjBpjs      = $reportData->sum('tunjangan_bpjs');
    $totalJmlTunjangan  = $reportData->sum('jumlah_tunjangan');
    $totalBruto         = $reportData->sum('jumlah_bruto');
    $totalPotKes        = $reportData->sum('potongan_kes');
    $totalPotNaker      = $reportData->sum('potongan_naker');
    $totalPotongan      = $reportData->sum('jumlah_potongan');
    $totalNetto         = $reportData->sum('jumlah_netto');

    if (!function_exists('rpFmt')) {
        function rpFmt($val) {
            return number_format($val, 0, ',', '.');
        }
    }

    // Logo
    $logoUrl = null;
    if (!empty($settingLogo)) {
        if (str_starts_with($settingLogo, 'http')) {
            $logoUrl = $settingLogo;
        } else {
            $path = str_replace(['/storage/', 'storage/'], '', $settingLogo);
            $fullPath = storage_path('app/public/' . ltrim($path, '/'));
            if (file_exists($fullPath)) {
                $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                $data = file_get_contents($fullPath);
                $logoUrl = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } else {
                $logoUrl = asset('storage/' . ltrim($path, '/'));
            }
        }
    }
@endphp

<div class="no-print">
    <a href="javascript:history.back()" class="btn-back">← Kembali</a>
    <button class="btn-print" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
        </svg>
        Cetak / Print PDF
    </button>
</div>

<div class="page">

    {{-- KOP SURAT --}}
    <div class="kop">
        @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="Logo Sekolah" class="kop-logo" onerror="this.style.display='none'">
        @else
            <div class="kop-logo-placeholder">LOGO</div>
        @endif
        <div class="kop-text">
            <div class="nama-sekolah">{{ $settingName ?? 'SMK NU 1 ISLAMIYAH KRAMAT' }}</div>
            <div class="alamat-sekolah">{{ $settingAddress ?? 'Jl. Garuda No. 39 - Kemantran, Kramat, Tegal' }}</div>
            <div class="nss">Telp/Fax: &nbsp;&bull;&nbsp; Email: tata usaha sekolah</div>
        </div>
    </div>

    {{-- JUDUL --}}
    <div class="judul-wrap">
        <div class="judul-utama">Daftar Gaji Pegawai / SPJ</div>
        <div class="judul-periode">Bulan : {{ strtoupper($monthName) }} {{ $year }}</div>
    </div>

    {{-- TABEL --}}
    <table class="spj">
        <thead>
            <tr>
                <th rowspan="3" style="width:22px">No</th>
                <th rowspan="3" style="width:130px">Nama Pegawai</th>
                <th rowspan="3" style="width:100px">Jabatan</th>
                <th rowspan="3" style="width:72px">Gaji Pokok<br>(Rp)</th>
                <th colspan="3">Tunjangan (Rp)</th>
                <th rowspan="3" style="width:72px">Jumlah<br>Bruto (Rp)</th>
                <th colspan="3">Potongan BPJS (Rp)</th>
                <th rowspan="3" style="width:75px">Jumlah<br>Netto (Rp)</th>
                <th rowspan="3" style="width:80px">Tanda<br>Tangan</th>
            </tr>
            <tr class="sub-header">
                <th style="width:68px">Jabatan</th>
                <th style="width:68px">BPJS</th>
                <th style="width:68px">Jumlah</th>
                <th style="width:60px">Kes</th>
                <th style="width:60px">Naker</th>
                <th style="width:65px">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $i => $row)
            <tr>
                <td class="no">{{ $i + 1 }}</td>
                <td class="nama">{{ $row['nama'] }}</td>
                <td class="jabatan">{{ $row['jabatan'] }}</td>
                <td class="angka">{{ rpFmt($row['gaji_pokok']) }}</td>
                <td class="angka">{{ $row['tunjangan_jabatan'] > 0 ? rpFmt($row['tunjangan_jabatan']) : '-' }}</td>
                <td class="angka">{{ $row['tunjangan_bpjs'] > 0 ? rpFmt($row['tunjangan_bpjs']) : '-' }}</td>
                <td class="angka">{{ $row['jumlah_tunjangan'] > 0 ? rpFmt($row['jumlah_tunjangan']) : '-' }}</td>
                <td class="angka-bruto">{{ rpFmt($row['jumlah_bruto']) }}</td>
                <td class="angka-potongan">{{ $row['potongan_kes'] > 0 ? rpFmt($row['potongan_kes']) : '-' }}</td>
                <td class="angka-potongan">{{ $row['potongan_naker'] > 0 ? rpFmt($row['potongan_naker']) : '-' }}</td>
                <td class="angka-potongan">{{ $row['jumlah_potongan'] > 0 ? rpFmt($row['jumlah_potongan']) : '-' }}</td>
                <td class="angka-netto">{{ rpFmt($row['jumlah_netto']) }}</td>
                <td style="font-size:7px; color:#555;">{{ $i + 1 }} . . . . . . . . .</td>
            </tr>
            @empty
            <tr>
                <td colspan="13" style="text-align:center; padding:20px; font-style:italic; color:#666;">
                    Belum ada data gaji untuk periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="label-total">J U M L A H</td>
                <td>{{ rpFmt($totalGajiPokok) }}</td>
                <td>{{ rpFmt($totalTunjJabatan) }}</td>
                <td>{{ rpFmt($totalTunjBpjs) }}</td>
                <td>{{ rpFmt($totalJmlTunjangan) }}</td>
                <td class="total-bruto">{{ rpFmt($totalBruto) }}</td>
                <td class="total-potongan">{{ rpFmt($totalPotKes) }}</td>
                <td class="total-potongan">{{ $totalPotNaker > 0 ? rpFmt($totalPotNaker) : '-' }}</td>
                <td class="total-potongan">{{ $totalPotongan > 0 ? rpFmt($totalPotongan) : '-' }}</td>
                <td class="total-netto">{{ $totalNetto > 0 ? rpFmt($totalNetto) : '-' }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    {{-- TERBILANG & TANDA TANGAN --}}
    <div class="bottom-section">
        <div class="terbilang-box">
            <div class="tb-label">Jumlah Netto Keseluruhan :</div>
            <div class="tb-netto">Rp {{ rpFmt($totalNetto) }},-</div>
            <div class="tb-label" style="margin-top:4px;">Terbilang :</div>
            <div class="tb-value">
                {{ ucwords($fmt->format($totalNetto)) }} Rupiah
            </div>
        </div>

        <div class="ttd-area">
            <div class="ttd-col">
                <div class="ttd-title">Mengetahui,<br>Kepala Sekolah</div>
                <div class="ttd-name">___________________</div>
                <div class="ttd-nip">NIP. -</div>
            </div>
            <div class="ttd-col">
                <div class="ttd-title">{{ $settingCity ?? 'Tegal' }}, {{ now()->translatedFormat('d F Y') }}<br>Bendahara</div>
                <div class="ttd-name">___________________</div>
                <div class="ttd-nip">NIP. -</div>
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer-print">
        <span>{{ $settingFooter ?? 'Aplikasi Keuangan Sekolah' }}</span>
        <span>Dicetak: {{ now()->translatedFormat('d/m/Y H:i') }} WIB</span>
    </div>

</div>
</body>
</html>
