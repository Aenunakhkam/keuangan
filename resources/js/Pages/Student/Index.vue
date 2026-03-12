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
import DangerButton from '@/Components/DangerButton.vue';
import debounce from 'lodash/debounce';
import { confirmDelete } from '@/Utils/sweetalert';

const props = defineProps<{
    students: any;
    classRooms: any[];
    filters: any;
}>();

const search = ref(props.filters?.search || '');

watch(search, debounce((value) => {
    router.get(route('students.index'), { search: value }, { preserveState: true, replace: true });
}, 300));

const showingModal = ref(false);
const modalMode = ref('create'); // 'create' or 'edit'
const selectedStudent = ref<any>(null);

const form = useForm({
    name: '',
    nis: '',
    class_room_id: '',
    gender: 'L',
    address: '',
    phone: '',
    parent_name: '',
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    form.clearErrors();
    showingModal.value = true;
};

const openEditModal = (student: any) => {
    modalMode.value = 'edit';
    selectedStudent.value = student;
    form.name = student.name;
    form.nis = student.nis;
    form.class_room_id = student.class_room_id;
    form.gender = student.gender;
    form.address = student.address;
    form.phone = student.phone;
    form.parent_name = student.parent_name;
    form.clearErrors();
    showingModal.value = true;
};

const submit = () => {
    if (modalMode.value === 'create') {
        form.post(route('students.store'), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('students.update', selectedStudent.value.id), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    }
};

const deleteStudent = async (student: any) => {
    const result = await confirmDelete(
        'Hapus Data Siswa',
        `Apakah Anda yakin ingin menghapus data siswa ${student.name}? Tindakan ini tidak dapat dibatalkan.`
    );

    if (result.isConfirmed) {
        router.delete(route('students.destroy', student.id));
    }
};
</script>

<template>
    <Head title="Data Siswa" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Data Siswa</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Kelola seluruh informasi siswa di sini.</p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="px-5 py-3 bg-[#003B73] text-white rounded-2xl text-sm font-black shadow-xl shadow-indigo-500/20 transform transition-transform hover:scale-[1.02] active:scale-[0.98] flex items-center space-x-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Siswa</span>
                </button>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Filter -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="max-w-md">
                    <TextInput v-model="search" placeholder="Cari Siswa atau NIS..." class="block w-full" />
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#003B73] border-b border-indigo-800">
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center w-16">No</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">NIS</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Nama</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Kelas</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">L/P</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="(student, index) in students.data" :key="student.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                            <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-gray-300">{{ (students.current_page - 1) * students.per_page + index + 1 }}</td>
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ student.nis }}</td>
                            <td class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300">{{ student.name }}</td>
                            <td class="px-6 py-4"><span class="px-3 py-1 bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 rounded-full text-xs font-bold">{{ student.class_room?.name || 'N/A' }}</span></td>
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 font-bold uppercase">{{ student.gender }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="openEditModal(student)" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button @click="deleteStudent(student)" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="students.data.length === 0">
                            <td colspan="6" class="px-6 py-20 text-center text-gray-500">Belum ada data siswa.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Form -->
        <Modal :show="showingModal" @close="showingModal = false">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">
                    {{ modalMode === 'create' ? 'Tambah Siswa Baru' : 'Edit Data Siswa' }}
                </h2>

                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5 md:col-span-2">
                        <InputLabel for="name" value="Nama Lengkap" />
                        <TextInput id="name" v-model="form.name" type="text" class="block w-full" required />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="nis" value="NIS" />
                        <TextInput id="nis" v-model="form.nis" type="text" class="block w-full" required />
                        <InputError :message="form.errors.nis" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="class_room_id" value="Kelas" />
                        <select 
                            id="class_room_id" 
                            v-model="form.class_room_id" 
                            class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200"
                            required
                        >
                            <option value="">Pilih Kelas</option>
                            <option v-for="room in classRooms" :key="room.id" :value="room.id">{{ room.name }}</option>
                        </select>
                        <InputError :message="form.errors.class_room_id" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="gender" value="Jenis Kelamin" />
                        <select 
                            id="gender" 
                            v-model="form.gender" 
                            class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200"
                        >
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        <InputError :message="form.errors.gender" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="phone" value="No. Telepon" />
                        <TextInput id="phone" v-model="form.phone" type="text" class="block w-full" />
                        <InputError :message="form.errors.phone" />
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <InputLabel for="parent_name" value="Nama Orang Tua / Wali" />
                        <TextInput id="parent_name" v-model="form.parent_name" type="text" class="block w-full" />
                        <InputError :message="form.errors.parent_name" />
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
