<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import debounce from 'lodash/debounce';

const props = defineProps<{
    transactions: any;
    summary: any;
    filters: any;
    categories: string[];
}>();

const start_date = ref(props.filters.start_date);
const end_date = ref(props.filters.end_date);
const type = ref(props.filters.type);
const category = ref(props.filters.category);

const updateFilters = debounce(() => {
    router.get(route('reports.transactions'), {
        start_date: start_date.value,
        end_date: end_date.value,
        type: type.value,
        category: category.value
    }, { preserveState: true, replace: true });
}, 300);

watch([start_date, end_date, type, category], updateFilters);

const printReport = () => {
    window.print();
};
</script>

<template>
    <Head title="Laporan Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between print:hidden">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Laporan Transaksi</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Filter dan urutkan laporan pemasukan & pengeluaran.</p>
                </div>
                <button 
                    @click="printReport"
                    class="px-5 py-3 bg-white text-indigo-700 border border-indigo-100 rounded-2xl text-sm font-black hover:bg-indigo-50 transition-all flex items-center space-x-2 shadow-sm shadow-indigo-500/10"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Laporan Resmi</span>
                </button>
            </div>

            <!-- Professional KOP Surat (Print Only) -->
            <div class="hidden print:block mb-6">
                <div class="flex items-center justify-between border-b-4 border-double border-gray-900 pb-3">
                    <div class="flex items-center space-x-4">
                        <img v-if="$page.props.settings?.logo_url" :src="$page.props.settings.logo_url" class="h-16 w-auto object-contain" />
                        <div class="text-left">
                            <h1 class="text-xl font-black text-gray-900 uppercase tracking-tight">{{ $page.props.settings?.school_name || 'NAMA SEKOLAH BELUM DIATUR' }}</h1>
                            <p class="text-sm font-bold text-gray-700 uppercase tracking-wider">{{ $page.props.settings?.school_motto || 'MENCERDASKAN BANGSA, MEMBANGUN NEGERI' }}</p>
                            <p class="text-[10px] text-gray-500 font-semibold leading-tight mt-0.5">
                                {{ $page.props.settings?.school_address || 'Alamat sekolah belum diatur di menu pengaturan.' }}<br>
                                Telp: {{ $page.props.settings?.school_phone || '-' }} | Email: {{ $page.props.settings?.school_email || '-' }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 text-center">
                    <h2 class="text-xl font-black uppercase underline tracking-widest text-gray-900 italic">LAPORAN TRANSAKSI KEUANGAN</h2>
                    <p class="text-xs font-bold mt-1">Periode: {{ start_date }} s/d {{ end_date }} | Dicetak: {{ new Date().toLocaleDateString('id-ID') }}</p>
                </div>
            </div>
        </template>

        <div class="space-y-8">
            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm print:hidden">
                <div class="space-y-1.5">
                    <InputLabel value="Tanggal Mulai" />
                    <TextInput v-model="start_date" type="date" class="block w-full" />
                </div>
                <div class="space-y-1.5">
                    <InputLabel value="Tanggal Akhir" />
                    <TextInput v-model="end_date" type="date" class="block w-full" />
                </div>
                <div class="space-y-1.5">
                    <InputLabel value="Tipe Transaksi" />
                    <select v-model="type" class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold">
                        <option value="all">Semua Tipe</option>
                        <option value="income">Pemasukan (+)</option>
                        <option value="expense">Pengeluaran (-)</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <InputLabel value="Kategori" />
                    <select v-model="category" class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold">
                        <option value="all">Semua Kategori</option>
                        <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                    </select>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-emerald-50 dark:bg-emerald-950/30 p-6 rounded-3xl border border-emerald-100 dark:border-emerald-900/50 print:p-3 print:border-gray-400">
                    <div class="text-[10px] font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 print:text-[8pt] print:text-gray-900 mb-1">Total Pemasukan</div>
                    <div class="text-2xl font-black text-emerald-700 dark:text-emerald-300 print:text-[14pt] print:text-gray-900">Rp {{ Number(summary.total_income).toLocaleString('id-ID') }}</div>
                </div>
                <div class="bg-rose-50 dark:bg-rose-950/30 p-6 rounded-3xl border border-rose-100 dark:border-rose-900/50 print:p-3 print:border-gray-400">
                    <div class="text-[10px] font-black uppercase tracking-widest text-rose-600 dark:text-rose-400 print:text-[8pt] print:text-gray-900 mb-1">Total Pengeluaran</div>
                    <div class="text-2xl font-black text-rose-700 dark:text-rose-300 print:text-[14pt] print:text-gray-900">Rp {{ Number(summary.total_expense).toLocaleString('id-ID') }}</div>
                </div>
                <div class="bg-indigo-50 dark:bg-indigo-950/30 p-6 rounded-3xl border border-indigo-100 dark:border-indigo-900/50 print:p-3 print:border-gray-400">
                    <div class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 print:text-[8pt] print:text-gray-900 mb-1">Saldo Neto</div>
                    <div class="text-2xl font-black text-indigo-700 dark:text-indigo-300 print:text-[14pt] print:text-gray-900 font-serif italic">Rp {{ Number(summary.balance).toLocaleString('id-ID') }}</div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden print:border-gray-900 print:rounded-none">
                <div class="overflow-x-auto">
                    <table class="w-full text-left print:text-[9pt] print:table-layout-fixed">
                        <thead class="print:display-table-header-group">
                            <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700 print:bg-gray-100 print:border-b-2 print:border-gray-900">
                                <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-widest text-center w-16 print:px-2 print:py-2 print:w-10 print:text-gray-900">No</th>
                                <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-widest print:px-2 print:py-2 print:w-28 print:text-gray-900">Tanggal</th>
                                <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-widest print:px-2 print:py-2 print:w-40 print:text-gray-900">Kategori</th>
                                <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-widest print:px-2 print:py-2 print:text-gray-900">Keterangan</th>
                                <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-widest text-right print:px-2 print:py-2 print:w-36 print:text-gray-900">Pemasukan</th>
                                <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-widest text-right print:px-2 print:py-2 print:w-36 print:text-gray-900">Pengeluaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700 print:divide-gray-900">
                            <tr v-for="(tx, index) in transactions" :key="tx.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors print:break-inside-avoid">
                                <td class="px-6 py-4 text-center text-sm font-bold text-gray-400 print:px-2 print:py-2 print:text-[8pt] print:text-gray-900">{{ index + 1 }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white print:px-2 print:py-2 print:text-gray-900">{{ tx.transaction_date }}</td>
                                <td class="px-6 py-4 text-sm font-black text-indigo-600 uppercase tracking-tight print:px-2 print:py-2 print:text-gray-900">{{ tx.category }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 font-medium print:px-2 print:py-2 print:text-gray-900">{{ tx.description || '-' }}</td>
                                <td class="px-6 py-4 text-right text-sm font-black text-emerald-600 print:px-2 print:py-2 print:text-gray-900">
                                    {{ tx.type === 'income' ? 'Rp ' + Number(tx.amount).toLocaleString('id-ID') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-black text-rose-600 print:px-2 print:py-2 print:text-gray-900">
                                    {{ tx.type === 'expense' ? 'Rp ' + Number(tx.amount).toLocaleString('id-ID') : '-' }}
                                </td>
                            </tr>
                            <tr v-if="transactions.length === 0">
                                <td colspan="6" class="px-6 py-20 text-center text-gray-500 font-medium">Tidak ada data transaksi pada periode ini.</td>
                            </tr>
                        </tbody>
                        <tfoot v-if="transactions.length > 0">
                            <tr class="bg-gray-50 dark:bg-gray-900/50 font-black print:bg-gray-50">
                                <td colspan="4" class="px-6 py-4 text-right uppercase tracking-widest text-xs print:px-2 print:py-2 print:text-[8pt] print:text-gray-900 print:border-t-2 print:border-gray-900">Total Akhir</td>
                                <td class="px-6 py-4 text-right text-emerald-600 print:px-2 print:py-2 print:text-gray-900 print:border-t-2 print:border-gray-900">Rp {{ Number(summary.total_income).toLocaleString('id-ID') }}</td>
                                <td class="px-6 py-4 text-right text-rose-600 print:px-2 print:py-2 print:text-gray-900 print:border-t-2 print:border-gray-900">Rp {{ Number(summary.total_expense).toLocaleString('id-ID') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Professional Signature Section (Print Only) -->
            <div class="hidden print:grid grid-cols-2 gap-20 mt-12 text-center font-bold">
                <div class="space-y-16">
                    <div>
                        <p class="mb-1 text-xs">Mengetahui,</p>
                        <p class="uppercase text-sm">Kepala Sekolah</p>
                    </div>
                    <div>
                        <p class="border-b-2 border-gray-900 inline-block px-8 min-w-[200px] text-sm">{{ $page.props.settings?.principal_name || '..........................................' }}</p>
                        <p class="text-[10px] mt-0.5">NIP: {{ $page.props.settings?.principal_nip || '..............................' }}</p>
                    </div>
                </div>
                <div class="space-y-16">
                    <div>
                        <p class="mb-1 text-xs">Dicetak oleh,</p>
                        <p class="uppercase text-sm">Bendahara Sekolah</p>
                    </div>
                    <div>
                        <p class="border-b-2 border-gray-900 inline-block px-8 min-w-[200px] text-sm">{{ $page.props.auth.user.name }}</p>
                        <p class="text-[10px] mt-0.5">Waktu Cetak: {{ new Date().toLocaleString('id-ID') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    @page {
        size: landscape;
        margin: 1.2cm;
    }
    
    body {
        background-color: white !important;
        color: black !important;
        font-family: "Times New Roman", serif !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .min-h-screen {
        min-height: auto !important;
        background-color: white !important;
    }

    main {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }

    nav, footer, .print\:hidden {
        display: none !important;
    }

    .bg-white, .bg-gray-50, .bg-emerald-50, .bg-rose-50, .bg-indigo-50 {
        background-color: transparent !important;
        border-color: #000 !important;
    }

    .text-indigo-600, .text-emerald-600, .text-rose-600 {
        color: black !important;
    }

    .rounded-3xl, .rounded-2xl, .rounded-xl {
        border-radius: 0 !important;
    }

    .shadow-sm, .shadow-xl, .shadow-indigo-500\/10 {
        box-shadow: none !important;
    }

    table {
        border-collapse: collapse !important;
        width: 100% !important;
        border: 1.5px solid #000 !important;
        table-layout: fixed !important;
    }

    th, td {
        border: 1px solid #000 !important;
        color: #000 !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
    }
    
    thead {
        display: table-header-group !important;
    }
    
    tr {
        page-break-inside: avoid !important;
    }
}
</style>
