<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { confirmDelete } from '@/Utils/sweetalert';

const props = defineProps<{
    payments: any;
}>();

const form = useForm({});

const deletePayment = async (id: number) => {
    const result = await confirmDelete('Hapus Pembayaran', 'Hapus riwayat pembayaran ini? Ini juga akan memperbarui status tagihan.');
    if (result.isConfirmed) {
        router.delete(route('payments.destroy', id));
    }
};
</script>

<template>
    <Head title="Riwayat Pembayaran" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Riwayat Pembayaran</h2>
            <p class="text-sm text-gray-500 font-medium mt-1">Daftar seluruh transaksi pembayaran yang telah dicatat.</p>
        </template>

        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#003B73] border-b border-indigo-800">
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center w-16">No</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Tanggal</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Siswa</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Keterangan</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Metode</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Jumlah</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        <tr v-for="(pay, index) in payments.data" :key="pay.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                            <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-gray-300">{{ (payments.current_page - 1) * payments.per_page + index + 1 }}</td>
                            <td class="px-6 py-4 font-bold text-gray-600 dark:text-gray-400">{{ new Date(pay.payment_date).toLocaleDateString() }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ pay.student_bill.student.name }}</div>
                                <div class="text-[10px] text-gray-400 font-black uppercase tracking-tight">{{ pay.student_bill.student.nis }}</div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300">{{ pay.student_bill.fee_type.name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400 rounded-full text-[10px] font-black uppercase">{{ pay.payment_method }}</span>
                            </td>
                            <td class="px-6 py-4 font-black text-emerald-600">Rp {{ Number(pay.amount || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</td>
                            <td class="px-6 py-4 text-right">
                                <button @click="deletePayment(pay.id)" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="payments.data.length === 0">
                            <td colspan="7" class="px-6 py-20 text-center text-gray-500 font-medium">Belum ada riwayat transaksi.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
