<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch, inject } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import debounce from 'lodash/debounce';
import { confirmDelete } from '@/Utils/sweetalert';

const props = defineProps<{
    academicYears: any;
    filters: any;
}>();

const search = ref(props.filters?.search || '');

watch(search, debounce((value) => {
    router.get(route('academic-years.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

const showingModal = ref(false);
const modalMode = ref('create'); 
const selectedYear = ref<any>(null);

const form = useForm({
    year_name: '',
    semester: 1,
    is_active: false,
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    form.clearErrors();
    showingModal.value = true;
};

const openEditModal = (year: any) => {
    modalMode.value = 'edit';
    selectedYear.value = year;
    form.year_name = year.year_name;
    form.semester  = year.semester ?? 1;
    form.is_active = !!year.is_active;
    form.clearErrors();
    showingModal.value = true;
};

const submit = () => {
    if (modalMode.value === 'create') {
        form.post(route('academic-years.store'), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('academic-years.update', selectedYear.value.id), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    }
};

const deleteYear = async (year: any) => {
    const result = await confirmDelete(
        'Hapus Tahun Pelajaran',
        `Apakah Anda yakin ingin menghapus tahun pelajaran ${year.year_name}?`
    );

    if (result.isConfirmed) {
        router.delete(route('academic-years.destroy', year.id));
    }
};
</script>

<template>
    <Head title="Tahun Pelajaran" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Tahun Pelajaran</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Kelola periode aktif pendidikan.</p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="px-5 py-3 bg-[#003B73] text-white rounded-2xl text-sm font-black shadow-xl shadow-indigo-500/20 transform transition-transform hover:scale-[1.02] active:scale-[0.98] flex items-center space-x-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Tahun Pelajaran</span>
                </button>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Filter -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="max-w-md">
                    <TextInput v-model="search" placeholder="Cari Tahun Pelajaran..." class="block w-full" />
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-[#003B73] border-b border-indigo-800">
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center w-16">No</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Tahun Pelajaran</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Semester</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Status</th>
                                <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="(year, index) in academicYears" :key="year.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-gray-300">{{ index + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ year.year_name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase" :class="year.semester == 1 ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700'">
                                        {{ year.semester == 1 ? 'Ganjil' : 'Genap' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="year.is_active" class="px-3 py-1 bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400 rounded-full text-xs font-bold uppercase">Aktif</span>
                                    <span v-else class="px-3 py-1 bg-gray-50 text-gray-400 dark:bg-gray-900/30 dark:text-gray-500 rounded-full text-xs font-bold uppercase">Non-Aktif</span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button @click="openEditModal(year)" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteYear(year)" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="academicYears.length === 0">
                                <td colspan="5" class="px-6 py-20 text-center text-gray-500">Belum ada data tahun pelajaran.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Form -->
        <Modal :show="showingModal" @close="showingModal = false">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">
                    {{ modalMode === 'create' ? 'Tambah Tahun Pelajaran' : 'Edit Tahun Pelajaran' }}
                </h2>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-1.5">
                        <InputLabel for="year_name" value="Tahun Pelajaran" />
                        <TextInput id="year_name" v-model="form.year_name" type="text" class="block w-full" required placeholder="Contoh: 2024/2025" />
                        <InputError :message="form.errors.year_name" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="semester" value="Semester" />
                        <select id="semester" v-model="form.semester" class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                            <option :value="1">Semester 1 (Ganjil)</option>
                            <option :value="2">Semester 2 (Genap)</option>
                        </select>
                        <InputError :message="form.errors.semester" />
                    </div>

                    <div class="flex items-center space-x-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl">
                        <input type="checkbox" id="is_active" v-model="form.is_active" class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                        <label for="is_active" class="text-sm font-bold text-gray-700 dark:text-gray-300 cursor-pointer">Setel sebagai Tahun Aktif</label>
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
