<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Bar, Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale, ArcElement);

const props = defineProps<{
    incomeByMonth: any[];
    expenseByMonth: any[];
    expenseByCategory: any[];
    incomeByCategory: any[];
    recentSalaries: any[];
    payrollStatusSummary: { paid: number, pending: number };
}>();

const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

const monthlyChartData = computed(() => {
    const incomeData = new Array(12).fill(0);
    const expenseData = new Array(12).fill(0);
    
    props.incomeByMonth.forEach(item => {
        incomeData[item.month - 1] = item.total;
    });
    
    props.expenseByMonth.forEach(item => {
        expenseData[item.month - 1] = item.total;
    });

    return {
        labels: monthLabels,
        datasets: [
            {
                label: 'Pemasukan',
                backgroundColor: '#10b981',
                data: incomeData
            },
            {
                label: 'Pengeluaran',
                backgroundColor: '#f43f5e',
                data: expenseData
            }
        ]
    };
});

const categoryChartData = computed(() => {
    return {
        labels: props.expenseByCategory.map(cat => cat.category),
        datasets: [
            {
                data: props.expenseByCategory.map(cat => cat.total),
                backgroundColor: [
                    '#4f46e5', '#7c3aed', '#2563eb', '#0891b2', '#059669', '#d97706', '#dc2626'
                ]
            }
        ]
    };
});

const payrollStatusChartData = computed(() => {
    return {
        labels: ['Selesai', 'Tertunda'],
        datasets: [
            {
                data: [props.payrollStatusSummary?.paid || 0, props.payrollStatusSummary?.pending || 0],
                backgroundColor: ['#10b981', '#fbbf24']
            }
        ]
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' as const }
    }
};

const barOptions = {
    ...chartOptions,
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value: any) => 'Rp ' + Number(value).toLocaleString('id-ID')
            }
        }
    }
};

const exportExpense = () => {
    window.location.href = route('reports.export-expense');
};

const exportIncome = () => {
    window.location.href = route('reports.export-income');
};

const monthName = (m: number) => {
    const names = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return names[m - 1];
};
</script>

<template>
    <Head title="Laporan Keuangan" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Laporan & Analitik</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Ringkasan performa keuangan dan data payroll pegawai.</p>
                </div>
                <div class="flex space-x-3">
                    <Link 
                        :href="route('reports.transactions')"
                        class="px-5 py-3 bg-indigo-600 text-white rounded-2xl text-sm font-black shadow-xl shadow-indigo-500/20 transform transition-transform hover:scale-[1.02] flex items-center space-x-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Transaksi Detail</span>
                    </Link>
                    <button 
                        @click="exportIncome"
                        class="px-5 py-3 bg-emerald-600 text-white rounded-2xl text-sm font-black shadow-xl shadow-emerald-500/20 transform transition-transform hover:scale-[1.02] flex items-center space-x-2"
                    >
                        <span>Ekspor Pemasukan</span>
                    </button>
                    <button 
                        @click="exportExpense"
                        class="px-5 py-3 bg-[#003B73] text-white rounded-2xl text-sm font-black shadow-xl shadow-blue-500/20 transform transition-transform hover:scale-[1.02] flex items-center space-x-2"
                    >
                        <span>Ekspor Pengeluaran</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-10">
            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6">Tren Pemasukan vs Pengeluaran</h3>
                    <div class="h-80">
                        <Bar :data="monthlyChartData" :options="barOptions" />
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6">Status Payroll Pegawai</h3>
                    <div class="h-80 flex flex-col items-center justify-center">
                        <Doughnut :data="payrollStatusChartData" :options="chartOptions" />
                        <div class="mt-4 text-center">
                            <div class="text-sm font-bold text-gray-500">Total Transaksi Payroll</div>
                            <div class="text-2xl font-black text-gray-900 dark:text-white">{{ (payrollStatusSummary?.paid || 0) + (payrollStatusSummary?.pending || 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center">
                        Pemasukan Bulanan
                    </h3>
                    <div class="space-y-4">
                        <div v-for="item in incomeByMonth" :key="item.month" class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
                            <span class="font-bold text-gray-600">{{ monthName(item.month) }}</span>
                            <span class="font-black text-emerald-600">Rp {{ Number(item.total || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</span>
                        </div>
                        <div v-if="incomeByMonth.length === 0" class="text-center py-4 text-gray-400 font-medium italic">Belum ada data pemasukan.</div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6 flex items-center">
                        Pengeluaran Bulanan
                    </h3>
                    <div class="space-y-4">
                        <div v-for="item in expenseByMonth" :key="item.month" class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
                            <span class="font-bold text-gray-600">{{ monthName(item.month) }}</span>
                            <span class="font-black text-rose-600">Rp {{ Number(item.total || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</span>
                        </div>
                        <div v-if="expenseByMonth.length === 0" class="text-center py-4 text-gray-400 font-medium italic">Belum ada data pengeluaran.</div>
                    </div>
                </div>
            </div>

            <!-- Recent Salaries List -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-black text-gray-900 dark:text-white mb-6">Distribusi Gaji Terakhir</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                                <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center w-16">No</th>
                                <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Pegawai</th>
                                <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Periode</th>
                                <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Nominal</th>
                                <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            <tr v-for="(sal, index) in recentSalaries" :key="sal.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-gray-300">{{ index + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-gray-900">{{ sal.teacher?.name || '-' }}</td>
                                <td class="px-6 py-4 text-gray-600 font-medium text-sm">{{ sal.period }}</td>
                                <td class="px-6 py-4 text-right font-black text-indigo-600">Rp {{ Number(sal.amount || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase">
                                        {{ sal.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
