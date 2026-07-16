<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengajuan Gaji Perbulan - Tahun {{ $year }}</title>
</head>
<body>
    <table>
        <tr>
            <th colspan="4" style="text-align: center; font-size: 16px; font-weight: bold;">
                LAPORAN REKAPITULASI PENGAJUAN GAJI BULANAN
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; font-size: 14px; font-weight: bold;">
                TAHUN {{ $year }}
            </th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center;">
                {{ $commonData['settingName'] }}
            </th>
        </tr>
        <tr><td colspan="4"></td></tr>
    </table>

    <table border="1">
        <thead>
            <tr>
                <th style="background-color: #f0f0f0; font-weight: bold; text-align: center;">No</th>
                <th style="background-color: #f0f0f0; font-weight: bold; text-align: center;">Bulan</th>
                <th style="background-color: #f0f0f0; font-weight: bold; text-align: center;">Total Pegawai Diajukan</th>
                <th style="background-color: #f0f0f0; font-weight: bold; text-align: center;">Total Nominal Pengajuan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $grandTotal = 0;
            @endphp
            
            @foreach($monthlyData as $index => $data)
                @php $grandTotal += $data['total_amount']; @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $months[$data['month'] - 1] }} {{ $year }}</td>
                    <td style="text-align: center;">{{ $data['total_pegawai'] > 0 ? $data['total_pegawai'] : 0 }}</td>
                    <td style="text-align: right;">{{ number_format($data['total_amount'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align: right; font-weight: bold;">TOTAL KESELURUHAN PENGELUARAN TAHUN {{ $year }}</th>
                <th style="text-align: right; font-weight: bold;">{{ number_format($grandTotal, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
