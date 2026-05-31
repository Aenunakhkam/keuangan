<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potongan Gaji - {{ $monthName }} {{ $year }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 9px; color: #000; background: #f5f5f5; padding: 10px; }

        .page {
            background: #fff;
            width: 420mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 10mm 12mm;
            box-shadow: 0 2px 20px rgba(0,0,0,0.15);
        }

        /* KOP */
        .kop { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 6px; margin-bottom: 4px; }
        .kop-logo { width: 55px; height: 55px; object-fit: contain; margin-right: 12px; flex-shrink: 0; }
        .kop-logo-placeholder { width: 55px; height: 55px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; font-size: 7px; color: #999; margin-right: 12px; flex-shrink: 0; }
        .kop-text { flex: 1; text-align: center; }
        .kop-school { font-size: 16pt; font-weight: bold; line-height: 1.2; text-transform: uppercase; }
        .kop-address { font-size: 8pt; color: #333; margin-top: 2px; }

        /* JUDUL */
        .judul { text-align: center; margin: 6px 0 4px; }
        .judul h2 { font-size: 12pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .judul p  { font-size: 9pt; margin-top: 2px; }

        /* TABEL */
        table { border-collapse: collapse; width: 100%; font-size: 7.5px; }
        th, td { border: 1px solid #555; padding: 2.5px 3px; }
        th { background: #d9e1f2; text-align: center; font-weight: bold; vertical-align: middle; white-space: nowrap; }
        td { vertical-align: middle; }
        .angka { text-align: right; white-space: nowrap; }
        .center { text-align: center; }

        /* Cluster headers */
        .th-kop    { background: #c5d5f0; }
        .th-bpd    { background: #c5e8d0; }
        .th-sek    { background: #fce8c5; }
        .th-total  { background: #f5c5c5; }
        .th-bersih { background: #c5f5d5; }

        /* Cells warna */
        .td-kop    { background: #f0f5ff; text-align: right; }
        .td-bpd    { background: #f0fff5; text-align: right; }
        .td-sek    { background: #fffaf0; text-align: right; }
        .td-total  { background: #fff0f0; text-align: right; font-weight: bold; color: #b71c1c; }
        .td-bersih { background: #f0fff8; text-align: right; font-weight: bold; color: #1b5e20; }

        /* Total row */
        tfoot tr td { background: #003B73; color: #fff; font-weight: bold; text-align: right; }
        tfoot tr td.label { text-align: center; background: #003B73; }
        tfoot tr td.td-bersih-total { background: #1b5e20; color: #fff; font-weight: bold; }

        /* TTD */
        .bottom-section { display: flex; justify-content: space-between; align-items: flex-start; margin-top: 14px; }
        .terbilang-box { flex: 1; background: #f8f9fa; border: 1px solid #ccc; border-radius: 4px; padding: 8px 10px; margin-right: 20px; font-size: 8px; line-height: 1.6; }
        .tb-label { font-weight: bold; color: #555; }
        .tb-netto { font-size: 10px; font-weight: bold; color: #003B73; }
        .tb-value { font-style: italic; color: #333; }

        .ttd-area { display: flex; gap: 30px; }
        .ttd-col { text-align: center; min-width: 130px; font-size: 8px; }
        .ttd-title { font-weight: bold; margin-bottom: 2px; line-height: 1.4; }
        .ttd-space { height: 45px; }
        .ttd-name { border-top: 1px solid #000; padding-top: 2px; font-weight: bold; min-width: 120px; display: inline-block; }
        .ttd-nip { color: #555; margin-top: 1px; }

        .footer-print { display: flex; justify-content: space-between; margin-top: 8px; padding-top: 4px; border-top: 1px solid #ccc; color: #888; font-size: 7px; }

        @media print {
            body { background: #fff; padding: 0; }
            .page { box-shadow: none; width: 100%; padding: 8mm 10mm; }
        }
    </style>
</head>
<body>
@php
    function rpFmtDed($n) { return 'Rp ' . number_format($n, 0, ',', '.'); }

    // Terbilang sederhana
    $terbilangArr = ['', 'satu','dua','tiga','empat','lima','enam','tujuh','delapan','sembilan','sepuluh','sebelas'];
    function terbilangDed($n) {
        global $terbilangArr;
        $n = (int) abs($n);
        if ($n < 12) return $terbilangArr[$n];
        if ($n < 20) return $terbilangArr[$n - 10] . ' belas';
        if ($n < 100) return $terbilangArr[(int)($n/10)] . ' puluh ' . $terbilangArr[$n % 10];
        if ($n < 200) return 'seratus ' . terbilangDed($n - 100);
        if ($n < 1000) return $terbilangArr[(int)($n/100)] . ' ratus ' . terbilangDed($n % 100);
        if ($n < 2000) return 'seribu ' . terbilangDed($n - 1000);
        if ($n < 1000000) return terbilangDed((int)($n/1000)) . ' ribu ' . terbilangDed($n % 1000);
        if ($n < 1000000000) return terbilangDed((int)($n/1000000)) . ' juta ' . terbilangDed($n % 1000000);
        return terbilangDed((int)($n/1000000000)) . ' miliar ' . terbilangDed($n % 1000000000);
    }

    $totalSpjNetto = $totalTabKhusus = $totalSimpWajib = $totalSimpSukarela = $totalAngsuran = $totalJmlKop = 0;
    $totalDplkSlawi = $totalDplkKem = $totalPinjBpd = $totalJmlBpd = 0;
    $totalBankTgr = $totalPremi = $totalDansos = $totalLain1 = $totalLain2 = $totalDenda = 0;
    $totalPotongan = $totalGajiBersih = 0;
@endphp

<div class="page">
    {{-- KOP --}}
    <div class="kop">
        @if(!empty($settingLogo ?? null))
            <img src="{{ $settingLogo }}" class="kop-logo" alt="Logo">
        @else
            <div class="kop-logo-placeholder">LOGO</div>
        @endif
        <div class="kop-text">
            <div class="kop-school">{{ $settingName ?? 'SMK NU 1 ISLAMIYAH KRAMAT' }}</div>
            <div class="kop-address">{{ $settingAddress ?? '' }}</div>
        </div>
    </div>

    {{-- JUDUL --}}
    <div class="judul">
        <h2>Daftar Potongan Gaji & Take Home Pay</h2>
        <p>Bulan : {{ strtoupper($monthName) }} {{ $year }}</p>
    </div>

    {{-- TABEL --}}
    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">NIPY</th>
                <th rowspan="2">Nama Pegawai</th>
                <th rowspan="2">Jabatan</th>
                <th rowspan="2">Netto SPJ (Rp)</th>
                <th colspan="5" class="th-kop">Kluster Koperasi (Rp)</th>
                <th colspan="4" class="th-bpd">Kluster BPD (Rp)</th>
                <th colspan="6" class="th-sek">Sektoral & Lainnya (Rp)</th>
                <th rowspan="2" class="th-total">Total Potongan (Rp)</th>
                <th rowspan="2" class="th-bersih">Gaji Bersih / THP (Rp)</th>
            </tr>
            <tr>
                <th class="th-kop">Tab.Khusus (8%)</th>
                <th class="th-kop">Simp.Wajib</th>
                <th class="th-kop">Simp.Sukarela</th>
                <th class="th-kop">Angsuran Kop.</th>
                <th class="th-kop">Jml Koperasi</th>
                <th class="th-bpd">DPLK Slawi</th>
                <th class="th-bpd">DPLK Kemantran</th>
                <th class="th-bpd">Pinj. BPD Jtg</th>
                <th class="th-bpd">Jml BPD</th>
                <th class="th-sek">Bank TGR</th>
                <th class="th-sek">BPJS Angg+</th>
                <th class="th-sek">Dansos</th>
                <th class="th-sek">Lainnya 1</th>
                <th class="th-sek">Lainnya 2</th>
                <th class="th-sek">Denda Finger</th>
            </tr>
        </thead>
        <tbody>
        @forelse($deductions as $i => $row)
        @php
            $totalSpjNetto     += $row['spj_netto'];
            $totalTabKhusus    += $row['tab_khusus'] ?? 0;
            $totalSimpWajib    += $row['simpanan_wajib'] ?? 0;
            $totalSimpSukarela += $row['simpanan_sukarela'] ?? 0;
            $totalAngsuran     += $row['angsuran_koperasi'] ?? 0;
            $totalJmlKop       += $row['jumlah_koperasi'] ?? 0;
            $totalDplkSlawi    += $row['dplk_slawi'] ?? 0;
            $totalDplkKem      += $row['dplk_kemantran'] ?? 0;
            $totalPinjBpd      += $row['pinjaman_bpd_jateng'] ?? 0;
            $totalJmlBpd       += $row['jumlah_bpd'] ?? 0;
            $totalBankTgr      += $row['bank_tgr'] ?? 0;
            $totalPremi        += $row['premi_bpjs_anggota'] ?? 0;
            $totalDansos       += $row['dansos'] ?? 0;
            $totalLain1        += $row['lainnya_1'] ?? 0;
            $totalLain2        += $row['lainnya_2'] ?? 0;
            $totalDenda        += $row['denda_fingerprint'] ?? 0;
            $totalPotongan     += $row['jumlah_potongan'] ?? 0;
            $totalGajiBersih   += $row['gaji_bersih'] ?? 0;
        @endphp
        <tr>
            <td class="center">{{ $i + 1 }}</td>
            <td>{{ $row['teacher_nipy'] }}</td>
            <td>{{ $row['teacher_name'] }}</td>
            <td>{{ $row['teacher_position'] }}</td>
            <td class="angka">{{ rpFmtDed($row['spj_netto']) }}</td>
            <td class="td-kop">{{ rpFmtDed($row['tab_khusus'] ?? 0) }}</td>
            <td class="td-kop">{{ rpFmtDed($row['simpanan_wajib'] ?? 0) }}</td>
            <td class="td-kop">{{ rpFmtDed($row['simpanan_sukarela'] ?? 0) }}</td>
            <td class="td-kop">{{ rpFmtDed($row['angsuran_koperasi'] ?? 0) }}</td>
            <td class="td-kop" style="font-weight:bold;">{{ rpFmtDed($row['jumlah_koperasi'] ?? 0) }}</td>
            <td class="td-bpd">{{ rpFmtDed($row['dplk_slawi'] ?? 0) }}</td>
            <td class="td-bpd">{{ rpFmtDed($row['dplk_kemantran'] ?? 0) }}</td>
            <td class="td-bpd">{{ rpFmtDed($row['pinjaman_bpd_jateng'] ?? 0) }}</td>
            <td class="td-bpd" style="font-weight:bold;">{{ rpFmtDed($row['jumlah_bpd'] ?? 0) }}</td>
            <td class="td-sek">{{ rpFmtDed($row['bank_tgr'] ?? 0) }}</td>
            <td class="td-sek">{{ rpFmtDed($row['premi_bpjs_anggota'] ?? 0) }}</td>
            <td class="td-sek">{{ rpFmtDed($row['dansos'] ?? 0) }}</td>
            <td class="td-sek">{{ rpFmtDed($row['lainnya_1'] ?? 0) }}</td>
            <td class="td-sek">{{ rpFmtDed($row['lainnya_2'] ?? 0) }}</td>
            <td class="td-sek">{{ rpFmtDed($row['denda_fingerprint'] ?? 0) }}</td>
            <td class="td-total">{{ rpFmtDed($row['jumlah_potongan'] ?? 0) }}</td>
            <td class="td-bersih">{{ rpFmtDed($row['gaji_bersih'] ?? 0) }}</td>
        </tr>
        @empty
        <tr><td colspan="22" style="text-align:center; padding:15px; font-style:italic;">Belum ada data potongan gaji untuk periode ini.</td></tr>
        @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="label" style="text-align:center;">J U M L A H</td>
                <td>{{ rpFmtDed($totalSpjNetto) }}</td>
                <td>{{ rpFmtDed($totalTabKhusus) }}</td>
                <td>{{ rpFmtDed($totalSimpWajib) }}</td>
                <td>{{ rpFmtDed($totalSimpSukarela) }}</td>
                <td>{{ rpFmtDed($totalAngsuran) }}</td>
                <td>{{ rpFmtDed($totalJmlKop) }}</td>
                <td>{{ rpFmtDed($totalDplkSlawi) }}</td>
                <td>{{ rpFmtDed($totalDplkKem) }}</td>
                <td>{{ rpFmtDed($totalPinjBpd) }}</td>
                <td>{{ rpFmtDed($totalJmlBpd) }}</td>
                <td>{{ rpFmtDed($totalBankTgr) }}</td>
                <td>{{ rpFmtDed($totalPremi) }}</td>
                <td>{{ rpFmtDed($totalDansos) }}</td>
                <td>{{ rpFmtDed($totalLain1) }}</td>
                <td>{{ rpFmtDed($totalLain2) }}</td>
                <td>{{ rpFmtDed($totalDenda) }}</td>
                <td>{{ rpFmtDed($totalPotongan) }}</td>
                <td class="td-bersih-total">{{ rpFmtDed($totalGajiBersih) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- TERBILANG & TTD --}}
    <div class="bottom-section">
        <div class="terbilang-box">
            <div class="tb-label">Total Gaji Bersih (THP) :</div>
            <div class="tb-netto">{{ rpFmtDed($totalGajiBersih) }},-</div>
            <div class="tb-label" style="margin-top:4px;">Terbilang :</div>
            <div class="tb-value">
                {{ ucwords(trim(terbilangDed($totalGajiBersih))) }} Rupiah
            </div>
        </div>

        <div class="ttd-area">
            <div class="ttd-col">
                <div class="ttd-title">Mengetahui,<br>Kepala Sekolah</div>
                <div class="ttd-space"></div>
                <div class="ttd-name">___________________</div>
                <div class="ttd-nip">NIP. -</div>
            </div>
            <div class="ttd-col">
                <div class="ttd-title">{{ $settingCity ?? 'Tegal' }}, {{ now()->translatedFormat('d F Y') }}<br>Bendahara</div>
                <div class="ttd-space"></div>
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
