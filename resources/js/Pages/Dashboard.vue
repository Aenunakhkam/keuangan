<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps<{
    role: string;
    stats: any;
}>();

import { computed } from 'vue';

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
                callback: (value: any) => 'Rp ' + value.toLocaleString('id-ID', {maximumFractionDigits: 0})
            }
        }
    }
};

const totalDaily = computed(() => (props.stats?.today_paid_count || 0) + (props.stats?.pending_bills_count || 0));
const paidPercent = computed(() => totalDaily.value > 0 ? Math.round(((props.stats?.today_paid_count || 0) / totalDaily.value) * 100) : 0);
const pendingPercent = computed(() => totalDaily.value > 0 ? Math.round(((props.stats?.pending_bills_count || 0) / totalDaily.value) * 100) : 0);
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                        {{ (role || 'User').charAt(0).toUpperCase() + (role || 'User').slice(1) }} Dashboard
                    </h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Selamat datang kembali, berikut adalah ringkasan hari ini.</p>
                </div>
                <div></div>
            </div>
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Admin Dashboard -->
                <div v-if="role === 'admin'" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-indigo-600 p-6 rounded-2xl shadow-xl shadow-indigo-200 dark:shadow-none text-white overflow-hidden relative group transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute -right-4 -top-4 bg-white/10 w-24 h-24 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3 backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div class="text-[10px] font-black text-indigo-100 uppercase tracking-widest mb-1">Total Siswa</div>
                                <div class="text-3xl font-extrabold tracking-tight">{{ stats.students_count }}</div>
                                <div class="mt-3 text-[9px] font-bold text-indigo-200 flex items-center bg-white/10 px-2 py-1 rounded-full w-fit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    <span>Terdaftar</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-emerald-600 p-6 rounded-2xl shadow-xl shadow-emerald-200 dark:shadow-none text-white overflow-hidden relative group transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute -right-4 -top-4 bg-white/10 w-24 h-24 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3 backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="text-[10px] font-black text-emerald-100 uppercase tracking-widest mb-1">Total Guru</div>
                                <div class="text-3xl font-extrabold tracking-tight">{{ stats.teachers_count }}</div>
                                <div class="mt-3 text-[9px] font-bold text-emerald-200 flex items-center bg-white/10 px-2 py-1 rounded-full w-fit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    <span>Aktif</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-blue-600 p-6 rounded-2xl shadow-xl shadow-blue-200 dark:shadow-none text-white overflow-hidden relative group transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute -right-4 -top-4 bg-white/10 w-24 h-24 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3 backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <div class="text-[10px] font-black text-blue-100 uppercase tracking-widest mb-1">Pemasukan</div>
                                <div class="text-2xl font-extrabold tracking-tight">Rp {{ Number(stats.total_income || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</div>
                                <div class="mt-3 text-[9px] font-bold text-blue-200 bg-white/10 px-2 py-1 rounded-full w-fit">Bulan Berjalan</div>
                            </div>
                        </div>
                        <div class="bg-rose-600 p-6 rounded-2xl shadow-xl shadow-rose-200 dark:shadow-none text-white overflow-hidden relative group transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute -right-4 -top-4 bg-white/10 w-24 h-24 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-3 backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                    </svg>
                                </div>
                                <div class="text-[10px] font-black text-rose-100 uppercase tracking-widest mb-1">Pengeluaran</div>
                                <div class="text-2xl font-extrabold tracking-tight">Rp {{ Number(stats.total_expense || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</div>
                                <div class="mt-3 text-[9px] font-bold text-rose-200 bg-white/10 px-2 py-1 rounded-full w-fit">Bulan Berjalan</div>
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
                            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 tracking-tight">Status Pembayaran Hari Ini</h3>
                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black dark:text-gray-200">Lunas</div>
                                            <div class="text-xs text-gray-500 font-bold">{{ stats?.today_paid_count || 0 }} Transaksi Hari Ini</div>
                                        </div>
                                    </div>
                                    <div class="text-lg font-black text-emerald-600">{{ paidPercent }}%</div>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full transition-all duration-1000" :style="{ width: paidPercent + '%' }"></div>
                                </div>

                                <div class="flex items-center justify-between pt-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black dark:text-gray-200">Tertunda</div>
                                            <div class="text-xs text-gray-500 font-bold">{{ stats?.pending_bills_count || 0 }} Menunggu</div>
                                        </div>
                                    </div>
                                    <div class="text-lg font-black text-amber-600">{{ pendingPercent }}%</div>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-amber-500 h-2 rounded-full transition-all duration-1000" :style="{ width: pendingPercent + '%' }"></div>
                                </div>
                            </div>
                            <Link :href="route('reports.index')" class="mt-10 w-full py-4 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-2xl text-sm font-black transition-all border border-gray-100 flex items-center justify-center">Lihat Laporan Detail</Link>
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
                                    <div class="text-2xl font-black tracking-tight">Rp {{ Number(stats?.cash_balance || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</div>
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
                                    <div class="text-[10px] font-black uppercase tracking-widest text-amber-100 mb-0.5">Tagihan Menunggu</div>
                                    <div class="text-2xl font-black tracking-tight">{{ stats?.pending_bills_count || '0' }} Tagihan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 tracking-tight">Pembayaran Pelunasan Terakhir</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                    <tr v-for="(pay, index) in stats?.recent_payments" :key="pay.id" class="hover:bg-gray-50">
                                        <td class="py-4 pr-4 pl-2 text-center text-xs font-black text-gray-400">{{ index + 1 }}</td>
                                        <td class="py-4 font-bold text-gray-900 dark:text-white">{{ pay.student_bill?.student?.name }}</td>
                                        <td class="py-4 text-gray-500 font-medium">{{ pay.student_bill?.fee_type?.name }}</td>
                                        <td class="py-4 font-black text-emerald-600">Rp {{ (pay.amount || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</td>
                                        <td class="py-4 text-right text-xs font-black uppercase text-gray-400">{{ new Date(pay.created_at).toLocaleDateString() }}</td>
                                    </tr>
                                    <tr v-if="!stats?.recent_payments?.length">
                                        <td colspan="5" class="py-8 text-center text-gray-400">Belum ada aktivitas baru.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Guru Dashboard -->
                <div v-if="role === 'guru'" class="space-y-8">
                    <!-- 3 Stat Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                                    <div class="text-3xl font-black tracking-tight">Rp {{ (stats?.financials?.last_salary || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</div>
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
                                    <div class="text-3xl font-black tracking-tight">Rp {{ (stats?.financials?.total_year || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</div>
                                </div>
                            </div>
                        </div>
                        <!-- Beban Mengajar -->
                        <div class="bg-amber-500 p-8 rounded-3xl text-white shadow-xl shadow-amber-100 flex flex-col justify-between h-52 group overflow-hidden relative transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-duration-500"></div>
                            <div class="relative z-10 h-full flex flex-col justify-between">
                                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-black uppercase tracking-widest text-amber-100 mb-1">Beban Mengajar</div>
                                    <div class="text-3xl font-black tracking-tight">{{ stats?.teacher?.teaching_hours || 0 }} <span class="text-base font-bold">JP/Minggu</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile & Salary Table -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Profile & Positions Card -->
                        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center space-y-4">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white text-3xl font-black shadow-lg shadow-indigo-200">
                                {{ (stats?.teacher?.name || 'G').charAt(0) }}
                            </div>
                            <div>
                                <div class="text-lg font-black text-gray-900 dark:text-white">{{ stats?.teacher?.name || '-' }}</div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">NIP: {{ stats?.teacher?.nip || '-' }}</div>
                            </div>
                            <div class="w-full pt-4 border-t border-gray-100 dark:border-gray-700">
                                <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Jabatan Struktural</div>
                                <div v-if="stats?.teacher?.positions?.length" class="space-y-2">
                                    <div v-for="pos in stats.teacher.positions" :key="pos.name"
                                        class="flex items-center justify-between text-sm bg-indigo-50 dark:bg-indigo-900/20 rounded-xl px-4 py-2">
                                        <span class="font-bold text-indigo-700 dark:text-indigo-300 text-left">{{ pos.name }}</span>
                                        <span class="font-black text-indigo-600 dark:text-indigo-400 text-xs whitespace-nowrap ml-2">+Rp {{ (pos.allowance || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</span>
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
                                            <th class="py-3 px-3">Transport</th>
                                            <th class="py-3 px-3">Potongan</th>
                                            <th class="py-3 px-3 text-center">Hadir</th>
                                            <th class="py-3 px-3">Gaji Bersih</th>
                                            <th class="py-3 px-3 text-center">Status</th>
                                            <th class="py-3 pr-4 rounded-r-xl text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                        <tr v-for="(sal, idx) in stats?.salaries" :key="sal.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <td class="py-3.5 pl-4 text-center font-black text-gray-400 text-xs">{{ idx + 1 }}</td>
                                            <td class="py-3.5 px-3 font-bold text-gray-900 dark:text-white text-sm whitespace-nowrap">
                                                {{ String(sal.month).padStart(2, '0') }}/{{ sal.year }}
                                            </td>
                                            <td class="py-3.5 px-3 text-gray-600 font-medium text-sm">Rp {{ Number(sal.base_salary || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</td>
                                            <td class="py-3.5 px-3 text-emerald-600 font-medium text-sm">+Rp {{ Number(sal.allowance || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</td>
                                            <td class="py-3.5 px-3 text-sky-600 font-medium text-sm">+Rp {{ Number(sal.transport_allowance || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</td>
                                            <td class="py-3.5 px-3 text-sm">
                                                <div class="text-rose-600 font-medium">-Rp {{ Number(sal.deduction || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</div>
                                                <div v-if="sal.deduction_description" class="text-[10px] text-gray-400 italic mt-0.5 max-w-[100px] truncate" :title="sal.deduction_description">{{ sal.deduction_description }}</div>
                                            </td>
                                            <td class="py-3.5 px-3 text-center font-bold text-gray-600 text-sm whitespace-nowrap">{{ sal.days_present || 0 }} Hr</td>
                                            <td class="py-3.5 px-3 font-black text-indigo-700 dark:text-indigo-400 text-sm whitespace-nowrap">Rp {{ Number(sal.net_salary || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</td>
                                            <td class="py-3.5 px-3 text-center">
                                                <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-full"
                                                    :class="sal.status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                                                    {{ sal.status === 'paid' ? 'Lunas' : 'Proses' }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 pr-4 text-center">
                                                <a :href="`/salaries/${sal.id}/print`" target="_blank"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase rounded-lg transition-all shadow-sm hover:shadow-indigo-200 hover:shadow-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                    Slip
                                                </a>
                                            </td>
                                        </tr>
                                        <tr v-if="!stats?.salaries?.length">
                                            <td colspan="10" class="py-10 text-center text-gray-400 font-bold italic">Belum ada data gaji tersedia.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Siswa Dashboard -->
                <div v-if="role === 'siswa'" class="space-y-8">
                    <!-- Student Profile & Balance Card -->
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                        <!-- Profile Card -->
                        <div class="lg:col-span-1 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-3xl font-black shadow-lg shadow-indigo-200 mb-4 transition-transform hover:scale-110 duration-500">
                                {{ (stats?.student?.name || 'S').charAt(0) }}
                            </div>
                            <h3 class="text-lg font-black text-gray-900 dark:text-white leading-tight">{{ stats?.student?.name }}</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">NIS: {{ stats?.student?.nis }}</p>
                            <div class="mt-4 px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 rounded-full text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">
                                KELAS {{ stats?.student?.class_name }}
                            </div>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col justify-between group overflow-hidden relative transition-all duration-300 hover:-translate-y-1">
                                <div class="absolute -right-4 -top-4 w-24 h-24 bg-gray-50 dark:bg-gray-700/50 rounded-full blur-2xl group-hover:scale-150 transition-duration-500"></div>
                                <div class="relative z-10">
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Tagihan</div>
                                    <div class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Rp {{ Number(stats?.summary?.total_bills || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</div>
                                </div>
                                <div class="relative z-10 mt-4 text-[10px] font-bold text-gray-400">Seluruh Periode</div>
                            </div>
                            <div class="bg-emerald-600 p-8 rounded-3xl text-white shadow-xl shadow-emerald-100 flex flex-col justify-between group overflow-hidden relative transition-all duration-300 hover:-translate-y-1">
                                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-duration-500"></div>
                                <div class="relative z-10">
                                    <div class="text-[10px] font-black text-emerald-100 uppercase tracking-widest mb-1">Sudah Dibayar</div>
                                    <div class="text-2xl font-black tracking-tight">Rp {{ Number(stats?.summary?.total_paid || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</div>
                                </div>
                                <div class="relative z-10 mt-4 text-[10px] font-bold text-emerald-200">Transaksi Lunas</div>
                            </div>
                            <div class="bg-rose-600 p-8 rounded-3xl text-white shadow-xl shadow-rose-100 flex flex-col justify-between group overflow-hidden relative transition-all duration-300 hover:-translate-y-1">
                                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-duration-500"></div>
                                <div class="relative z-10">
                                    <div class="text-[10px] font-black text-rose-100 uppercase tracking-widest mb-1">Sisa Tunggakan</div>
                                    <div class="text-2xl font-black tracking-tight">Rp {{ Number(stats?.summary?.total_unpaid || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</div>
                                </div>
                                <div class="relative z-10 mt-4 text-[10px] font-bold text-rose-200">Harus Segera Dilunasi</div>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Transfer Info -->
                    <div v-if="stats.bank_name || stats.bank_account_number" class="bg-indigo-50 border border-indigo-100 p-6 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-6 dark:bg-indigo-900/20 dark:border-indigo-800">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm text-indigo-600 dark:bg-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-indigo-900 dark:text-indigo-100 uppercase tracking-widest mb-1">Metode Pembayaran Transfer</h4>
                                <div class="text-xs text-indigo-700 dark:text-indigo-300 font-medium italic">Kirim bukti pembayaran ke bagian TU setelah melakukan transfer.</div>
                            </div>
                        </div>
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-indigo-50 min-w-[320px] dark:bg-gray-800 dark:border-gray-700 flex flex-col space-y-1">
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">{{ stats.bank_name || 'Rekening Bank' }}</div>
                            <div class="text-2xl font-black text-indigo-900 tracking-tight dark:text-white">{{ stats.bank_account_number || '-' }}</div>
                            <div class="text-xs font-bold text-gray-500 uppercase">A.N. {{ stats.bank_account_name || 'SEKOLAH' }}</div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 tracking-tight">Rincian Tagihan Pembayaran</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-[#003B73] text-white text-[10px] font-black uppercase tracking-widest">
                                        <th class="py-4 pl-6 rounded-l-2xl w-12 text-center">NO</th>
                                        <th class="py-4 px-4 font-black">JENIS PEMBAYARAN</th>
                                        <th class="py-4 px-4 font-black text-center">JATUH TEMPO</th>
                                        <th class="py-4 px-4 font-black">NOMINAL TAGIHAN</th>
                                        <th class="py-4 px-4 font-black text-center">STATUS</th>
                                        <th class="py-4 pr-6 rounded-r-2xl font-black text-right">KETERANGAN</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                    <tr v-for="(bill, idx) in stats?.bills" :key="bill.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                        <td class="py-4 pl-6 text-center font-black text-gray-400 text-xs">{{ idx + 1 }}</td>
                                        <td class="py-4 px-4">
                                            <div class="font-bold text-gray-900 dark:text-white uppercase tracking-tight">{{ bill.fee_type?.name }}</div>
                                            <div class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">ID TAGIHAN: #{{ bill.id }}</div>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <div class="inline-flex items-center px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-lg text-xs font-black text-gray-600 dark:text-gray-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1.5 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ new Date(bill.due_date).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'}) }}
                                            </div>
                                        </td>
                                        <td class="py-4 px-4">
                                            <div class="text-sm font-black text-indigo-700 dark:text-indigo-400">Rp {{ Number(bill.amount || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</div>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span class="text-[10px] font-black uppercase px-3 py-1.5 rounded-full inline-block shadow-sm"
                                                :class="bill.status === 'paid' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200'">
                                                {{ bill.status === 'paid' ? 'LUNAS' : 'BELUM BAYAR' }}
                                            </span>
                                        </td>
                                        <td class="py-4 pr-6 text-right">
                                            <div v-if="bill.status === 'paid'" class="text-[10px] font-bold text-emerald-600 italic">Terima kasih atas pembayarannya</div>
                                            <div v-else class="text-[10px] font-bold text-rose-500 italic">Harap segera melunasi pembayaran</div>
                                        </td>
                                    </tr>
                                    <tr v-if="!stats?.bills?.length">
                                        <td colspan="6" class="py-20 text-center text-gray-400 font-bold italic">
                                            Anda tidak memiliki tagihan aktif saat ini.
                                        </td>
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
