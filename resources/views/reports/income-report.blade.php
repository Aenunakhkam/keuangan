<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemasukan - {{ $year }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Times+New+Roman&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 15mm;
            }
            body { 
                background: white; 
                -webkit-print-color-adjust: exact;
            }
            #printBtn { display: none; }
            .no-print { display: none; }
            .print-border { border: 1px solid #000 !important; }
        }

        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .table-formal th {
            background-color: #f3f4f6 !important;
            color: #000 !important;
            border: 1px solid #000;
        }

        .table-formal td {
            border: 1px solid #000;
        }

        .text-formal {
            font-family: 'Times New Roman', serif;
        }
    </style>
</head>
<body class="bg-gray-100 py-10">
    
    <div class="bg-white mx-auto w-[297mm] min-h-[210mm] p-[15mm] shadow-lg border border-gray-300">
        <!-- Kop Surat -->
        <div class="kop-surat flex items-center justify-between">
            <div class="w-24">
                @if($settingLogo)
                    <img src="{{ asset('storage/' . $settingLogo) }}" alt="Logo Sekolah" class="w-full object-contain">
                @else
                    <div class="w-full h-24 bg-gray-100 flex items-center justify-center border border-dashed border-gray-400 text-[10px] text-gray-400 font-bold uppercase">Logo</div>
                @endif
            </div>
            <div class="flex-1 text-center px-6">
                <h1 class="text-2xl font-bold uppercase tracking-tight text-gray-900 leading-tight">{{ $settingName ?? 'YAYASAN PENDIDIKAN SEKOLAH' }}</h1>
                <h2 class="text-xl font-black uppercase text-gray-800 mb-1">UNIT KEUANGAN & BENDAHARA</h2>
                <p class="text-[11px] text-gray-600 italic">{{ $settingAddress ?? 'Alamat Lengkap Sekolah Belum Diatur' }}</p>
                <p class="text-[10px] text-gray-500 font-medium">Telepon: (021) 12345678 | Email: admin@sekolah.sch.id</p>
            </div>
            <div class="w-24"></div> <!-- Balancing space -->
        </div>

        <!-- Judul Laporan -->
        <div class="text-center mb-10">
            <h3 class="text-lg font-black underline uppercase tracking-widest decoration-2 underline-offset-4">LAPORAN REKAPITULASI PEMASUKAN</h3>
            <p class="text-sm font-bold text-gray-700 mt-2">
                Periode: {{ $month ? Carbon\Carbon::create()->month($month)->translatedFormat('F') : 'Semua Bulan' }} {{ $year }}
            </p>
        </div>

        <!-- Detail Tabel Laporan -->
        <div class="space-y-8">
            @php $globalIndex = 1; @endphp
            @foreach($income as $category => $items)
                <div class="break-inside-avoid">
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="w-2 h-4 bg-emerald-800"></span>
                        <h4 class="text-xs font-black uppercase tracking-widest text-gray-900">Kategori: {{ $category }}</h4>
                    </div>
                    <table class="w-full text-[12px] table-formal border-collapse">
                        <thead>
                            <tr class="text-center font-bold">
                                <th class="px-3 py-2 w-10">NO</th>
                                <th class="px-3 py-2 w-24">TANGGAL</th>
                                <th class="px-3 py-2">URAIAN / KETERANGAN</th>
                                <th class="px-3 py-2 w-32">NOMINAL (RP)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($items as $index => $item)
                                <tr>
                                    <td class="px-3 py-2 text-center">{{ $globalIndex++ }}</td>
                                    <td class="px-3 py-2 text-center whitespace-nowrap">{{ Carbon\Carbon::parse($item->transaction_date)->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2 leading-tight uppercase">{{ $item->description }}</td>
                                    <td class="px-3 py-2 text-right font-bold tabular-nums text-emerald-700">{{ number_format($item->amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 font-black">
                            <tr>
                                <td colspan="3" class="px-3 py-2 text-right uppercase tracking-tighter text-[10px]">Subtotal {{ $category }}</td>
                                <td class="px-3 py-2 text-right bg-emerald-50 underline decoration-double text-emerald-800">Rp {{ number_format($items->sum('amount'), 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endforeach
        </div>

        <!-- Grand Total Section -->
        <div class="mt-10 border-t-2 border-black pt-4 flex justify-between items-start">
            <div>
                <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Catatan Tambahan:</p>
                <p class="text-[10px] text-gray-500 italic">* Laporan ini dihasilkan secara otomatis oleh Sistem Manajemen Keuangan Sekolah.</p>
            </div>
            <div class="text-right">
                <span class="text-xs font-black uppercase tracking-widest mr-4 text-emerald-600">Total Seluruh Pemasukan:</span>
                <span class="text-xl font-black border-b-4 border-double border-emerald-900 tabular-nums text-emerald-900">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Tanda Tangan -->
        <div class="mt-20 grid grid-cols-2 gap-20 text-center text-sm">
            <div class="break-inside-avoid">
                <p class="mb-24">Mengetahui,<br><span class="font-bold">Kepala Sekolah</span></p>
                <div class="w-48 border-b border-black mx-auto mb-1"></div>
                <p class="text-xs uppercase font-bold text-gray-400 tracking-widest">( Nama Kepala Sekolah )</p>
            </div>
            <div class="break-inside-avoid">
                <p class="mb-4">{{ now()->translatedFormat('d F Y') }}</p>
                <p class="mb-20">Bendahara Sekolah</p>
                <p class="font-black text-gray-900 uppercase border-b border-black mx-auto inline-block min-w-[200px]">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-gray-500 font-bold mt-1 uppercase tracking-widest">NIP: ............................</p>
            </div>
        </div>

        <!-- Print Buttons (Floating/Fixed) -->
        <div class="fixed bottom-8 right-8 flex flex-col space-y-3 no-print" id="printBtn">
            <button onclick="window.print()" class="px-6 py-4 bg-emerald-900 text-white rounded-full text-sm font-black shadow-2xl hover:scale-105 active:scale-95 transition-all flex items-center space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Portofolio Pemasukan</span>
            </button>
            <button onclick="window.close()" class="px-6 py-4 bg-white text-gray-500 border border-gray-200 rounded-full text-sm font-bold shadow-xl hover:bg-gray-50 transition-all text-center">
                Tutup Halaman
            </button>
        </div>
    </div>

    <!-- Page Shadow/Border Decorative for Screen only -->
    <div class="no-print mt-10 text-center text-xs text-gray-400 font-medium">
        &copy; {{ date('Y') }} Sistem Keuangan Sekolah - Dokumen Digital Resmi
    </div>
</body>
</html>
