<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <table>
        <tr>
            <td colspan="12" style="text-align: center; font-weight: bold; font-size: 14pt;">
                DAFTAR GAJI PEGAWAI / SPJ
            </td>
        </tr>
        <tr>
            <td colspan="12" style="text-align: center; font-weight: bold; font-size: 12pt;">
                Bulan : {{ strtoupper($monthName) }} {{ $year }}
            </td>
        </tr>
        <tr>
            <td colspan="12" style="text-align: center; font-size: 11pt;">
                {{ $settingName ?? 'SMK NU 1 ISLAMIYAH KRAMAT' }}
            </td>
        </tr>
        <tr>
            <td colspan="12"></td> <!-- baris kosong -->
        </tr>
    </table>

    <table border="1" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th rowspan="2" style="font-weight: bold; text-align: center; background-color: #d9e1f2; vertical-align: middle;">No</th>
                <th rowspan="2" style="font-weight: bold; text-align: center; background-color: #d9e1f2; vertical-align: middle;">Nama Pegawai</th>
                <th rowspan="2" style="font-weight: bold; text-align: center; background-color: #d9e1f2; vertical-align: middle;">Jabatan</th>
                <th rowspan="2" style="font-weight: bold; text-align: center; background-color: #d9e1f2; vertical-align: middle;">Gaji Pokok (Rp)</th>
                <th colspan="3" style="font-weight: bold; text-align: center; background-color: #d9e1f2;">Tunjangan (Rp)</th>
                <th rowspan="2" style="font-weight: bold; text-align: center; background-color: #d9e1f2; vertical-align: middle;">Jumlah Bruto (Rp)</th>
                <th colspan="3" style="font-weight: bold; text-align: center; background-color: #d9e1f2;">Potongan BPJS (Rp)</th>
                <th rowspan="2" style="font-weight: bold; text-align: center; background-color: #d9e1f2; vertical-align: middle;">Jumlah Netto (Rp)</th>
            </tr>
            <tr>
                <th style="font-weight: bold; text-align: center; background-color: #e9edf8;">Jabatan</th>
                <th style="font-weight: bold; text-align: center; background-color: #e9edf8;">BPJS</th>
                <th style="font-weight: bold; text-align: center; background-color: #e9edf8;">Jumlah</th>
                <th style="font-weight: bold; text-align: center; background-color: #e9edf8;">Kes</th>
                <th style="font-weight: bold; text-align: center; background-color: #e9edf8;">Naker</th>
                <th style="font-weight: bold; text-align: center; background-color: #e9edf8;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalGajiPokok     = 0;
                $totalTunjJabatan   = 0;
                $totalTunjBpjs      = 0;
                $totalJmlTunjangan  = 0;
                $totalBruto         = 0;
                $totalPotKes        = 0;
                $totalPotNaker      = 0;
                $totalPotongan      = 0;
                $totalNetto         = 0;
            @endphp

            @forelse($reportData as $i => $row)
                @php
                    $totalGajiPokok     += $row['gaji_pokok'];
                    $totalTunjJabatan   += $row['tunjangan_jabatan'];
                    $totalTunjBpjs      += $row['tunjangan_bpjs'];
                    $totalJmlTunjangan  += $row['jumlah_tunjangan'];
                    $totalBruto         += $row['jumlah_bruto'];
                    $totalPotKes        += $row['potongan_kes'];
                    $totalPotNaker      += $row['potongan_naker'];
                    $totalPotongan      += $row['jumlah_potongan'];
                    $totalNetto         += $row['jumlah_netto'];
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ $row['jabatan'] }}</td>
                    <td style="text-align: right;">{{ $row['gaji_pokok'] }}</td>
                    <td style="text-align: right;">{{ $row['tunjangan_jabatan'] }}</td>
                    <td style="text-align: right;">{{ $row['tunjangan_bpjs'] }}</td>
                    <td style="text-align: right;">{{ $row['jumlah_tunjangan'] }}</td>
                    <td style="text-align: right; background-color: #e8f5e9;">{{ $row['jumlah_bruto'] }}</td>
                    <td style="text-align: right;">{{ $row['potongan_kes'] }}</td>
                    <td style="text-align: right;">{{ $row['potongan_naker'] }}</td>
                    <td style="text-align: right; color: #b71c1c;">{{ $row['jumlah_potongan'] }}</td>
                    <td style="text-align: right; background-color: #fff3e0; font-weight: bold;">{{ $row['jumlah_netto'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align: center;">Belum ada data gaji untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: center; font-weight: bold; background-color: #003B73; color: #ffffff;">J U M L A H</td>
                <td style="text-align: right; font-weight: bold; background-color: #003B73; color: #ffffff;">{{ $totalGajiPokok }}</td>
                <td style="text-align: right; font-weight: bold; background-color: #003B73; color: #ffffff;">{{ $totalTunjJabatan }}</td>
                <td style="text-align: right; font-weight: bold; background-color: #003B73; color: #ffffff;">{{ $totalTunjBpjs }}</td>
                <td style="text-align: right; font-weight: bold; background-color: #003B73; color: #ffffff;">{{ $totalJmlTunjangan }}</td>
                <td style="text-align: right; font-weight: bold; background-color: #1b5e20; color: #ffffff;">{{ $totalBruto }}</td>
                <td style="text-align: right; font-weight: bold; background-color: #003B73; color: #ffffff;">{{ $totalPotKes }}</td>
                <td style="text-align: right; font-weight: bold; background-color: #003B73; color: #ffffff;">{{ $totalPotNaker }}</td>
                <td style="text-align: right; font-weight: bold; background-color: #003B73; color: #ffcdd2;">{{ $totalPotongan }}</td>
                <td style="text-align: right; font-weight: bold; background-color: #e65100; color: #ffffff;">{{ $totalNetto }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- BARIS KOSONG & TANDA TANGAN --}}
    <table>
        <tr><td colspan="6" style="height:16px;"></td></tr>
        <tr>
            <td colspan="6" style="font-size:10pt;">
                <b>Total Netto Keseluruhan : {{ number_format($totalNetto, 0, ',', '.') }}</b>
            </td>
        </tr>
        <tr><td colspan="6" style="height:16px;"></td></tr>

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
