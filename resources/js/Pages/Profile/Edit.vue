<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();

const activeTab = ref('profile');
</script>

<template>
    <Head title="Pengaturan Profil" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-indigo-600 rounded-2xl shadow-lg shadow-indigo-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Pengaturan Akun</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Kelola informasi profil, keamanan, dan privasi Anda.</p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Sidebar Navigation -->
                    <div class="lg:col-span-4 lg:sticky lg:top-24">
                        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div class="p-8 pb-4">
                                <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Navigasi Pengaturan</h3>
                                <nav class="space-y-2">
                                    <button 
                                        @click="activeTab = 'profile'"
                                        class="w-full flex items-center space-x-3 px-4 py-3.5 rounded-2xl transition-all duration-200 group text-left"
                                        :class="activeTab === 'profile' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-700/50'"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="activeTab === 'profile' ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="font-bold">Informasi Profil</span>
                                    </button>

                                    <button 
                                        @click="activeTab = 'password'"
                                        class="w-full flex items-center space-x-3 px-4 py-3.5 rounded-2xl transition-all duration-200 group text-left"
                                        :class="activeTab === 'password' ? 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-700/50'"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="activeTab === 'password' ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        <span class="font-bold">Keamanan Password</span>
                                    </button>

                                    <button 
                                        @click="activeTab = 'danger'"
                                        class="w-full flex items-center space-x-3 px-4 py-3.5 rounded-2xl transition-all duration-200 group text-left"
                                        :class="activeTab === 'danger' ? 'bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400' : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-700/50'"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="activeTab === 'danger' ? 'text-rose-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="font-bold">Area Berbahaya</span>
                                    </button>
                                </nav>
                            </div>
                            
                            <div class="p-8 bg-gray-50 dark:bg-gray-900 border-t border-gray-100 dark:border-gray-700">
                                <div class="flex items-center space-x-4">
                                    <div class="h-12 w-12 rounded-full overflow-hidden border-2 border-indigo-200">
                                        <img src="https://ui-avatars.com/api/?name=Admin&background=random" alt="Avatar">
                                    </div>
                                    <div>
                                        <div class="font-black text-gray-900 dark:text-white">{{ $page.props.auth.user.name }}</div>
                                        <div class="text-xs text-gray-500 font-bold truncate max-w-[150px]">{{ $page.props.auth.user.email || 'Pengguna Aktif' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Panel -->
                    <div class="lg:col-span-8">
                        <div v-if="activeTab === 'profile'" class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 sm:p-10 transition-all duration-300">
                            <UpdateProfileInformationForm
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                            />
                        </div>

                        <div v-if="activeTab === 'password'" class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 sm:p-10 transition-all duration-300">
                            <UpdatePasswordForm />
                        </div>

                        <div v-if="activeTab === 'danger'" class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 sm:p-10 transition-all duration-300">
                            <DeleteUserForm />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
