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
    teachers: any;
    positions: any;
    filters: any;
}>();

const search = ref(props.filters?.search || '');

watch(search, debounce((value) => {
    router.get(route('teachers.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

const showingModal = ref(false);
const modalMode = ref('create'); 
const selectedTeacher = ref<any>(null);

const form = useForm({
    name: '',
    nip: '',
    phone: '',
    address: '',
    joined_date: '',
    position_ids: [] as number[],
    teaching_hours: 0,
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    form.clearErrors();
    showingModal.value = true;
};

const openEditModal = (teacher: any) => {
    modalMode.value = 'edit';
    selectedTeacher.value = teacher;
    form.name = teacher.name;
    form.nip = teacher.nip;
    form.phone = teacher.phone;
    form.address = teacher.address;
    form.joined_date = teacher.joined_date;
    form.position_ids = teacher.positions ? teacher.positions.map((p: any) => p.id) : [];
    form.teaching_hours = teacher.teaching_hours || 0;
    form.clearErrors();
    showingModal.value = true;
};

const submit = () => {
    if (modalMode.value === 'create') {
        form.post(route('teachers.store'), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('teachers.update', selectedTeacher.value.id), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    }
};

const deleteTeacher = async (teacher: any) => {
    const result = await confirmDelete(
        'Hapus Data Guru',
        `Apakah Anda yakin ingin menghapus data guru ${teacher.name}? Tindakan ini tidak dapat dibatalkan.`
    );

    if (result.isConfirmed) {
        router.delete(route('teachers.destroy', teacher.id));
    }
};
</script>

<template>
    <Head title="Data Guru" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Data Guru</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Kelola data tenaga pengajar di sini.</p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="px-5 py-3 bg-[#003B73] text-white rounded-2xl text-sm font-black shadow-xl shadow-indigo-500/20 transform transition-transform hover:scale-[1.02] active:scale-[0.98] flex items-center space-x-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Guru</span>
                </button>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Filter -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="max-w-md">
                    <TextInput v-model="search" placeholder="Cari Guru atau NIP..." class="block w-full" />
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#003B73] border-b border-indigo-800">
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center w-16">No</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">NIP</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Nama</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Jabatan / Jam</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="(teacher, index) in teachers.data" :key="teacher.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                            <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-gray-300">{{ (teachers.current_page - 1) * teachers.per_page + index + 1 }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ teacher.nip || '-' }}</td>
                            <td class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300">
                                <div>{{ teacher.name }}</div>
                                <div class="text-xs text-gray-500 font-bold uppercase mt-1">{{ teacher.phone || '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div v-if="teacher.positions && teacher.positions.length > 0" class="flex flex-wrap gap-1 mb-1">
                                    <span v-for="pos in teacher.positions" :key="pos.id" class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ pos.name }} (Rp {{ Number(pos.allowance).toLocaleString('id-ID', {maximumFractionDigits: 0}) }})
                                    </span>
                                </div>
                                <div v-else class="text-xs text-gray-400 italic mb-1">Tanpa Jabatan</div>
                                <div class="text-xs text-emerald-600 font-bold">{{ teacher.teaching_hours }} Jam Mengajar</div>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="openEditModal(teacher)" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button @click="deleteTeacher(teacher)" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="teachers.data.length === 0">
                            <td colspan="5" class="px-6 py-20 text-center text-gray-500">Belum ada data guru.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form -->
        <Modal :show="showingModal" @close="showingModal = false">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">
                    {{ modalMode === 'create' ? 'Tambah Guru Baru' : 'Edit Data Guru' }}
                </h2>

                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5 md:col-span-2">
                        <InputLabel for="name" value="Nama Lengkap" />
                        <TextInput id="name" v-model="form.name" type="text" class="block w-full" required />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="nip" value="NIP" />
                        <TextInput id="nip" v-model="form.nip" type="text" class="block w-full" />
                        <InputError :message="form.errors.nip" />
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <InputLabel value="Pengaturan Jabatan & Gaji" class="text-xs font-black uppercase tracking-widest text-indigo-600 border-b border-indigo-100 pb-2 mb-2" />
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <InputLabel for="position_ids" value="Jabatan Struktural (Bisa Pilih Lebih Dari Satu)" />
                        <div class="space-y-2 mt-2">
                            <label v-for="pos in positions" :key="pos.id" class="flex items-center space-x-3 cursor-pointer p-3 rounded-2xl border border-gray-100 hover:bg-gray-50 transition-colors">
                                <input type="checkbox" :value="pos.id" v-model="form.position_ids" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-5 h-5 transition-all">
                                <div>
                                    <span class="block text-sm font-bold text-gray-900">{{ pos.name }}</span>
                                    <span class="block text-xs font-medium text-emerald-600">+ Rp {{ Number(pos.allowance).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</span>
                                </div>
                            </label>
                            <InputError :message="form.errors.position_ids" />
                        </div>
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <InputLabel for="teaching_hours" value="Jumlah Jam Mengajar" />
                        <TextInput id="teaching_hours" v-model="form.teaching_hours" type="number" min="0" class="block w-full" />
                        <p class="text-[10px] text-gray-500"><span class="font-bold text-emerald-600">Gaji Pokok otomatis:</span> (Jumlah Jam &times; Honor Per Jam)</p>
                        <InputError :message="form.errors.teaching_hours" />
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <InputLabel value="Data Pribadi" class="text-xs font-black uppercase tracking-widest text-indigo-600 border-b border-indigo-100 pb-2 mb-2 mt-4" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="phone" value="No. Telepon" />
                        <TextInput id="phone" v-model="form.phone" type="text" class="block w-full" />
                        <InputError :message="form.errors.phone" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="joined_date" value="Tanggal Bergabung" />
                        <TextInput id="joined_date" v-model="form.joined_date" type="date" class="block w-full" />
                        <InputError :message="form.errors.joined_date" />
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <InputLabel for="address" value="Alamat Lengkap" />
                        <textarea 
                            id="address" 
                            v-model="form.address" 
                            class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200"
                            rows="3"
                        ></textarea>
                        <InputError :message="form.errors.address" />
                    </div>

                    <div class="md:col-span-2 mt-4 flex justify-end space-x-3">
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
