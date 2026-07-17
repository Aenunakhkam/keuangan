<script setup lang="ts">
import { formatRupiah } from '@/Utils/formatRupiah';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
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
    positions: any;
    filters: any;
}>();

const search = ref(props.filters?.search || '');
const perPage = ref(props.filters?.per_page || '10');

watch(search, debounce((value) => {
    router.get(route('positions.index'), { search: value, per_page: perPage.value }, { preserveState: true, replace: true });
}, 300));

watch(perPage, (value) => {
    router.get(route('positions.index'), { search: search.value, per_page: value }, { preserveState: true, replace: true });
});

const showingModal = ref(false);
const modalMode = ref('create'); 
const selectedPosition = ref<any>(null);

const form = useForm({
    name: '',
    allowance: 0,
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    form.clearErrors();
    showingModal.value = true;
};

const openEditModal = (position: any) => {
    modalMode.value = 'edit';
    selectedPosition.value = position;
    form.name = position.name;
    form.allowance = position.allowance;
    form.clearErrors();
    showingModal.value = true;
};

const submit = () => {
    if (modalMode.value === 'create') {
        form.post(route('positions.store'), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('positions.update', selectedPosition.value.id), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    }
};

const deletePosition = async (position: any) => {
    const result = await confirmDelete(
        'Hapus Data Jabatan',
        `Apakah Anda yakin ingin menghapus jabatan ${position.name}?`
    );

    if (result.isConfirmed) {
        router.delete(route('positions.destroy', position.id));
    }
};
</script>

<template>
    <Head title="Data Jabatan Struktural" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Data Jabatan</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Kelola daftar jabatan struktural dan tunjangannya (Wali Kelas, Waka, dll).</p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="px-5 py-3 bg-[#003B73] text-white rounded-2xl text-sm font-black shadow-xl shadow-indigo-500/20 transform transition-transform hover:scale-[1.02] active:scale-[0.98] flex items-center space-x-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Jabatan</span>
                </button>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Filter -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="max-w-md w-full">
                    <TextInput v-model="search" placeholder="Cari Jabatan..." class="block w-full" />
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm font-bold text-gray-500">Lihat:</span>
                    <select 
                        v-model="perPage"
                        class="rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm font-bold text-gray-700 dark:text-gray-300 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all px-4 py-2.5"
                    >
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-[#003B73] border-b border-indigo-800">
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center w-16">No</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Nama Jabatan</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Tunjangan Jabatan</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                             <tr v-for="(pos, index) in positions.data" :key="pos.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-gray-300">{{ (positions.current_page - 1) * positions.per_page + index + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ pos.name }}</td>
                                <td class="px-6 py-4 font-black text-emerald-600 dark:text-emerald-400 text-right">{{ formatRupiah(pos.allowance || 0) }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button @click="openEditModal(pos)" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="deletePosition(pos)" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="positions.data.length === 0">
                                <td colspan="4" class="px-6 py-20 text-center text-gray-500 font-bold">Belum ada data jabatan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Bar -->
                <div v-if="positions.links && positions.links.length > 3" class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <div class="text-sm text-gray-500 font-medium">
                        Menampilkan <span class="font-black text-gray-900">{{ positions.from || 0 }}</span> s.d <span class="font-black text-gray-900">{{ positions.to || 0 }}</span> dari <span class="font-black text-gray-900">{{ positions.total }}</span> jabatan
                    </div>
                    <div class="flex items-center space-x-1">
                        <template v-for="(link, k) in positions.links" :key="k">
                            <div v-if="link.url === null" class="px-4 py-2 text-sm text-gray-400 border border-transparent" v-html="link.label"></div>
                            <Link 
                                v-else 
                                :href="link.url" 
                                class="px-4 py-2 text-sm font-black rounded-xl transition-all" 
                                :class="link.active ? 'bg-[#003B73] text-white shadow-lg' : 'text-gray-600 hover:bg-white hover:shadow-md border border-transparent hover:border-gray-100'" 
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Form -->
        <Modal :show="showingModal" @close="showingModal = false">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">
                    {{ modalMode === 'create' ? 'Tambah Jabatan' : 'Edit Jabatan' }}
                </h2>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-1.5">
                        <InputLabel for="name" value="Nama Jabatan" />
                        <TextInput id="name" v-model="form.name" type="text" list="position-options" class="block w-full" required placeholder="Contoh: Wali Kelas" />
                        <datalist id="position-options">
                            <option value="Kepala Sekolah"></option>
                            <option value="Wakil Kepala Sekolah Kurikulum"></option>
                            <option value="Wakil Kepala Sekolah Kesiswaan"></option>
                            <option value="Wakil Kepala Sekolah Sarana dan Prasarana"></option>
                            <option value="Wakil Kepala Sekolah Humas"></option>
                            <option value="Kepala Tata Usaha"></option>
                            <option value="Bendahara Sekolah"></option>
                            <option value="Guru Mata Pelajaran"></option>
                            <option value="Wali Kelas"></option>
                            <option value="Guru Bimbingan Konseling (BK)"></option>
                            <option value="Staff Tata Usaha"></option>
                            <option value="Operator Sekolah"></option>
                            <option value="Petugas Perpustakaan"></option>
                            <option value="Laboran"></option>
                            <option value="Teknisi / IT Support"></option>
                            <option value="Satpam"></option>
                            <option value="Petugas Kebersihan"></option>
                        </datalist>
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="allowance" value="Nominal Tunjangan Jabatan (Rp)" />
                        <CurrencyInput id="allowance" v-model="form.allowance" class="block w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: 250.000" />
                        <InputError :message="form.errors.allowance" />
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
