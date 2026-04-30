<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Toast from '@/Components/Toast.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen bg-white dark:bg-gray-950">
        <!-- Left Side: Image (Hidden on small screens) -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-green-900">
            <img 
                src="/images/auth_bg.png" 
                alt="Background" 
                class="absolute inset-0 h-full w-full object-cover opacity-60 mix-blend-overlay"
            />
            <div class="relative z-10 flex flex-col items-center justify-center w-full p-12 text-white">
                <Link href="/">
                    <div v-if="$page.props.settings?.logo_url" class="bg-white p-4 rounded-3xl shadow-xl mb-8 transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                        <img :src="$page.props.settings.logo_url" class="h-20 w-auto" alt="Logo Sekolah" />
                    </div>
                    <ApplicationLogo v-else class="h-24 w-24 fill-current text-white mb-8" />
                </Link>
                <h1 class="text-4xl font-extrabold tracking-tight mb-4">Sistem Keuangan Sekolah</h1>
                <p class="text-xl text-green-100 text-center max-w-md">
                    Manajemen keuangan sekolah yang terintegrasi, transparan, dan profesional.
                </p>
                <div class="mt-12 grid grid-cols-1 gap-6 w-full max-w-md">
                    <div class="p-4 bg-white/10 backdrop-blur-md rounded-xl border border-white/20 flex items-center space-x-4">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-white">Terautentikasi</div>
                            <div class="text-sm text-green-200">Akses aman dan terenkripsi.</div>
                        </div>
                    </div>
                    <div class="p-4 bg-white/10 backdrop-blur-md rounded-xl border border-white/20 flex items-center space-x-4">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-white">Transparan</div>
                            <div class="text-sm text-green-200">Laporan keuangan real-time.</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Decorative Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-tr from-green-950/80 via-transparent to-transparent"></div>
        </div>

        <!-- Right Side: Auth Form -->
        <div class="flex w-full flex-col items-center justify-center px-6 lg:w-1/2 dark:bg-gray-900">
            <div class="w-full max-w-md">
                <!-- Logo for mobile -->
                <div class="flex justify-center lg:hidden mb-8">
                    <Link href="/">
                        <img v-if="$page.props.settings?.logo_url" :src="$page.props.settings.logo_url" class="h-16 w-auto" alt="Logo" />
                        <ApplicationLogo v-else class="h-16 w-16 fill-current text-green-600 dark:text-green-400" />
                    </Link>
                </div>
                
                <div class="mb-8">
                    <slot name="header_text" />
                </div>

                <div class="overflow-hidden bg-white px-8 py-10 shadow-2xl rounded-2xl dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                    <slot />
                </div>
                
                <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    &copy; 2026 Sistem Keuangan Sekolah. Seluruh hak cipta dilindungi.
                </div>
            </div>
        </div>
        <Toast />
    </div>
</template>
