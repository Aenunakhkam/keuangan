<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, inject } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps<{
    users: any;
    roles: any[];
}>();

const showingUserModal = ref(false);
const editingUser = ref<any>(null);
import { confirmDelete } from '@/Utils/sweetalert';

const confirmAction = (user: any) => {
    return confirmDelete(
        'Hapus User',
        `Apakah Anda yakin ingin menghapus user ${user.name}?`
    );
};

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
});

const openCreateModal = () => {
    editingUser.value = null;
    form.reset();
    showingUserModal.value = true;
};

const openEditModal = (user: any) => {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.role = user.roles[0]?.name || '';
    form.password = '';
    form.password_confirmation = '';
    showingUserModal.value = true;
};

const closeModal = () => {
    showingUserModal.value = false;
    form.reset();
};

const submit = () => {
    if (editingUser.value) {
        form.put(route('users.update', editingUser.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('users.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteUser = async (user: any) => {
    const result = await confirmAction(user);

    if (result.isConfirmed) {
        router.delete(route('users.destroy', user.id));
    }
};
</script>

<template>
    <Head title="Kelola Pengguna" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-white leading-tight">Kelola Pengguna</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium">Daftar Pengguna</h3>
                            <PrimaryButton @click="openCreateModal">
                                Tambah User
                            </PrimaryButton>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#003B73] text-white">
                                    <tr>
                                        <th class="px-6 py-3 text-center w-16 text-xs font-black text-white uppercase tracking-wider">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-black text-white uppercase tracking-wider">Nama</th>
                                        <th class="px-6 py-3 text-left text-xs font-black text-white uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-black text-white uppercase tracking-wider">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-black text-white uppercase tracking-wider text-right pr-12">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(user, index) in users.data" :key="user.id">
                                        <td class="px-6 py-4 text-center font-bold text-gray-900">{{ (users.current_page - 1) * users.per_page + index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ user.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ user.email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ user.roles[0]?.name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right pr-6 space-x-2">
                                            <button @click="openEditModal(user)" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button @click="deleteUser(user)" class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination (Simple) -->
                        <div class="mt-4 flex justify-end">
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                <template v-for="(link, k) in users.links" :key="k">
                                    <template v-if="link.url">
                                        <Link :href="link.url" v-html="link.label" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium" :class="link.active ? 'bg-indigo-50 border-indigo-500 text-indigo-600 z-10' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'" />
                                    </template>
                                    <template v-else>
                                        <span v-html="link.label" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-300" />
                                    </template>
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Modal -->
        <Modal :show="showingUserModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ editingUser ? 'Edit User' : 'Tambah User Baru' }}
                </h2>

                <form @submit.prevent="submit" class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="name" value="Nama" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="role" value="Role" />
                        <select id="role" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" v-model="form.role" required>
                            <option value="">Pilih Role</option>
                            <option v-for="role in roles" :key="role.id" :value="role.name">
                                {{ role.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.role" />
                    </div>

                    <div v-if="!editingUser">
                        <InputLabel for="password" value="Password" />
                        <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div v-if="!editingUser">
                        <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                        <TextInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div v-if="editingUser" class="bg-gray-50 p-4 rounded-md">
                        <p class="text-sm text-gray-600 mb-2">Biarkan password kosong jika tidak ingin mengubahnya.</p>
                        <div>
                            <InputLabel for="edit_password" value="Password Baru" />
                            <TextInput id="edit_password" type="password" class="mt-1 block w-full" v-model="form.password" />
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>
                        <div class="mt-4">
                            <InputLabel for="edit_password_confirmation" value="Konfirmasi Password Baru" />
                            <TextInput id="edit_password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <SecondaryButton @click="closeModal" class="mr-3">
                            Batal
                        </SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Simpan
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
