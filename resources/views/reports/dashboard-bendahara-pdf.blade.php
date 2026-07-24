<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Penggajian Terakhir</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; background: #fff; padding: 20px; }
        .page { background: #fff; width: 100%; margin: 0 auto; }
        @page { size: portrait; margin: 15mm; }
        
        .kop { display: flex; align-items: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 15px; }
        .kop-logo { width: 70px; height: 70px; object-fit: contain; margin-right: 15px; flex-shrink: 0; }
        .kop-text { flex: 1; text-align: center; }
        .kop-text h2 { font-size: 18px; text-transform: uppercase; margin-bottom: 5px; }
        .kop-text p { font-size: 12px; margin-bottom: 2px; }
        
        .report-title { text-align: center; margin-bottom: 15px; font-size: 14px; font-weight: bold; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 8px; }
        th { background: #f0f0f0; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .footer { display: flex; justify-content: flex-end; margin-top: 30px; }
        .signature { text-align: center; width: 200px; }
        .signature p { margin-bottom: 60px; }
        
        @media print {
            body { padding: 0; background: none; }
            .page { padding: 0; box-shadow: none; width: auto; min-height: auto; }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="kop">
            @if(isset($settingLogo) && $settingLogo)
                @php
                    $logoPath = public_path('storage/' . $settingLogo);
                    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
                    $logoMime = file_exists($logoPath) ? mime_content_type($logoPath) : '';
                @endphp
                @if($logoData)
                    <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Logo" class="kop-logo">
                @endif
            @endif
            <div class="kop-text">
                <h2>{{ $settingName ?? 'Sekolah' }}</h2>
                <p>{{ $settingAddress ?? '' }}</p>
            </div>
        </div>

        <div class="report-title">
            RIWAYAT PENGGAJIAN TERAKHIR<br>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Nama Pegawai</th>
                    <th width="15%">Periode</th>
                    <th width="15%">Gaji Pokok</th>
                    <th width="15%">Tunjangan</th>
                    <th width="15%">Gaji Bersih</th>
                    <th width="15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentSalaries as $index => $sal)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $sal->teacher ? $sal->teacher->name : '-' }}</td>
                        <td class="text-center">{{ str_pad($sal->month, 2, '0', STR_PAD_LEFT) }}/{{ $sal->year }}</td>
                        <td class="text-right">Rp {{ number_format($sal->base_salary, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($sal->allowance, 0, ',', '.') }}</td>
                        <td class="text-right font-bold">Rp {{ number_format($sal->net_salary, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $sal->status === 'paid' ? 'Lunas' : 'Proses' }}</td>
                    </tr>
                @endforeach
                @if(count($recentSalaries) == 0)
                    <tr>
                        <td colspan="7" class="text-center">Belum ada riwayat penggajian.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="footer">
            <div class="signature">
                <p>{{ date('d F Y') }}<br>Bendahara</p>
                <p style="text-decoration: underline; font-weight: bold;">( ........................................ )</p>
            </div>
        </div>
    </div>
</body>
</html>
