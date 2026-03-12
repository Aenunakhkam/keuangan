<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import CurrencyInput from '@/Components/CurrencyInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import debounce from 'lodash/debounce';
import { confirmDelete } from '@/Utils/sweetalert';

const props = defineProps<{
    transactions: any;
    stats: any;
    filters: any;
}>();

const showingModal = ref(false);
const modalMode = ref('create'); 
const selectedTransaction = ref<any>(null);

const type = ref(props.filters.type || '');
const category = ref(props.filters.category || '');

const form = useForm({
    type: 'expense',
    category: '',
    amount: 0,
    description: '',
    transaction_date: new Date().toISOString().split('T')[0],
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    form.clearErrors();
    showingModal.value = true;
};

const openEditModal = (tx: any) => {
    modalMode.value = 'edit';
    selectedTransaction.value = tx;
    form.type = tx.type;
    form.category = tx.category;
    form.amount = tx.amount;
    form.description = tx.description;
    form.transaction_date = tx.transaction_date;
    form.clearErrors();
    showingModal.value = true;
};

const submit = () => {
    if (modalMode.value === 'create') {
        form.post(route('cash-transactions.store'), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('cash-transactions.update', selectedTransaction.value.id), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    }
};

const deleteTransaction = async (id: number) => {
    const result = await confirmDelete('Hapus Transaksi', 'Apakah Anda yakin ingin menghapus transaksi ini?');
    if (result.isConfirmed) {
        router.delete(route('cash-transactions.destroy', id));
    }
};

watch([type, category], debounce(() => {
    router.get(route('cash-transactions.index'), { 
        type: type.value, 
        category: category.value 
    }, { preserveState: true });
}, 300));
</script>

<template>
    <Head title="Jurnal Kas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Jurnal Kas</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Laporan harian seluruh arus kas masuk dan keluar.</p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="px-5 py-3 bg-[#003B73] text-white rounded-2xl text-sm font-black shadow-xl shadow-blue-500/20 transform transition-transform hover:scale-[1.02] active:scale-[0.98] flex items-center space-x-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Catat Transaksi</span>
                </button>
            </div>
        </template>

        <div class="space-y-8">
            <!-- Stats Board -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-indigo-600 p-8 rounded-3xl text-white shadow-xl shadow-indigo-100 flex flex-col justify-between h-44 group overflow-hidden relative">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-duration-500"></div>
                    <div class="text-xs font-black uppercase tracking-widest text-indigo-100">Total Pemasukan</div>
                    <div class="text-3xl font-black tracking-tight">Rp {{ stats?.total_income?.toLocaleString('id-ID', {maximumFractionDigits: 0}) || '0' }}</div>
                </div>
                <div class="bg-rose-600 p-8 rounded-3xl text-white shadow-xl shadow-rose-100 flex flex-col justify-between h-44 group overflow-hidden relative">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-duration-500"></div>
                    <div class="text-xs font-black uppercase tracking-widest text-rose-100">Total Pengeluaran</div>
                    <div class="text-3xl font-black tracking-tight">Rp {{ stats?.total_expense?.toLocaleString('id-ID', {maximumFractionDigits: 0}) || '0' }}</div>
                </div>
                <div class="bg-emerald-600 p-8 rounded-3xl text-white shadow-xl shadow-emerald-100 flex flex-col justify-between h-44 group overflow-hidden relative">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-duration-500"></div>
                    <div class="text-xs font-black uppercase tracking-widest text-emerald-100">Saldo Akhir Kas</div>
                    <div class="text-3xl font-black tracking-tight">Rp {{ stats?.balance?.toLocaleString('id-ID', {maximumFractionDigits: 0}) || '0' }}</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col md:flex-row gap-4 bg-white dark:bg-gray-800 p-6 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm">
                <select v-model="type" class="px-5 py-3 rounded-2xl border-none ring-1 ring-gray-100 dark:ring-gray-700 bg-gray-50 dark:bg-gray-900 font-bold focus:ring-4 focus:ring-indigo-500/10">
                    <option value="">Semua Tipe</option>
                    <option value="income">Pemasukan (+)</option>
                    <option value="expense">Pengeluaran (-)</option>
                </select>
                <TextInput v-model="category" placeholder="Cari Kategori..." class="flex-1" />
            </div>

            <!-- Transactions Table -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-[#003B73] border-b border-indigo-800">
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center w-16">No</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Tanggal</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Kategori</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Keterangan</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Nominal</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center">Tipe</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            <tr v-for="(tx, index) in transactions.data" :key="tx.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-all duration-200">
                                <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-gray-300">{{ (transactions.current_page - 1) * transactions.per_page + index + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-gray-500 dark:text-gray-400 text-sm italic">{{ tx.transaction_date }}</td>
                                <td class="px-6 py-4 font-black text-gray-900 dark:text-white">{{ tx.category }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 font-medium">{{ tx.description || '-' }}</td>
                                <td class="px-6 py-4 text-right font-black" :class="tx.type === 'income' ? 'text-emerald-600' : 'text-rose-600'">
                                    {{ tx.type === 'income' ? '+' : '-' }} Rp {{ Number(tx.amount || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest" :class="tx.type === 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'">
                                        {{ tx.type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button @click="openEditModal(tx)" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteTransaction(tx.id)" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Transaction Modal -->
        <Modal :show="showingModal" @close="showingModal = false">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">
                    {{ modalMode === 'create' ? 'Catat Transaksi Kas' : 'Edit Transaksi Kas' }}
                </h2>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" value="income" v-model="form.type" class="sr-only peer">
                            <div class="p-4 border-2 border-gray-100 rounded-2xl text-center font-black text-gray-400 peer-checked:border-emerald-500 peer-checked:text-emerald-600 peer-checked:bg-emerald-50 transition-all">Pemasukan (+)</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" value="expense" v-model="form.type" class="sr-only peer">
                            <div class="p-4 border-2 border-gray-100 rounded-2xl text-center font-black text-gray-400 peer-checked:border-rose-500 peer-checked:text-rose-600 peer-checked:bg-rose-50 transition-all">Pengeluaran (-)</div>
                        </label>
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel value="Kategori (misal: Biaya Listrik, Alat Tulis)" />
                        <TextInput v-model="form.category" type="text" class="block w-full" required />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel value="Nominal (Rp)" />
                        <CurrencyInput v-model="form.amount" class="block w-full border-gray-300 rounded-md shadow-sm" placeholder="0" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel value="Tanggal Transaksi" />
                        <TextInput v-model="form.transaction_date" type="date" class="block w-full" required />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel value="Keterangan Tambahan" />
                        <textarea v-model="form.description" class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 transition-all" rows="2"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <SecondaryButton @click="showingModal = false">Batal</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Simpan Transaksi</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
