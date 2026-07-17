<script setup lang="ts">
import { formatRupiah } from '@/Utils/formatRupiah';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';
import { computed } from 'vue';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps<{
    role: string;
    stats: any;
}>();

const chartData = computed(() => ({
    labels: props.stats?.chart_data?.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
    datasets: [
        {
            label: 'Pemasukan',
            backgroundColor: '#10b981',
            data: props.stats?.chart_data?.income || [0, 0, 0, 0, 0, 0]
        },
        {
            label: 'Pengeluaran',
            backgroundColor: '#f43f5e',
            data: props.stats?.chart_data?.expense || [0, 0, 0, 0, 0, 0]
        }
    ]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top' as const,
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value: any) => formatRupiah(value)
            }
        }
    }
};

const payrollPaidPercent = computed(() => {
    if (!props.stats?.pending_payroll_count && props.stats?.payroll_this_month) return 100;
    if (!props.stats?.payroll_this_month) return 0;
    return 75; // Logic placeholder
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                        {{ role === 'guru' ? 'Pegawai' : (role || 'User').charAt(0).toUpperCase() + (role || 'User').slice(1) }} Dashboard
                    </h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Selamat datang kembali, berikut adalah ringkasan hari ini.</p>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Admin Dashboard -->
                <div v-if="role === 'admin'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Total Pegawai -->
                        <div class="bg-indigo-600 p-6 rounded-2xl shadow-xl shadow-indigo-200 dark:shadow-none text-white overflow-hidden relative group transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute -right-4 -top-4 bg-white/10 w-24 h-24 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3 backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="text-[10px] font-black text-indigo-100 uppercase tracking-widest mb-1">Total Pegawai</div>
                                <div class="text-3xl font-extrabold tracking-tight">{{ stats.teachers_count }}</div>
                                <div class="mt-3 text-[9px] font-bold text-indigo-200 bg-white/10 px-2 py-1 rounded-full w-fit">Aktif</div>
                            </div>
                        </div>
                        <!-- Total Jabatan -->
                        <div class="bg-emerald-600 p-6 rounded-2xl shadow-xl shadow-emerald-200 dark:shadow-none text-white overflow-hidden relative group transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute -right-4 -top-4 bg-white/10 w-24 h-24 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3 backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div class="text-[10px] font-black text-emerald-100 uppercase tracking-widest mb-1">Total Jabatan</div>
                                <div class="text-3xl font-extrabold tracking-tight">{{ stats.positions_count }}</div>
                                <div class="mt-3 text-[9px] font-bold text-emerald-200 bg-white/10 px-2 py-1 rounded-full w-fit">Struktural</div>
                            </div>
                        </div>
                        <!-- Gaji Terbayar -->
                        <div class="bg-blue-600 p-6 rounded-2xl shadow-xl shadow-blue-200 dark:shadow-none text-white overflow-hidden relative group transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute -right-4 -top-4 bg-white/10 w-24 h-24 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3 backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="text-[10px] font-black text-blue-100 uppercase tracking-widest mb-1">Payroll Terbayar</div>
                                <div class="text-xl font-extrabold tracking-tight">{{ formatRupiah(stats.payroll_this_month || 0) }}</div>
                                <div class="mt-3 text-[9px] font-bold text-blue-200 bg-white/10 px-2 py-1 rounded-full w-fit">Bulan Ini</div>
                            </div>
                        </div>
                        <!-- Payroll Tertunda -->
                        <div class="bg-rose-600 p-6 rounded-2xl shadow-xl shadow-rose-200 dark:shadow-none text-white overflow-hidden relative group transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute -right-4 -top-4 bg-white/10 w-24 h-24 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3 backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="text-[10px] font-black text-rose-100 uppercase tracking-widest mb-1">Payroll Tertunda</div>
                                <div class="text-3xl font-extrabold tracking-tight">{{ stats.pending_payroll_count }}</div>
                                <div class="mt-3 text-[9px] font-bold text-rose-200 bg-white/10 px-2 py-1 rounded-full w-fit">Belum Diproses</div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 tracking-tight">Grafik Arus Kas (6 Bulan Terakhir)</h3>
                            <div class="h-80">
                                <Bar :data="chartData" :options="chartOptions" />
                            </div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 tracking-tight">Status Payroll</h3>
                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black dark:text-gray-200">Selesai</div>
                                            <div class="text-xs text-gray-500 font-bold">Terbayar Bulan Ini</div>
                                        </div>
                                    </div>
                                    <div class="text-lg font-black text-emerald-600">{{ payrollPaidPercent }}%</div>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full transition-all duration-1000" :style="{ width: payrollPaidPercent + '%' }"></div>
                                </div>
                            </div>
                            <Link :href="route('salaries.index')" class="mt-10 w-full py-4 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-2xl text-sm font-black transition-all border border-gray-100 flex items-center justify-center">Kelola Payroll</Link>
                        </div>
                    </div>
                </div>

                <!-- Bendahara Dashboard -->
                <div v-if="role === 'bendahara'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-indigo-600 p-6 rounded-2xl text-white shadow-xl shadow-indigo-100 flex flex-col justify-between h-44 group overflow-hidden relative transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-duration-500"></div>
                            <div class="relative z-10 h-full flex flex-col justify-between">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-indigo-100 mb-0.5">Saldo Kas Sekarang</div>
                                    <div class="text-2xl font-black tracking-tight">{{ formatRupiah(stats?.cash_balance || 0) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-amber-600 p-6 rounded-2xl text-white shadow-xl shadow-amber-100 flex flex-col justify-between h-44 group overflow-hidden relative transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-duration-500"></div>
                            <div class="relative z-10 h-full flex flex-col justify-between">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-amber-100 mb-0.5">Payroll Tertunda</div>
                                    <div class="text-2xl font-black tracking-tight">{{ stats?.pending_payroll_count || '0' }} Pegawai</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 tracking-tight">Riwayat Penggajian Terakhir</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                    <tr v-for="(sal, index) in stats?.recent_salaries" :key="sal.id" class="hover:bg-gray-50">
                                        <td class="py-4 pr-4 pl-2 text-center text-xs font-black text-gray-400">{{ index + 1 }}</td>
                                        <td class="py-4 font-bold text-gray-900 dark:text-white">{{ sal.teacher?.name }}</td>
                                        <td class="py-4 text-gray-500 font-medium">Periode {{ String(sal.month).padStart(2, '0') }}/{{ sal.year }}</td>
                                        <td class="py-4 font-black text-emerald-600">{{ formatRupiah(sal.amount || 0) }}</td>
                                        <td class="py-4 text-right text-xs font-black uppercase text-gray-400">Status Selesai</td>
                                    </tr>
                                    <tr v-if="!stats?.recent_salaries?.length">
                                        <td colspan="5" class="py-8 text-center text-gray-400">Belum ada aktivitas baru.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pegawai Dashboard -->
                <div v-if="role === 'guru'" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Gaji Terakhir -->
                        <div class="bg-indigo-600 p-8 rounded-3xl text-white shadow-xl shadow-indigo-100 flex flex-col justify-between h-52 group overflow-hidden relative transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-duration-500"></div>
                            <div class="relative z-10 h-full flex flex-col justify-between">
                                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm3-4a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-black uppercase tracking-widest text-indigo-100 mb-1">Gaji Terakhir</div>
                                    <div class="text-3xl font-black tracking-tight">{{ formatRupiah(stats?.financials?.last_salary || 0) }}</div>
                                </div>
                            </div>
                        </div>
                        <!-- Total Tahun Ini -->
                        <div class="bg-emerald-600 p-8 rounded-3xl text-white shadow-xl shadow-emerald-100 flex flex-col justify-between h-52 group overflow-hidden relative transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-duration-500"></div>
                            <div class="relative z-10 h-full flex flex-col justify-between">
                                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-black uppercase tracking-widest text-emerald-100 mb-1">Total Diterima Tahun Ini</div>
                                    <div class="text-3xl font-black tracking-tight">{{ formatRupiah(stats?.financials?.total_year || 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile & Salary Table -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Profile Card -->
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center space-y-4">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-3xl font-black shadow-lg shadow-indigo-200">
                                {{ (stats?.teacher?.name || 'P').charAt(0) }}
                            </div>
                            <div>
                                <div class="text-lg font-black text-gray-900 dark:text-white">{{ stats?.teacher?.name || '-' }}</div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                                    TY: {{ stats?.teacher?.nipty || '-' }} | Y: {{ stats?.teacher?.nipy || '-' }}
                                </div>
                            </div>
                            <div class="w-full pt-4 border-t border-gray-100 dark:border-gray-700">
                                <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Jabatan Struktural</div>
                                <div v-if="stats?.teacher?.positions?.length" class="space-y-2">
                                    <div v-for="pos in stats.teacher.positions" :key="pos.name"
                                        class="flex items-center justify-between text-sm bg-indigo-50 dark:bg-indigo-900/20 rounded-xl px-4 py-2">
                                        <span class="font-bold text-indigo-700 dark:text-indigo-300 text-left">{{ pos.name }}</span>
                                        <span class="font-black text-indigo-600 dark:text-indigo-400 text-xs ml-2">+{{ formatRupiah(pos.allowance || 0) }}</span>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-gray-400 italic py-2">Tidak ada jabatan tambahan.</div>
                            </div>
                        </div>

                        <!-- Salary History Table -->
                        <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 tracking-tight">Riwayat Penggajian Detail</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="bg-[#003B73] text-white text-[10px] font-black uppercase tracking-widest">
                                            <th class="py-3 pl-4 rounded-l-xl w-8 text-center">No</th>
                                            <th class="py-3 px-3">Periode</th>
                                            <th class="py-3 px-3">Gaji Pokok</th>
                                            <th class="py-3 px-3">Tunjangan</th>
                                            <th class="py-3 px-3 text-center">Status</th>
                                            <th class="py-3 pr-4 rounded-r-xl text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                        <tr v-for="(sal, idx) in stats?.salaries" :key="sal.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="py-3.5 pl-4 text-center font-black text-gray-400 text-xs">{{ idx + 1 }}</td>
                                            <td class="py-3.5 px-3 font-bold text-gray-900 dark:text-white text-sm">
                                                {{ String(sal.month).padStart(2, '0') }}/{{ sal.year }}
                                            </td>
                                            <td class="py-3.5 px-3 text-gray-600 font-medium text-sm">{{ formatRupiah(sal.base_salary || 0) }}</td>
                                            <td class="py-3.5 px-3 text-emerald-600 font-medium text-sm">+{{ formatRupiah(sal.allowance || 0) }}</td>
                                            <td class="py-3.5 px-3 text-center">
                                                <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-full"
                                                    :class="sal.status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                                                    {{ sal.status === 'paid' ? 'Lunas' : 'Proses' }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 pr-4 text-center">
                                                <a :href="`/salaries/${sal.id}/print`" target="_blank"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase rounded-lg transition-all shadow-sm">
                                                    Slip
                                                </a>
                                            </td>
                                        </tr>
                                        <tr v-if="!stats?.salaries?.length">
                                            <td colspan="6" class="py-10 text-center text-gray-400 font-bold italic text-sm">Belum ada data gaji tersedia.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
