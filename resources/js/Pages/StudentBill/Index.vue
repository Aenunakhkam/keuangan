<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import debounce from 'lodash/debounce';
import { confirmDelete } from '@/Utils/sweetalert';

const props = defineProps<{
    bills: any;
    students: any[];
    feeTypes: any[];
    filters: any;
}>();

const showingModal = ref(false);
const showingBulkModal = ref(false);
const modalMode = ref('create'); 
const selectedBill = ref<any>(null);

const search = ref(props.filters.search);
const status = ref(props.filters.status);

const form = useForm({
    student_id: '',
    fee_type_id: '',
    amount: 0,
    due_date: '',
    status: 'unpaid',
});

const bulkForm = useForm({
    fee_type_id: '',
    due_date: '',
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    form.clearErrors();
    showingModal.value = true;
};

const openEditModal = (bill: any) => {
    modalMode.value = 'edit';
    selectedBill.value = bill;
    form.student_id = bill.student_id;
    form.fee_type_id = bill.fee_type_id;
    form.amount = bill.amount;
    form.due_date = bill.due_date;
    form.status = bill.status;
    form.clearErrors();
    showingModal.value = true;
};

const submit = () => {
    if (modalMode.value === 'create') {
        form.post(route('student-bills.store'), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('student-bills.update', selectedBill.value.id), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    }
};

const submitBulk = () => {
    bulkForm.post(route('student-bills.generate-bulk'), {
        onSuccess: () => {
            showingBulkModal.value = false;
            bulkForm.reset();
        },
    });
};

const deleteBill = async (bill: any) => {
    const result = await confirmDelete('Hapus Tagihan', `Hapus tagihan ${bill.fee_type?.name} untuk siswa ${bill.student?.name}?`);
    if (result.isConfirmed) {
        router.delete(route('student-bills.destroy', bill.id));
    }
};

const statusLabel = (s: string) => {
    const labels: any = {
        unpaid: 'Belum Bayar',
        partial: 'Cicilan',
        paid: 'Lunas',
    };
    return labels[s] || s;
};

const statusClass = (s: string) => {
    switch(s) {
        case 'paid': return 'bg-emerald-50 text-emerald-600';
        case 'partial': return 'bg-amber-50 text-amber-600';
        default: return 'bg-rose-50 text-rose-600';
    }
};

const showingPaymentModal = ref(false);
const paymentForm = useForm({
    student_bill_id: '',
    amount: 0,
    payment_date: new Date().toISOString().split('T')[0],
    payment_method: 'cash',
    note: '',
});

const openPaymentModal = (bill: any) => {
    paymentForm.student_bill_id = bill.id;
    paymentForm.amount = bill.amount; // Should ideally be remaining balance
    paymentForm.clearErrors();
    showingPaymentModal.value = true;
};

const updateAmount = () => {
    const fee = props.feeTypes.find(t => t.id == form.fee_type_id);
    if (fee) {
        form.amount = fee.default_amount;
    }
};

watch(search, debounce((value) => {
    router.get(route('student-bills.index'), { search: value, status: status.value }, { preserveState: true, replace: true });
}, 300));

watch(status, (value) => {
    router.get(route('student-bills.index'), { search: search.value, status: value }, { preserveState: true, replace: true });
});

const submitPayment = () => {
    paymentForm.post(route('payments.store'), {
        onSuccess: () => {
            showingPaymentModal.value = false;
            paymentForm.reset();
        },
    });
};
</script>

<template>
    <Head title="Tagihan Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Tagihan Siswa</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Kelola dan pantau seluruh tagihan pembayaran siswa.</p>
                </div>
                <div class="flex space-x-3">
                    <button 
                        @click="showingBulkModal = true"
                        class="px-5 py-3 bg-white border border-gray-200 rounded-2xl text-sm font-black shadow-sm transition-all hover:bg-gray-50 flex items-center space-x-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Generate Massal</span>
                    </button>
                    <button 
                        @click="openCreateModal"
                        class="px-5 py-3 bg-[#003B73] text-white rounded-2xl text-sm font-black shadow-xl shadow-blue-500/20 transform transition-transform hover:scale-[1.02] active:scale-[0.98] flex items-center space-x-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Buat Tagihan</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="md:col-span-2">
                    <TextInput v-model="search" placeholder="Cari Siswa atau NIS..." class="block w-full" />
                </div>
                <div>
                    <select v-model="status" class="block w-full px-5 py-3 rounded-2xl border-gray-200 bg-gray-50 dark:bg-gray-900 border-none ring-1 ring-gray-100 dark:ring-gray-700 font-bold focus:ring-4 focus:ring-indigo-500/10">
                        <option value="">Semua Status</option>
                        <option value="unpaid">Belum Bayar</option>
                        <option value="partial">Cicilan</option>
                        <option value="paid">Lunas</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#003B73] border-b border-indigo-800">
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center w-16">No</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Siswa</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Jenis Tagihan</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Jumlah</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Jatuh Tempo</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Status</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            <tr v-for="(bill, index) in bills.data" :key="bill.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-gray-300">{{ (bills.current_page - 1) * bills.per_page + index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ bill.student?.name || 'Siswa Terhapus' }}</div>
                                    <div class="text-xs text-gray-400 font-medium">{{ bill.student?.nis || '-' }}</div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300">{{ bill.fee_type?.name || 'Tipe Terhapus' }}</td>
                                <td class="px-6 py-4 font-black">Rp {{ Number(bill.amount || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</td>
                                <td class="px-6 py-4 text-gray-500 font-medium">{{ new Date(bill.due_date).toLocaleDateString() }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider" :class="statusClass(bill.status)">
                                        {{ statusLabel(bill.status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button v-if="bill.status !== 'paid'" @click="openPaymentModal(bill)" class="p-2 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-xl transition-all" title="Bayar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </button>
                                    <button @click="openEditModal(bill)" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteBill(bill)" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="bills.data.length === 0">
                                <td colspan="6" class="px-6 py-20 text-center text-gray-500">Belum ada data tagihan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Manual Bill Modal -->
        <Modal :show="showingModal" @close="showingModal = false">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">
                    {{ modalMode === 'create' ? 'Buat Tagihan Baru' : 'Edit Tagihan' }}
                </h2>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-1.5">
                        <InputLabel value="Pilih Siswa" />
                        <select v-model="form.student_id" class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                            <option v-for="s in students" :key="s.id" :value="s.id">{{ s.name }} ({{ s.nis }})</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <InputLabel value="Jenis Tagihan" />
                        <select v-model="form.fee_type_id" @change="updateAmount" class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                            <option v-for="t in feeTypes" :key="t.id" :value="t.id">{{ t.name }}</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <InputLabel value="Jumlah Tagihan (Rp)" />
                        <TextInput v-model="form.amount" type="number" class="block w-full" />
                    </div>
                    <div class="space-y-1.5">
                        <InputLabel value="Tanggal Jatuh Tempo" />
                        <TextInput v-model="form.due_date" type="date" class="block w-full" />
                    </div>
                    <div class="space-y-1.5">
                        <InputLabel value="Status" />
                        <select v-model="form.status" class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                            <option value="unpaid">Belum Bayar</option>
                            <option value="partial">Cicilan</option>
                            <option value="paid">Lunas</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <SecondaryButton @click="showingModal = false">Batal</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Simpan</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Bulk Generation Modal -->
        <Modal :show="showingBulkModal" @close="showingBulkModal = false">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">Generate Tagihan Massal</h2>
                <form @submit.prevent="submitBulk" class="space-y-6">
                    <div class="space-y-1.5">
                        <InputLabel value="Jenis Tagihan (Diberlakukan untuk SEMUA siswa)" />
                        <select v-model="bulkForm.fee_type_id" class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                            <option v-for="t in feeTypes" :key="t.id" :value="t.id">{{ t.name }}</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <InputLabel value="Tanggal Jatuh Tempo" />
                        <TextInput v-model="bulkForm.due_date" type="date" class="block w-full" />
                    </div>
                    <div class="flex justify-end space-x-3">
                        <SecondaryButton @click="showingBulkModal = false">Batal</SecondaryButton>
                        <PrimaryButton :disabled="bulkForm.processing">Generate Sekarang</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Payment Modal -->
        <Modal :show="showingPaymentModal" @close="showingPaymentModal = false">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">Catat Pembayaran</h2>
                <form @submit.prevent="submitPayment" class="space-y-6">
                    <div class="space-y-1.5">
                        <InputLabel value="Jumlah Pembayaran (Rp)" />
                        <TextInput v-model="paymentForm.amount" type="number" class="block w-full" required />
                        <InputError :message="paymentForm.errors.amount" />
                    </div>
                    <div class="space-y-1.5">
                        <InputLabel value="Tanggal Pembayaran" />
                        <TextInput v-model="paymentForm.payment_date" type="date" class="block w-full" required />
                        <InputError :message="paymentForm.errors.payment_date" />
                    </div>
                    <div class="space-y-1.5">
                        <InputLabel value="Metode Pembayaran" />
                        <select v-model="paymentForm.payment_method" class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                            <option value="cash">Tunai (Cash)</option>
                            <option value="transfer">Transfer Bank</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <InputLabel value="Catatan (Opsional)" />
                        <TextInput v-model="paymentForm.note" type="text" class="block w-full" />
                    </div>
                    <div class="flex justify-end space-x-3">
                        <SecondaryButton @click="showingPaymentModal = false">Batal</SecondaryButton>
                        <PrimaryButton :disabled="paymentForm.processing">Simpan Pembayaran</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
