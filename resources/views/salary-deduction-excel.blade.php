<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
@php
    function rpFmtXls($n) { return number_format((float)$n, 0, ',', '.'); }

    $totalSpjNetto = $totalTabKhusus = $totalSimpWajib = $totalSimpSukarela = $totalAngsuran = $totalJmlKop = 0;
    $totalDplkSlawi = $totalDplkKem = $totalPinjBpd = $totalJmlBpd = 0;
    $totalBankTgr = $totalPremi = $totalDansos = $totalLain1 = $totalLain2 = $totalDenda = 0;
    $totalPotongan = $totalGajiBersih = 0;
@endphp

    {{-- HEADER --}}
    <table>
        <tr>
            <td colspan="22" style="text-align:center; font-weight:bold; font-size:14pt;">
                DAFTAR POTONGAN GAJI &amp; TAKE HOME PAY
            </td>
        </tr>
        <tr>
            <td colspan="22" style="text-align:center; font-weight:bold; font-size:12pt;">
                Bulan : {{ strtoupper($monthName) }} {{ $year }}
            </td>
        </tr>
        <tr>
            <td colspan="22" style="text-align:center; font-size:11pt;">
                {{ $settingName ?? 'SMK NU 1 ISLAMIYAH KRAMAT' }}
            </td>
        </tr>
        <tr><td colspan="22"></td></tr>
    </table>

    {{-- TABEL DATA --}}
    <table border="1" style="border-collapse:collapse; font-size:9pt;">
        <thead>
            <tr>
                <th rowspan="2" style="background:#d9e1f2; text-align:center; vertical-align:middle; font-weight:bold;">No</th>
                <th rowspan="2" style="background:#d9e1f2; text-align:center; vertical-align:middle; font-weight:bold;">NIPY</th>
                <th rowspan="2" style="background:#d9e1f2; text-align:center; vertical-align:middle; font-weight:bold;">Nama Pegawai</th>
                <th rowspan="2" style="background:#d9e1f2; text-align:center; vertical-align:middle; font-weight:bold;">Jabatan</th>
                <th rowspan="2" style="background:#d9e1f2; text-align:center; vertical-align:middle; font-weight:bold;">Netto SPJ (Rp)</th>
                <th colspan="5" style="background:#c5d5f0; text-align:center; font-weight:bold;">Kluster Koperasi (Rp)</th>
                <th colspan="4" style="background:#c5e8d0; text-align:center; font-weight:bold;">Kluster BPD (Rp)</th>
                <th colspan="6" style="background:#fce8c5; text-align:center; font-weight:bold;">Sektoral &amp; Lainnya (Rp)</th>
                <th rowspan="2" style="background:#f5c5c5; text-align:center; vertical-align:middle; font-weight:bold;">Total Potongan (Rp)</th>
                <th rowspan="2" style="background:#c5f5d5; text-align:center; vertical-align:middle; font-weight:bold;">Gaji Bersih / THP (Rp)</th>
            </tr>
            <tr>
                <th style="background:#d9e8ff; text-align:center; font-weight:bold;">Tab.Khusus (8%)</th>
                <th style="background:#d9e8ff; text-align:center; font-weight:bold;">Simp.Wajib</th>
                <th style="background:#d9e8ff; text-align:center; font-weight:bold;">Simp.Sukarela</th>
                <th style="background:#d9e8ff; text-align:center; font-weight:bold;">Angsuran Kop.</th>
                <th style="background:#b8ccf5; text-align:center; font-weight:bold;">Jml Koperasi</th>
                <th style="background:#d9f0e8; text-align:center; font-weight:bold;">DPLK Slawi</th>
                <th style="background:#d9f0e8; text-align:center; font-weight:bold;">DPLK Kemantran</th>
                <th style="background:#d9f0e8; text-align:center; font-weight:bold;">Pinj.BPD Jtg</th>
                <th style="background:#b8e0c8; text-align:center; font-weight:bold;">Jml BPD</th>
                <th style="background:#fdf0d0; text-align:center; font-weight:bold;">Bank TGR</th>
                <th style="background:#fdf0d0; text-align:center; font-weight:bold;">BPJS Angg+</th>
                <th style="background:#fdf0d0; text-align:center; font-weight:bold;">Dansos</th>
                <th style="background:#fdf0d0; text-align:center; font-weight:bold;">Lainnya 1</th>
                <th style="background:#fdf0d0; text-align:center; font-weight:bold;">Lainnya 2</th>
                <th style="background:#fdf0d0; text-align:center; font-weight:bold;">Denda Finger</th>
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
            <td style="text-align:center;">{{ $i + 1 }}</td>
            <td>{{ $row['teacher_nipy'] }}</td>
            <td>{{ $row['teacher_name'] }}</td>
            <td>{{ $row['teacher_position'] }}</td>
            <td style="text-align:right;">{{ rpFmtXls($row['spj_netto']) }}</td>
            <td style="text-align:right; background:#f0f5ff;">{{ rpFmtXls($row['tab_khusus'] ?? 0) }}</td>
            <td style="text-align:right; background:#f0f5ff;">{{ rpFmtXls($row['simpanan_wajib'] ?? 0) }}</td>
            <td style="text-align:right; background:#f0f5ff;">{{ rpFmtXls($row['simpanan_sukarela'] ?? 0) }}</td>
            <td style="text-align:right; background:#f0f5ff;">{{ rpFmtXls($row['angsuran_koperasi'] ?? 0) }}</td>
            <td style="text-align:right; background:#dce8ff; font-weight:bold;">{{ rpFmtXls($row['jumlah_koperasi'] ?? 0) }}</td>
            <td style="text-align:right; background:#f0fff5;">{{ rpFmtXls($row['dplk_slawi'] ?? 0) }}</td>
            <td style="text-align:right; background:#f0fff5;">{{ rpFmtXls($row['dplk_kemantran'] ?? 0) }}</td>
            <td style="text-align:right; background:#f0fff5;">{{ rpFmtXls($row['pinjaman_bpd_jateng'] ?? 0) }}</td>
            <td style="text-align:right; background:#d0f0e0; font-weight:bold;">{{ rpFmtXls($row['jumlah_bpd'] ?? 0) }}</td>
            <td style="text-align:right; background:#fffaf0;">{{ rpFmtXls($row['bank_tgr'] ?? 0) }}</td>
            <td style="text-align:right; background:#fffaf0;">{{ rpFmtXls($row['premi_bpjs_anggota'] ?? 0) }}</td>
            <td style="text-align:right; background:#fffaf0;">{{ rpFmtXls($row['dansos'] ?? 0) }}</td>
            <td style="text-align:right; background:#fffaf0;">{{ rpFmtXls($row['lainnya_1'] ?? 0) }}</td>
            <td style="text-align:right; background:#fffaf0;">{{ rpFmtXls($row['lainnya_2'] ?? 0) }}</td>
            <td style="text-align:right; background:#fffaf0;">{{ rpFmtXls($row['denda_fingerprint'] ?? 0) }}</td>
            <td style="text-align:right; background:#fff0f0; font-weight:bold; color:#b71c1c;">{{ rpFmtXls($row['jumlah_potongan'] ?? 0) }}</td>
            <td style="text-align:right; background:#e8ffe8; font-weight:bold; color:#1b5e20;">{{ rpFmtXls($row['gaji_bersih'] ?? 0) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="22" style="text-align:center; padding:10px; font-style:italic;">Belum ada data potongan gaji untuk periode ini.</td>
        </tr>
        @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:center; font-weight:bold; background:#003B73; color:#fff;">J U M L A H</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalSpjNetto) }}</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalTabKhusus) }}</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalSimpWajib) }}</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalSimpSukarela) }}</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalAngsuran) }}</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalJmlKop) }}</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalDplkSlawi) }}</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalDplkKem) }}</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalPinjBpd) }}</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalJmlBpd) }}</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalBankTgr) }}</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalPremi) }}</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalDansos) }}</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalLain1) }}</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalLain2) }}</td>
                <td style="text-align:right; font-weight:bold; background:#003B73; color:#fff;">{{ rpFmtXls($totalDenda) }}</td>
                <td style="text-align:right; font-weight:bold; background:#b71c1c; color:#fff;">{{ rpFmtXls($totalPotongan) }}</td>
                <td style="text-align:right; font-weight:bold; background:#1b5e20; color:#fff;">{{ rpFmtXls($totalGajiBersih) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- BARIS KOSONG --}}
    <table>
        <tr><td colspan="6" style="height:16px;"></td></tr>
        <tr>
            <td colspan="6" style="font-size:10pt;">
                <b>Total Gaji Bersih (THP) : {{ rpFmtXls($totalGajiBersih) }}</b>
            </td>
        </tr>
        <tr><td colspan="6" style="height:16px;"></td></tr>

        {{-- TANDA TANGAN --}}
        <tr>
            <td style="width:200px; text-align:center; font-weight:bold;">Mengetahui,</td>
            <td style="width:50px;"></td>
            <td style="width:200px; text-align:center; font-weight:bold;">{{ $settingCity ?? 'Tegal' }}, {{ now()->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td style="text-align:center; font-weight:bold;">Kepala Sekolah</td>
            <td></td>
            <td style="text-align:center; font-weight:bold;">Bendahara</td>
        </tr>
        <tr>
            <td style="height:50px;"></td>
            <td></td>
            <td style="height:50px;"></td>
        </tr>
        <tr>
            <td style="text-align:center; border-top:1px solid #000; padding-top:3px;">___________________________</td>
            <td></td>
            <td style="text-align:center; border-top:1px solid #000; padding-top:3px;">___________________________</td>
        </tr>
        <tr>
            <td style="text-align:center; color:#555;">NIP. -</td>
            <td></td>
            <td style="text-align:center; color:#555;">NIP. -</td>
        </tr>
    </table>

</body>
</html>
