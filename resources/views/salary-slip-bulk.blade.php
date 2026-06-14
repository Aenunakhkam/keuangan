<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Gaji Massal - Keuangan Sekolah</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Arial:wght@400;700&display=swap');
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
            font-size: 11px;
            color: #000;
        }

        .main-container {
            background: white;
            width: 210mm;
            height: 138mm;
            margin: 0 auto;
            padding: 15px 30px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            position: relative;
        }

        .cutting-line {
            width: 210mm;
            margin: 15px auto;
            text-align: center;
            border-top: 2px dashed #000;
            color: #000;
            font-weight: bold;
            font-size: 10px;
            padding-top: 5px;
            box-sizing: border-box;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }
            html, body {
                background: white;
                margin: 0;
                padding: 0;
                width: 210mm;
            }
            #printBtn { display: none; }
            .no-print { display: none !important; }
            .main-container {
                box-shadow: none !important;
                margin: 0 auto !important;
                padding: 10mm 15mm !important;
                width: 210mm !important;
                height: 138mm !important;
                box-sizing: border-box !important;
                page-break-inside: avoid !important;
            }
            .cutting-line {
                border-top: 2px dashed #000 !important;
                margin: 4mm auto !important;
                text-align: center !important;
                color: #000 !important;
                font-weight: bold !important;
                font-size: 10px !important;
                width: 210mm !important;
                display: block !important;
            }
            .page-break {
                page-break-after: always;
                clear: both;
            }
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 60px;
            height: auto;
            margin-right: 15px;
        }

        .school-info h1 {
            font-size: 13px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }

        .school-info p {
            font-size: 11px;
            margin: 0;
            font-style: italic;
        }

        .header-right {
            text-align: right;
        }

        .header-right h2 {
            font-size: 13px;
            font-weight: bold;
            margin: 0 0 5px 0;
            text-decoration: underline;
        }

        .header-right p {
            font-size: 11px;
            font-weight: bold;
            margin: 0;
            font-style: italic;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 3px;
            margin-bottom: 3px;
        }

        .info-col {
            display: flex;
        }

        .info-label {
            width: 80px;
        }

        .info-value {
            font-weight: bold;
        }

        table.salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        table.salary-table th {
            border-bottom: 2px solid #000;
            text-align: left;
            padding: 3px 0;
            font-size: 11px;
        }

        table.salary-table td {
            padding: 1.5px 0;
            vertical-align: top;
        }

        .col-pendapatan {
            width: 35%;
            padding-right: 20px !important;
        }

        .col-potongan {
            width: 65%;
            padding-left: 20px !important;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
        }

        .item-label {
            text-transform: uppercase;
        }

        .item-value {
            text-align: right;
            display: flex;
            justify-content: space-between;
            width: 90px;
        }

        .item-value span:first-child {
            margin-right: 5px;
        }

        .total-row {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 5px 0;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }
        
        .total-col {
            display: flex;
            justify-content: space-between;
            text-transform: uppercase;
        }

        .netto-box {
            display: inline-block;
            border: 2px solid #000;
            padding: 2px 10px;
            font-weight: bold;
            font-size: 11px;
            margin-left: 5px;
        }

        .signature-area {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }

        .signature-box {
            text-align: center;
            width: 250px;
        }

        .btn-print {
            background-color: #1e293b;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            margin: 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
        }

        .btn-print:hover {
            background-color: #0f172a;
            transform: scale(1.02);
        }
    </style>
