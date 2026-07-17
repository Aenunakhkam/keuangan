<script setup lang="ts">
import { formatRupiah } from '@/Utils/formatRupiah';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    yearlyData: { year: number; total_pegawai: number; total_amount: number }[];
}>();

</script>

<template>
    <Head title="Laporan Gaji Pertahun" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Laporan Pengajuan Gaji Pertahun</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Rekapitulasi total nominal pengajuan gaji setiap tahunnya.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a :href="route('reports.yearly.print')" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-25 transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak PDF
                    </a>
                    <a :href="route('reports.yearly.excel')" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 hover:text-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-25 transition-all duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Excel
                    </a>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/80">
                                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-widest border-b">Tahun</th>
                                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-widest border-b text-center">Total Pegawai Diajukan</th>
                                        <th class="px-6 py-4 text-xs font-black text-gray-500 uppercase tracking-widest border-b text-right">Total Nominal Pengajuan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr v-for="data in yearlyData" :key="data.year" class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-bold text-gray-900">Tahun {{ data.year }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span v-if="data.total_pegawai > 0" class="inline-flex items-center justify-center px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full border border-green-200 shadow-sm">
                                                {{ data.total_pegawai }} Pegawai
                                            </span>
                                            <span v-else class="text-gray-400 text-sm font-medium">-</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-sm font-black text-gray-900">{{ formatRupiah(data.total_amount) }}</span>
                                        </td>
                                    </tr>
                                    <tr v-if="yearlyData.length === 0">
                                        <td colspan="3" class="px-6 py-8 text-center text-gray-500 font-medium">Belum ada data pengajuan gaji.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
