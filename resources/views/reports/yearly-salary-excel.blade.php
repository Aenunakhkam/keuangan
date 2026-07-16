<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengajuan Gaji Pertahun</title>
</head>
<body>
    <table>
        <tr>
            <th colspan="4" style="text-align: center; font-size: 16px; font-weight: bold;">
                LAPORAN REKAPITULASI PENGAJUAN GAJI TAHUNAN
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
                <th style="background-color: #f0f0f0; font-weight: bold; text-align: center;">Tahun</th>
                <th style="background-color: #f0f0f0; font-weight: bold; text-align: center;">Total Pegawai Diajukan</th>
                <th style="background-color: #f0f0f0; font-weight: bold; text-align: center;">Total Nominal Pengajuan</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            
            @foreach($yearlyData as $index => $data)
                @php $grandTotal += $data['total_amount']; @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-align: center;">{{ $data['year'] }}</td>
                    <td style="text-align: center;">{{ $data['total_pegawai'] > 0 ? $data['total_pegawai'] : 0 }}</td>
                    <td style="text-align: right;">{{ number_format($data['total_amount'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align: right; font-weight: bold;">TOTAL KESELURUHAN PENGELUARAN</th>
                <th style="text-align: right; font-weight: bold;">{{ number_format($grandTotal, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
