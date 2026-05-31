<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps<{
    reportData: any[];
    filters: {
        month: number;
        year: number;
    };
}>();

const selectedMonth = ref(props.filters.month);
const selectedYear = ref(props.filters.year);

const months = [
    { value: 1, label: 'Januari' },
    { value: 2, label: 'Februari' },
    { value: 3, label: 'Maret' },
    { value: 4, label: 'April' },
    { value: 5, label: 'Mei' },
    { value: 6, label: 'Juni' },
    { value: 7, label: 'Juli' },
    { value: 8, label: 'Agustus' },
    { value: 9, label: 'September' },
    { value: 10, label: 'Oktober' },
    { value: 11, label: 'November' },
    { value: 12, label: 'Desember' }
];

const currentYear = new Date().getFullYear();
const years = Array.from({length: 5}, (_, i) => currentYear - 2 + i);

const filterReport = () => {
    router.get(route('reports.salary-spj'), {
        month: selectedMonth.value,
        year: selectedYear.value
    }, { preserveState: true, replace: true });
};

watch([selectedMonth, selectedYear], () => {
    filterReport();
});

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID').format(value);
};

const getMonthName = (m: number) => {
    return months.find(month => month.value == m)?.label.toUpperCase() || '';
};
</script>

<template>
    <Head title="Daftar Gaji SPJ" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Laporan Daftar Gaji SPJ</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Perincian Gaji Pokok, Tunjangan, dan BPJS.</p>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Filters -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Bulan</label>
                    <select v-model="selectedMonth" class="rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                        <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tahun</label>
                    <select v-model="selectedYear" class="rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                        <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                    </select>
                </div>
            </div>

            <!-- Header Laporan -->
            <div class="mb-2" id="print-section">
                <div class="text-sm font-bold text-gray-800 print:text-center print:text-xl print:mb-4">LAPORAN DAFTAR GAJI SPJ - BULAN : {{ getMonthName(selectedMonth) }} {{ selectedYear }}</div>
            </div>

            <!-- Tabel SPJ Modern -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden print-table-container">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead>
                            <tr class="bg-slate-100 border-b border-gray-200">
                                <th rowspan="2" class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest text-center w-12 border-r border-gray-200 whitespace-nowrap">No</th>
                                <th rowspan="2" class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest w-64 border-r border-gray-200 whitespace-nowrap">Nama Pegawai</th>
                                <th rowspan="2" class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest w-48 border-r border-gray-200 whitespace-nowrap">Jabatan</th>
                                <th colspan="8" class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-widest text-center border-b border-gray-200 whitespace-nowrap">Perincian Gaji & Tunjangan</th>
                            </tr>
                            <tr class="bg-slate-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-600 uppercase tracking-wider text-right border-r border-gray-200 whitespace-nowrap">Gaji Pokok</th>
                                <th class="px-0 py-0 border-r border-gray-200 align-top">
                                    <div class="border-b border-gray-200 px-4 py-2 text-[10px] font-bold text-slate-600 uppercase tracking-wider text-center bg-indigo-50/50 text-indigo-700 whitespace-nowrap">Tunjangan</div>
                                    <div class="grid grid-cols-3 divide-x divide-gray-200">
                                        <div class="px-4 py-2 text-[10px] font-bold text-slate-600 uppercase text-right whitespace-nowrap">Jabatan</div>
                                        <div class="px-4 py-2 text-[10px] font-bold text-slate-600 uppercase text-right whitespace-nowrap">BPJS</div>
                                        <div class="px-4 py-2 text-[10px] font-bold text-indigo-700 uppercase text-right bg-indigo-100/50 whitespace-nowrap">Jumlah</div>
                                    </div>
                                </th>
                                <th class="px-4 py-3 text-[10px] font-black text-slate-700 uppercase tracking-wider text-right border-r border-gray-200 bg-slate-100 whitespace-nowrap">Jml Bruto</th>
                                <th class="px-0 py-0 border-r border-gray-200 align-top">
                                    <div class="border-b border-gray-200 px-4 py-2 text-[10px] font-bold text-slate-600 uppercase tracking-wider text-center bg-rose-50/50 text-rose-700 whitespace-nowrap">Potongan BPJS</div>
                                    <div class="grid grid-cols-3 divide-x divide-gray-200">
                                        <div class="px-4 py-2 text-[10px] font-bold text-slate-600 uppercase text-right whitespace-nowrap">Kes</div>
                                        <div class="px-4 py-2 text-[10px] font-bold text-slate-600 uppercase text-right whitespace-nowrap">Naker</div>
                                        <div class="px-4 py-2 text-[10px] font-bold text-rose-700 uppercase text-right bg-rose-100/50 whitespace-nowrap">Jumlah</div>
                                    </div>
                                </th>
                                <th class="px-4 py-3 text-[10px] font-black text-emerald-700 uppercase tracking-wider text-right bg-emerald-50/50 whitespace-nowrap">Jml Netto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="reportData.length === 0">
                                <td colspan="12" class="px-6 py-20 text-center text-gray-500 font-bold italic">Belum ada data gaji untuk bulan ini.</td>
                            </tr>
                            <tr v-for="(row, index) in reportData" :key="row.id" class="hover:bg-slate-50 transition-colors group">
                                <td class="px-6 py-4 text-center font-bold text-slate-500 text-sm border-r border-gray-200 whitespace-nowrap">{{ index + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-slate-800 border-r border-gray-200 whitespace-nowrap">{{ row.nama }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 border-r border-gray-200 whitespace-nowrap">{{ row.jabatan }}</td>
                                
                                <!-- Gaji Pokok -->
                                <td class="px-4 py-4 text-right font-medium text-slate-700 border-r border-gray-200 whitespace-nowrap">
                                    {{ formatCurrency(row.gaji_pokok) }}
                                </td>
                                
                                <!-- Tunjangan -->
                                <td class="px-0 py-0 border-r border-gray-200 align-middle">
                                    <div class="grid grid-cols-3 divide-x divide-gray-200 h-full items-center">
                                        <div class="px-4 py-4 text-right text-sm text-slate-600 whitespace-nowrap">{{ row.tunjangan_jabatan > 0 ? formatCurrency(row.tunjangan_jabatan) : '-' }}</div>
                                        <div class="px-4 py-4 text-right text-sm text-slate-600 whitespace-nowrap">{{ row.tunjangan_bpjs > 0 ? formatCurrency(row.tunjangan_bpjs) : '-' }}</div>
                                        <div class="px-4 py-4 text-right text-sm font-bold text-indigo-700 bg-indigo-50/50 whitespace-nowrap">{{ row.jumlah_tunjangan > 0 ? formatCurrency(row.jumlah_tunjangan) : '-' }}</div>
                                    </div>
                                </td>
                                
                                <!-- Bruto -->
                                <td class="px-4 py-4 text-right font-black text-slate-800 border-r border-gray-200 bg-slate-50 whitespace-nowrap">
                                    {{ formatCurrency(row.jumlah_bruto) }}
                                </td>
                                
                                <!-- Potongan BPJS -->
                                <td class="px-0 py-0 border-r border-gray-200 align-middle">
                                    <div class="grid grid-cols-3 divide-x divide-gray-200 h-full items-center">
                                        <div class="px-4 py-4 text-right text-sm text-slate-600 whitespace-nowrap">{{ row.potongan_kes > 0 ? formatCurrency(row.potongan_kes) : '-' }}</div>
                                        <div class="px-4 py-4 text-right text-sm text-slate-600 whitespace-nowrap">{{ row.potongan_naker > 0 ? formatCurrency(row.potongan_naker) : '-' }}</div>
                                        <div class="px-4 py-4 text-right text-sm font-bold text-rose-600 bg-rose-50/50 whitespace-nowrap">{{ row.jumlah_potongan > 0 ? formatCurrency(row.jumlah_potongan) : '-' }}</div>
                                    </div>
                                </td>
                                
                                <!-- Netto -->
                                <td class="px-4 py-4 text-right font-black text-emerald-700 bg-emerald-50/50 whitespace-nowrap">
                                    {{ formatCurrency(row.jumlah_netto) }}
                                </td>
                            </tr>
                            
                            <!-- Grand Total Row -->
                            <tr class="bg-slate-800 text-white" v-if="reportData.length > 0">
                                <td colspan="3" class="px-6 py-4 text-center text-xs font-black uppercase tracking-widest text-slate-300 border-r border-slate-700 whitespace-nowrap">Total Akumulasi</td>
                                <td class="px-4 py-4 text-right font-bold text-white border-r border-slate-700 whitespace-nowrap">
                                    {{ formatCurrency(reportData.reduce((sum, row) => sum + row.gaji_pokok, 0)) }}
                                </td>
                                <td class="px-0 py-0 border-r border-slate-700 align-middle">
                                    <div class="grid grid-cols-3 divide-x divide-slate-700 h-full items-center">
                                        <div class="px-4 py-4 text-right text-sm font-medium text-slate-300 whitespace-nowrap">{{ formatCurrency(reportData.reduce((sum, row) => sum + row.tunjangan_jabatan, 0)) }}</div>
                                        <div class="px-4 py-4 text-right text-sm font-medium text-slate-300 whitespace-nowrap">{{ formatCurrency(reportData.reduce((sum, row) => sum + row.tunjangan_bpjs, 0)) }}</div>
                                        <div class="px-4 py-4 text-right text-sm font-black text-indigo-300 whitespace-nowrap">{{ formatCurrency(reportData.reduce((sum, row) => sum + row.jumlah_tunjangan, 0)) }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right font-black text-white border-r border-slate-700 whitespace-nowrap">
                                    {{ formatCurrency(reportData.reduce((sum, row) => sum + row.jumlah_bruto, 0)) }}
                                </td>
                                <td class="px-0 py-0 border-r border-slate-700 align-middle">
                                    <div class="grid grid-cols-3 divide-x divide-slate-700 h-full items-center">
                                        <div class="px-4 py-4 text-right text-sm font-medium text-slate-300 whitespace-nowrap">{{ formatCurrency(reportData.reduce((sum, row) => sum + row.potongan_kes, 0)) }}</div>
                                        <div class="px-4 py-4 text-right text-sm font-medium text-slate-300 whitespace-nowrap">{{ formatCurrency(reportData.reduce((sum, row) => sum + row.potongan_naker, 0)) }}</div>
                                        <div class="px-4 py-4 text-right text-sm font-black text-rose-300 whitespace-nowrap">{{ formatCurrency(reportData.reduce((sum, row) => sum + row.jumlah_potongan, 0)) }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-right font-black text-emerald-400 whitespace-nowrap">
                                    {{ formatCurrency(reportData.reduce((sum, row) => sum + row.jumlah_netto, 0)) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Print Section container (pseudo) -->
            
            <div class="flex justify-end mt-4">
                <button class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold flex items-center space-x-2 hover:bg-blue-700" onclick="window.print()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Cetak Laporan</span>
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #print-section, #print-section *, .print-table-container, .print-table-container * {
        visibility: visible;
    }
    #print-section {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    /* Move the table right below the header in print */
    .print-table-container {
        position: absolute;
        left: 0;
        top: 40px;
        width: 100%;
        overflow: visible !important;
    }
    /* Remove layout constraints */
    .overflow-x-auto, .overflow-hidden {
        overflow: visible !important;
    }
    /* Keep background colors */
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    /* Hide scrollbars and borders that look bad in print */
    .shadow-sm {
        box-shadow: none !important;
    }
    @page {
        size: landscape;
        margin: 1cm;
    }
}
</style>