</head>
<body>

    <!-- Tombol Cetak -->
    <div class="no-print" id="printBtn">
        <button onclick="window.print()" class="btn-print">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg>
            Cetak Semua Slip Gaji (Bulk)
        </button>
    </div>

    @php
        function formatRp($value) {
            if (!$value || $value == 0) return '<span>Rp</span><span>-</span>';
            return '<span>Rp</span><span>' . number_format($value, 0, ',', '.') . '</span>';
        }
    @endphp

    @foreach($salaries as $index => $salary)
        @php
            $spjNetto = $salary->salaryDeduction ? $salary->salaryDeduction->spj_netto : ($salary->base_salary + $salary->allowance);
            $deduction = $salary->salaryDeduction;
            $totalPotongan = $deduction ? $deduction->jumlah_potongan : 0;
            $gajiBersih = $deduction ? $deduction->gaji_bersih : $spjNetto;
        @endphp

        <!-- Slip Gaji Container -->
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <div class="header-left">
                    @if($settingLogo)
                        @php
                            $logoSrc = $settingLogo;
                            if (!str_starts_with($settingLogo, 'http')) {
                                $path = str_replace(['/storage/', 'storage/'], '', $settingLogo);
                                $fullPath = storage_path('app/public/' . ltrim($path, '/'));
                                if (file_exists($fullPath)) {
                                    $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                                    $data = file_get_contents($fullPath);
                                    $logoSrc = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                } else {
                                    $logoSrc = asset('storage/' . ltrim($path, '/'));
                                }
                            }
                        @endphp
                        <img src="{{ $logoSrc }}" alt="Logo" class="logo" onerror="this.style.display='none'">
                    @endif
                    <div class="school-info">
                        <h1>{{ $settingName ?? 'SMK NU 1 ISLAMIYAH' }}</h1>
                        <p>{{ $settingAddress ?? 'Jl. Garuda No. 39 - Kemantran' }}</p>
                    </div>
                </div>
                <div class="header-right">
                    <h2>SLIP GAJI</h2>
                    @php
                        \Carbon\Carbon::setLocale('id');
                        $date = $salary->payment_date 
                            ? \Carbon\Carbon::parse($salary->payment_date) 
                            : ($salary->paid_at ? \Carbon\Carbon::parse($salary->paid_at) : now());
                    @endphp
                    <p>{{ $date->translatedFormat('d F Y') }}</p>
                </div>
            </div>

            <!-- Info Pegawai -->
            <div class="info-row">
                <div class="info-col" style="flex: 1;">
                    <div class="info-label">Nama / NIK</div>
                    <div>: <span class="info-value">{{ $salary->teacher->name }} {{ $salary->teacher->nipy ? '(' . $salary->teacher->nipy . ')' : '' }}</span></div>
                </div>
                <div class="info-col" style="width: 250px;">
                    <div class="info-label" style="width: 60px;">Jabatan</div>
                    <div>: <span class="info-value">
                        @if($salary->teacher->positions->count() > 0)
                            {{ $salary->teacher->positions->pluck('name')->join(', ') }}
                        @else
                            Guru Mata Pelajaran
                        @endif
                    </span></div>
                </div>
            </div>

            <!-- Tabel Gaji -->
            <table class="salary-table">
                <thead>
                    <tr>
                        <th class="col-pendapatan">PENDAPATAN</th>
                        <th class="col-potongan">POTONGAN</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <!-- Kolom Pendapatan -->
                        <td class="col-pendapatan">
                            <div class="item-row" style="margin-bottom: 20px;">
                                <span class="item-label">NETTO</span>
                                <span class="item-value">{!! formatRp($spjNetto) !!}</span>
                            </div>
                        </td>

                        <!-- Kolom Potongan -->
                        <td class="col-potongan">
                            @if($deduction)
                            <div class="item-row">
                                <span class="item-label">TAB.KHUSUS/SHR</span>
                                <span class="item-value">{!! formatRp($deduction->tab_khusus) !!}</span>
                            </div>
                            <div class="item-row">
                                <span class="item-label">SIMPANAN WAJIB</span>
                                <span class="item-value">{!! formatRp($deduction->simpanan_wajib) !!}</span>
                            </div>
                            <div class="item-row">
                                <span class="item-label">SIMPANAN MANA SUKA</span>
                                <span class="item-value">{!! formatRp($deduction->simpanan_sukarela) !!}</span>
                            </div>
                            <div class="item-row">
                                <span class="item-label">ANGSURAN PINJAMAN KOPERASI</span>
                                <span class="item-value">{!! formatRp($deduction->angsuran_koperasi) !!}</span>
                            </div>
                            <div class="item-row">
                                <span class="item-label">DPLK BPD SLAWI</span>
                                <span class="item-value">{!! formatRp($deduction->dplk_slawi) !!}</span>
                            </div>
                            <div class="item-row">
                                <span class="item-label">DPLK BPD KEMANTRAN</span>
                                <span class="item-value">{!! formatRp($deduction->dplk_kemantran) !!}</span>
                            </div>
                            <div class="item-row">
                                <span class="item-label">PINJ. B. JATENG (KEMANTRAN)</span>
                                <span class="item-value">{!! formatRp($deduction->pinjaman_bpd_jateng) !!}</span>
                            </div>
                            <div class="item-row">
                                <span class="item-label">BANK TGR</span>
                                <span class="item-value">{!! formatRp($deduction->bank_tgr) !!}</span>
                            </div>
                            <div class="item-row">
                                <span class="item-label">PRMI BPJS ANGG +</span>
                                <span class="item-value">{!! formatRp($deduction->premi_bpjs_anggota) !!}</span>
                            </div>
                            <div class="item-row">
                                <span class="item-label">DANSOS</span>
                                <span class="item-value">{!! formatRp($deduction->dansos) !!}</span>
                            </div>
                            <div class="item-row">
                                <span class="item-label">LAINNYA (fingerprint)</span>
                                <span class="item-value">{!! formatRp($deduction->denda_fingerprint) !!}</span>
                            </div>
                            <div class="item-row">
                                <span class="item-label">LAINNYA</span>
                                <span class="item-value">{!! formatRp($deduction->lainnya_1) !!}</span>
                            </div>
                            <div class="item-row">
                                <span class="item-label">LAINNYA</span>
                                <span class="item-value">{!! formatRp($deduction->lainnya_2) !!}</span>
                            </div>
                            @else
                            <div style="font-style: italic; color: #666; text-align: center; padding: 20px;">
                                Data potongan belum diinput.
                            </div>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Total -->
            <div class="total-row">
                <div class="total-col" style="width: 35%; padding-right: 20px;">
                    <span>JUMLAH PENDAPATAN</span>
                    <span class="item-value">{!! formatRp($spjNetto) !!}</span>
                </div>
                <div class="total-col" style="width: 65%; padding-left: 20px;">
                    <span>JUMLAH POTONGAN</span>
                    <span class="item-value">{!! formatRp($totalPotongan) !!}</span>
                </div>
            </div>

            <!-- Take Home Pay & Signatures -->
            <div class="signature-area">
                <div>
                    <div style="display: flex; align-items: center; margin-bottom: 5px;">
                        <span style="font-weight: bold; font-size: 11px;">GAJI BERSIH &nbsp;&nbsp;&nbsp;:</span>
                        <span class="netto-box">Rp {{ number_format($gajiBersih, 0, ',', '.') }}</span>
                    </div>
                    <div style="font-style: italic; font-size: 10px; margin-left: 80px;">
                        ( {{ ucwords((new NumberFormatter("id", NumberFormatter::SPELLOUT))->format($gajiBersih)) }} Rupiah )
                    </div>
                </div>
                <div class="signature-box">
                    <div style="margin-bottom: 50px;">Bendahara,</div>
                    <div style="font-weight: bold;">{{ Auth::user() ? Auth::user()->name : 'Tina Agustina' }}</div>
                </div>
            </div>

            <!-- Footer Branding -->
            <div style="margin-top: 15px; border-top: 1px dashed #ccc; padding-top: 3px; display: flex; justify-content: space-between; align-items: center; font-size: 8px; color: #666; font-style: italic;">
                <span>{{ $settingFooter }}</span>
                <span>Waktu Cetak: {{ now()->translatedFormat('d/m/Y H:i:s') }}</span>
            </div>
        </div>

        <!-- Garis Pemotong jika ini slip pertama di halaman A4 -->
        @if($index % 2 === 0 && !$loop->last)
            <div class="cutting-line">
                ✂️ - - - - - - - - - - - - - - - - - - - - - GUNTING DI SINI - - - - - - - - - - - - - - - - - - - - -
            </div>
        @endif

        <!-- Pindah Halaman setelah slip kedua -->
        @if($index % 2 === 1 && !$loop->last)
            <div class="page-break"></div>
        @endif

    @endforeach

</body>
</html>
