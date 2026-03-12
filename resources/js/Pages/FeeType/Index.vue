<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch, inject } from 'vue';
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
    feeTypes: any;
    filters: any;
}>();

const search = ref(props.filters?.search || '');

watch(search, debounce((value) => {
    router.get(route('fee-types.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

const showingModal = ref(false);
const modalMode = ref('create'); 
const selectedFeeType = ref<any>(null);

const form = useForm({
    name: '',
    default_amount: 0,
    period: 'monthly',
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    form.clearErrors();
    showingModal.value = true;
};

const openEditModal = (feeType: any) => {
    modalMode.value = 'edit';
    selectedFeeType.value = feeType;
    form.name = feeType.name;
    form.default_amount = feeType.default_amount;
    form.period = feeType.period;
    form.clearErrors();
    showingModal.value = true;
};

const submit = () => {
    if (modalMode.value === 'create') {
        form.post(route('fee-types.store'), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('fee-types.update', selectedFeeType.value.id), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    }
};

const deleteFeeType = async (feeType: any) => {
    const result = await confirmDelete(
        'Hapus Jenis Pembayaran',
        `Apakah Anda yakin ingin menghapus jenis pembayaran "${feeType.name}"?`
    );

    if (result.isConfirmed) {
        router.delete(route('fee-types.destroy', feeType.id));
    }
};

const periodLabel = (period: string) => {
    const labels: any = {
        monthly: 'Bulanan',
        yearly: 'Tahunan',
        once: 'Sekali Bayar',
    };
    return labels[period] || period;
};
</script>

<template>
    <Head title="Jenis Pembayaran" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Jenis Pembayaran</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Konfigurasi berbagai kategori tagihan sekolah.</p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="px-5 py-3 bg-[#003B73] text-white rounded-2xl text-sm font-black shadow-xl shadow-indigo-500/20 transform transition-transform hover:scale-[1.02] active:scale-[0.98] flex items-center space-x-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Kategori</span>
                </button>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Filter -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="max-w-md">
                    <TextInput v-model="search" placeholder="Cari Jenis Pembayaran..." class="block w-full" />
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#003B73] border-b border-indigo-800">
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center w-16">No</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Nama Tagihan</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Periode</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Nominal Default</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="(type, index) in feeTypes" :key="type.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                            <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-gray-300">{{ index + 1 }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ type.name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 rounded-full text-xs font-bold uppercase">{{ periodLabel(type.period) }}</span>
                            </td>
                            <td class="px-6 py-4 font-black text-gray-700 dark:text-gray-300">Rp {{ Number(type.default_amount || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="openEditModal(type)" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button @click="deleteFeeType(type)" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="feeTypes.length === 0">
                            <td colspan="5" class="px-6 py-20 text-center text-gray-500">Belum ada kategori pembayaran.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form -->
        <Modal :show="showingModal" @close="showingModal = false">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">
                    {{ modalMode === 'create' ? 'Tambah Kategori Baru' : 'Edit Kategori' }}
                </h2>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-1.5">
                        <InputLabel for="name" value="Nama Tagihan" />
                        <TextInput id="name" v-model="form.name" type="text" class="block w-full" required placeholder="Contoh: SPP Bulanan" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="default_amount" value="Nominal Default (Rp)" />
                        <CurrencyInput id="default_amount" v-model="form.default_amount" class="block w-full border-gray-300 rounded-md shadow-sm" />
                        <InputError :message="form.errors.default_amount" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="period" value="Periode Pembayaran" />
                        <select 
                            id="period" 
                            v-model="form.period" 
                            class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200"
                        >
                            <option value="monthly">Bulanan</option>
                            <option value="yearly">Tahunan</option>
                            <option value="once">Sekali Bayar</option>
                        </select>
                        <InputError :message="form.errors.period" />
                    </div>

                    <div class="mt-4 flex justify-end space-x-3">
                        <SecondaryButton @click="showingModal = false">Batal</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">
                            {{ modalMode === 'create' ? 'Simpan' : 'Perbarui' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
