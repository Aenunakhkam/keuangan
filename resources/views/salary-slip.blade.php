<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $salary->teacher->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 15mm;
            }
            body { 
                background: white; 
                padding: 0;
            }
            #printBtn { display: none; }
            .no-print { display: none; }
            .main-container {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                margin: 0 !important;
                width: 100% !important;
            }
        }

        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .slip-title {
            text-decoration: underline;
            text-underline-offset: 4px;
        }

        .label-cell {
            width: 140px;
            color: #666;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .value-cell {
            font-weight: 700;
            color: #000;
            font-size: 0.85rem;
        }

        .section-header {
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            padding: 8px 12px;
            color: #1e293b;
        }

        .amount-cell {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 700;
        }
    </style>
</head>
<body class="py-10 flex justify-center">
    
    <div class="main-container bg-white w-[210mm] min-h-[148mm] p-10 shadow-xl border border-gray-200 rounded-sm overflow-hidden relative">
        <!-- Watermark or Decorative element if needed -->
        
        <!-- Kop Surat -->
        <div class="kop-surat flex items-center justify-between">
            <div class="w-20">
                @if($settingLogo)
                    <img src="{{ asset('storage/' . $settingLogo) }}" alt="Logo Sekolah" class="w-full object-contain">
                @else
                    <div class="w-20 h-20 bg-gray-50 flex items-center justify-center border border-dashed border-gray-300 text-[10px] text-gray-400 font-bold uppercase">Logo</div>
                @endif
            </div>
            <div class="flex-1 text-center px-4">
                <h1 class="text-xl font-bold uppercase leading-tight text-gray-900">{{ $settingName ?? 'NAMA SEKOLAH' }}</h1>
                <p class="text-[10px] text-gray-500 mt-1">{{ $settingAddress ?? 'Alamat Sekolah Belum Diatur' }}</p>
                <p class="text-[9px] text-gray-400 font-medium">Telepon: (021) 12345678 | Email: info@sekolah.sch.id</p>
            </div>
            <div class="w-20 text-right">
                <div class="inline-block border-2 border-gray-900 px-3 py-1 font-black text-sm uppercase tracking-tighter">
                    OFFICIAL
                </div>
            </div>
        </div>

        <!-- Judul Document -->
        <div class="text-center mb-8">
            <h2 class="text-lg font-black uppercase tracking-widest slip-title">SLIP GAJI KARYAWAN</h2>
            <div class="text-xs font-bold text-gray-600 mt-2">
                Periode: {{ Carbon\Carbon::create()->month($salary->month)->translatedFormat('F') }} {{ $salary->year }}
            </div>
        </div>

        <!-- Data Pegawai -->
        <div class="grid grid-cols-2 gap-x-12 gap-y-2 mb-8 bg-gray-50/50 p-4 rounded-lg border border-gray-100">
            <div class="flex items-center">
                <span class="label-cell">Nama Lengkap</span>
                <span class="mr-2">:</span>
                <span class="value-cell uppercase">{{ $salary->teacher->name }}</span>
            </div>
            <div class="flex items-center">
                <span class="label-cell">Jam Mengajar</span>
                <span class="mr-2">:</span>
                <span class="value-cell">{{ $salary->teacher->teaching_hours }} Jam</span>
            </div>
            <div class="flex items-center">
                <span class="label-cell">NIP / ID</span>
                <span class="mr-2">:</span>
                <span class="value-cell">{{ $salary->teacher->nip ?? '-' }}</span>
            </div>
            <div class="flex items-center">
                <span class="label-cell">Rate Per Jam</span>
                <span class="mr-2">:</span>
                <span class="value-cell italic text-gray-500">Rp {{ number_format($teachingRate, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center">
                <span class="label-cell">Jabatan</span>
                <span class="mr-2">:</span>
                <span class="value-cell">
                    @if($salary->teacher->positions->count() > 0)
                        {{ $salary->teacher->positions->pluck('name')->join(', ') }}
                    @else
                        Guru Mata Pelajaran
                    @endif
                </span>
            </div>
            @if($salary->payment_date)
            <div class="flex items-center">
                <span class="label-cell font-bold text-indigo-700">Tgl Pembayaran</span>
                <span class="mr-2">:</span>
                <span class="value-cell text-indigo-700">{{ Carbon\Carbon::parse($salary->payment_date)->translatedFormat('d F Y') }}</span>
            </div>
            @endif
        </div>

        <!-- Main Content (Earnings & Deductions) -->
        <div class="grid grid-cols-2 gap-0 border border-gray-200">
            <!-- Earnings Section -->
            <div class="border-r border-gray-200 min-h-[250px]">
                <div class="section-header">A. Penerimaan (Earnings)</div>
                <div class="p-4 space-y-3">
                    <div class="flex justify-between text-xs items-center">
                        <span class="text-gray-600 font-medium italic">Honor Mengajar</span>
                        <span class="font-bold tabular-nums">Rp {{ number_format($salary->base_salary, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs items-center">
                        <span class="text-gray-600 font-medium italic">Tunjangan Jabatan & Kes.</span>
                        <span class="font-bold tabular-nums">Rp {{ number_format($salary->allowance, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs items-center">
                        <span class="text-gray-600 font-medium italic">
                            Transportasi ({{ $salary->days_present }} hari)
                        </span>
                        <span class="font-bold tabular-nums">Rp {{ number_format($salary->transport_allowance, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="mt-auto p-4 border-t border-gray-100 bg-gray-50/30">
                    <div class="flex justify-between text-xs font-black uppercase text-gray-900">
                        <span>Total (A)</span>
                        <span>Rp {{ number_format($salary->base_salary + $salary->allowance + $salary->transport_allowance, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Deductions Section -->
            <div class="min-h-[250px] flex flex-col">
                <div class="section-header">B. Potongan (Deductions)</div>
                <div class="p-4 space-y-3">
                    @if($salary->deduction > 0)
                    <div class="flex justify-between text-xs items-center">
                        <span class="text-gray-600 font-medium italic">{{ $salary->deduction_description ?? 'Potongan Lain-lain' }}</span>
                        <span class="font-bold text-rose-600 tabular-nums">Rp {{ number_format($salary->deduction, 0, ',', '.') }}</span>
                    </div>
                    @else
                    <div class="text-[10px] text-gray-300 italic text-center py-4">Tidak ada potongan.</div>
                    @endif
                </div>
                <div class="mt-auto p-4 border-t border-gray-100 bg-gray-50/30">
                    <div class="flex justify-between text-xs font-black uppercase text-gray-900">
                        <span>Total (B)</span>
                        <span class="text-rose-600">Rp {{ number_format($salary->deduction, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Take Home Pay Section -->
        <div class="mt-4 bg-[#1e293b] text-white p-6 rounded-b-lg flex justify-between items-center shadow-inner">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 block mb-1">Gaji Bersih Diterima (Take Home Pay)</span>
                <span class="text-[10px] italic text-gray-300 block">
                    Terbilang: * {{ ucwords((new NumberFormatter("id", NumberFormatter::SPELLOUT))->format($salary->net_salary)) }} Rupiah *
                </span>
            </div>
            <div class="text-3xl font-black tabular-nums">
                Rp {{ number_format($salary->net_salary, 0, ',', '.') }}
            </div>
        </div>

        <!-- Signature Section -->
        <div class="mt-16 grid grid-cols-2 gap-10 text-center text-[11px]">
            <div>
                <p class="text-gray-500 font-medium mb-20 uppercase tracking-tighter">Tanda Tangan Penerima,</p>
                <p class="font-bold text-gray-900 border-b border-gray-900 inline-block px-10 pb-1 uppercase italic tracking-wide">{{ $salary->teacher->name }}</p>
            </div>
            <div>
                <p class="text-gray-500 font-medium mb-3">{{ now()->translatedFormat('d F Y') }}</p>
                <p class="text-gray-500 font-medium mb-16 uppercase tracking-tighter">Mengetahui, Bendahara</p>
                <p class="font-bold text-gray-900 border-b border-gray-900 inline-block px-10 pb-1 uppercase italic tracking-wide">
                    {{ Auth::user() ? Auth::user()->name : 'BENDAHARA SEKOLAH' }}
                </p>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-12 text-center no-print" id="printBtn">
            <button onclick="window.print()" class="px-8 py-3 bg-[#1e293b] text-white rounded-xl text-sm font-black shadow-xl hover:scale-105 active:scale-95 transition-all flex items-center justify-center space-x-2 mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Slip Gaji Resmi</span>
            </button>
            <p class="text-[10px] text-gray-400 mt-4 italic font-medium">Dokumen ini merupakan bukti pembayaran resmi yang sah secara digital.</p>
        </div>

        <!-- Decorative Footer -->
        <div class="absolute bottom-4 left-0 w-full text-center pointer-events-none opacity-10 font-black text-[60px] uppercase -z-10 select-none">
            {{ $settingName ?? 'CONFIDENTIAL' }}
        </div>
    </div>

</body>
</html>
