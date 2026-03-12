<script setup lang="ts">
import { ref, provide } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import Toast from '@/Components/Toast.vue';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const activeMobileMenu = ref<string | null>(null);

const toggleMobileMenu = (menu: string) => {
    activeMobileMenu.value = activeMobileMenu.value === menu ? null : menu;
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950 font-sans antialiased">
        <!-- Top Navigation Bar (Blue Theme) -->
        <nav class="bg-[#003B73] text-white shadow-lg sticky top-0 z-50">
            <!-- Premium Mesh Aura Overlay -->
            <div class="absolute inset-0 pointer-events-none mesh-aura"></div>
            <div class="absolute inset-0 pointer-events-none noise-overlay"></div>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Branding & Utilities Row -->
                <div class="flex h-20 items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Logo -->
                        <div class="flex shrink-0 items-center bg-white p-2 rounded-xl shadow-sm relative z-10">
                            <Link :href="route('dashboard')">
                                <img v-if="$page.props.settings?.logo_url" :src="$page.props.settings.logo_url" class="block h-10 w-auto" />
                                <ApplicationLogo v-else class="block h-10 w-auto fill-current text-[#003B73]" />
                            </Link>
                        </div>
                        <!-- App Name -->
                        <div class="hidden sm:block relative z-10">
                            <div class="text-xs font-medium text-indigo-200 uppercase tracking-widest leading-tight">
                                {{ $page.props.settings?.app_name || 'SIM Keuangan' }}
                            </div>
                            <div class="text-xl font-extrabold tracking-tight">
                                {{ $page.props.settings?.school_name || 'Universitas Keuangan Sekolah' }}
                            </div>
                        </div>
                    </div>

                    <!-- Right Utilities -->
                    <div class="flex items-center space-x-4">
                        <!-- Notification Icon -->
                        <button class="p-2 text-indigo-100 hover:text-white transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>

                        <!-- Profile Dropdown -->
                        <div class="relative flex items-center">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button type="button" class="flex items-center space-x-3 p-1.5 rounded-full hover:bg-white/10 transition-colors duration-200 border border-white/10">
                                        <div class="h-9 w-9 bg-gray-200 rounded-full overflow-hidden flex-shrink-0 border-2 border-indigo-400">
                                            <img src="https://ui-avatars.com/api/?name=Admin&background=random" alt="User Avatar">
                                        </div>
                                        <span class="hidden md:inline-block text-sm font-bold pr-2">{{ $page.props.auth.user.name }}</span>
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>
                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">
                                        Profil Pengguna
                                    </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">
                                        Keluar Sistem
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>

                <!-- Main Menu Row (Horizontal) -->
                <div class="hidden lg:flex border-t border-white/10 pb-0.5">
                    <div class="flex space-x-1 py-1">
                        <!-- Dashboard -->
                        <Link :href="route('dashboard')" 
                            class="px-5 py-3 text-sm font-bold rounded-xl transition-all duration-300 flex items-center space-x-2 glass-nav"
                            :class="route().current('dashboard') ? 'bg-white/10 text-white shadow-sm ring-1 ring-white/20' : 'text-indigo-100 hover:bg-white/10 hover:text-white'">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Dashboard</span>
                        </Link>

                        <!-- Data Master Dropdown (Consolidated Master & Akademik) -->
                        <Dropdown align="left" width="56" v-if="$page.props.auth.user.roles.includes('admin')">
                            <template #trigger>
                                <button class="px-5 py-3 text-sm font-bold rounded-xl flex items-center space-x-2 text-indigo-100 hover:bg-white/10 hover:text-white transition-all duration-300 glass-nav"
                                    :class="route().current('students.*') || route().current('teachers.*') || route().current('positions.*') || route().current('academic-years.*') || route().current('class-rooms.*') ? 'bg-white/10 text-white shadow-sm ring-1 ring-white/20' : ''">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <span>Data Master</span>
                                    <svg class="h-4 w-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <div class="px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 mb-1">Data Utama</div>
                                <DropdownLink :href="route('students.index')">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <span>Data Siswa</span>
                                    </div>
                                </DropdownLink>
                                <DropdownLink :href="route('teachers.index')">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span>Data Guru</span>
                                    </div>
                                </DropdownLink>
                                <DropdownLink :href="route('positions.index')">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>Data Jabatan</span>
                                    </div>
                                </DropdownLink>
                                <div class="px-4 py-2 mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 mb-1">Akademik</div>
                                <DropdownLink :href="route('academic-years.index')">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>Tahun Pelajaran</span>
                                    </div>
                                </DropdownLink>
                                <DropdownLink :href="route('class-rooms.index')">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>Data Kelas</span>
                                    </div>
                                </DropdownLink>
                            </template>
                        </Dropdown>

                        <!-- Keuangan Dropdown -->
                        <Dropdown align="left" width="56" v-if="$page.props.auth.user.roles.some(r => ['admin', 'bendahara'].includes(r))">
                            <template #trigger>
                                <button class="px-5 py-3 text-sm font-bold rounded-xl flex items-center space-x-2 text-indigo-100 hover:bg-white/10 hover:text-white transition-all duration-300 glass-nav"
                                    :class="route().current('fee-types.*') || route().current('student-bills.*') || route().current('payments.*') || route().current('salaries.*') || route().current('cash-transactions.*') ? 'bg-white/10 text-white shadow-sm ring-1 ring-white/20' : ''">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Keuangan</span>
                                    <svg class="h-4 w-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('fee-types.index')">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                        <span>Jenis Pembayaran</span>
                                    </div>
                                </DropdownLink>
                                <DropdownLink :href="route('student-bills.index')">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Tagihan Siswa</span>
                                    </div>
                                </DropdownLink>
                                <DropdownLink :href="route('payments.index')">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span>Transaksi Pembayaran</span>
                                    </div>
                                </DropdownLink>
                                <DropdownLink :href="route('salaries.index')">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <span>Penggajian (Payroll)</span>
                                    </div>
                                </DropdownLink>
                                <DropdownLink :href="route('cash-transactions.index')">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                        </svg>
                                        <span>Jurnal Kas</span>
                                    </div>
                                </DropdownLink>
                            </template>
                        </Dropdown>



                        <!-- Laporan -->
                        <Dropdown align="left" width="56" v-if="$page.props.auth.user.roles.some(r => ['admin', 'bendahara'].includes(r))">
                            <template #trigger>
                                <button class="px-5 py-3 text-sm font-bold rounded-xl flex items-center space-x-2 text-indigo-100 hover:bg-white/10 hover:text-white transition-all duration-300 glass-nav"
                                    :class="route().current('reports.*') ? 'bg-white/10 text-white shadow-sm ring-1 ring-white/20' : ''">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Laporan</span>
                                    <svg class="h-4 w-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('reports.index')">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        <span>Ringkasan Keuangan</span>
                                    </div>
                                </DropdownLink>
                                <DropdownLink :href="route('reports.transactions')">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span>Laporan Transaksi</span>
                                    </div>
                                </DropdownLink>
                            </template>
                        </Dropdown>

                        <!-- Pengaturan Dropdown (Admin Only) -->
                        <Dropdown align="left" width="56" v-if="$page.props.auth.user.roles.includes('admin')">
                            <template #trigger>
                                <button class="px-5 py-3 text-sm font-bold rounded-xl flex items-center space-x-2 text-indigo-100 hover:bg-white/10 hover:text-white transition-all duration-300 glass-nav"
                                    :class="route().current('settings.*') || route().current('users.*') ? 'bg-white/10 text-white shadow-sm ring-1 ring-white/20' : ''">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Pengaturan</span>
                                    <svg class="h-4 w-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('settings.index')">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        </svg>
                                        <span>Pengaturan Umum</span>
                                    </div>
                                </DropdownLink>
                                <DropdownLink :href="route('users.index')">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <span>Kelola Akun</span>
                                    </div>
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>

                <!-- Hamburger for mobile -->
                <div class="lg:hidden h-14 flex items-center">
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="text-white">
                        <svg class="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation Sidebar Modal -->
            <div v-show="showingNavigationDropdown" class="fixed inset-0 z-[100] flex lg:hidden" aria-modal="true" role="dialog">
                <!-- Overlay Backdrop -->
                <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="showingNavigationDropdown = false"></div>

                <!-- Sidebar Content -->
                <div class="relative flex w-full max-w-xs flex-1 flex-col bg-white h-full shadow-2xl overflow-y-auto transform transition-transform duration-300">
                    <!-- Close button (floating outside sidebar if possible, but inside is safer for mobile) -->
                    <div class="absolute top-0 right-0 -mr-12 pt-4 hidden sm:block">
                        <button type="button" @click="showingNavigationDropdown = false" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Header/Logo exactly like screenshot (White BG, Logo, App Name) -->
                    <div class="flex items-center space-x-4 px-6 py-6 border-b border-gray-100">
                        <img v-if="$page.props.settings?.logo_url" :src="$page.props.settings.logo_url" class="h-12 w-auto object-contain" />
                        <ApplicationLogo v-else class="h-12 w-auto text-[#003B73]" />
                        <div>
                            <div class="text-[10px] text-gray-400 font-medium uppercase tracking-widest">{{ $page.props.settings?.app_name || 'SIM Akademik' }}</div>
                            <div class="text-sm font-black text-[#003B73]">{{ $page.props.settings?.school_name || 'Universitas Harkat Negeri' }}</div>
                        </div>
                    </div>
                    
                    <button @click="showingNavigationDropdown = false" class="sm:hidden absolute top-6 right-6 p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>

                    <!-- User Profile Area Mobile -->
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center font-bold text-indigo-700">
                                {{ $page.props.auth.user.name.charAt(0) }}
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900">{{ $page.props.auth.user.name }}</div>
                                <Link :href="route('profile.edit')" class="text-xs text-indigo-600 font-medium hover:underline">Edit Profil</Link>
                            </div>
                        </div>
                        <Link :href="route('logout')" method="post" as="button" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        </Link>
                    </div>

                    <!-- Accordion Menus -->
                    <nav class="flex-1 overflow-y-auto w-full">
                        <div class="flex flex-col w-full">
                            
                            <!-- Beranda / Dashboard -->
                            <Link :href="route('dashboard')" class="w-full flex items-center space-x-3 text-left px-6 py-4 border-b border-gray-100 text-sm font-medium transition-colors" :class="route().current('dashboard') ? 'text-indigo-600 bg-indigo-50/50' : 'text-gray-700 hover:bg-gray-50'">
                                <svg class="h-5 w-5" :class="route().current('dashboard') ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                                <span>Beranda</span>
                            </Link>

                            <!-- Data Master Accordion (Consolidated) -->
                            <div class="w-full border-b border-gray-100" v-if="$page.props.auth.user.roles.includes('admin')">
                                <button @click="toggleMobileMenu('master')" class="w-full flex items-center justify-between px-6 py-4 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                        <span>Data Master</span>
                                    </div>
                                    <svg class="h-4 w-4 text-gray-400 transition-transform duration-300 ease-in-out" :class="{'rotate-180': activeMobileMenu === 'master'}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <transition
                                    enter-active-class="transition-[max-height,opacity] ease-in-out duration-300 overflow-hidden"
                                    enter-from-class="max-h-0 opacity-0"
                                    enter-to-class="max-h-[500px] opacity-100"
                                    leave-active-class="transition-[max-height,opacity] ease-in-out duration-300 overflow-hidden"
                                    leave-from-class="max-h-[500px] opacity-100"
                                    leave-to-class="max-h-0 opacity-0"
                                >
                                    <div v-show="activeMobileMenu === 'master'" class="bg-gray-50/50 pb-2">
                                        <div class="px-10 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Data Utama</div>
                                        <Link :href="route('students.index')" class="flex items-center space-x-3 w-full px-10 py-3 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white transition-colors" :class="{'text-indigo-600 font-semibold bg-white': route().current('students.*')}">
                                            <svg class="h-4 w-4" :class="route().current('students.*') ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                            <span>Data Siswa</span>
                                        </Link>
                                        <Link :href="route('teachers.index')" class="flex items-center space-x-3 w-full px-10 py-3 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white transition-colors" :class="{'text-indigo-600 font-semibold bg-white': route().current('teachers.*')}">
                                            <svg class="h-4 w-4" :class="route().current('teachers.*') ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                            <span>Data Guru</span>
                                        </Link>
                                        <Link :href="route('positions.index')" class="flex items-center space-x-3 w-full px-10 py-3 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white transition-colors" :class="{'text-indigo-600 font-semibold bg-white': route().current('positions.*')}">
                                            <svg class="h-4 w-4" :class="route().current('positions.*') ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                            <span>Data Jabatan</span>
                                        </Link>
                                        <div class="px-10 py-2 mt-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Akademik</div>
                                        <Link :href="route('academic-years.index')" class="flex items-center space-x-3 w-full px-10 py-3 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white transition-colors" :class="{'text-indigo-600 font-semibold bg-white': route().current('academic-years.*')}">
                                            <svg class="h-4 w-4" :class="route().current('academic-years.*') ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            <span>Tahun Pelajaran</span>
                                        </Link>
                                        <Link :href="route('class-rooms.index')" class="flex items-center space-x-3 w-full px-10 py-3 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white transition-colors" :class="{'text-indigo-600 font-semibold bg-white': route().current('class-rooms.*')}">
                                            <svg class="h-4 w-4" :class="route().current('class-rooms.*') ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                            <span>Data Kelas</span>
                                        </Link>
                                    </div>
                                </transition>
                            </div>
                            
                            <!-- Keuangan Accordion -->
                            <div class="w-full border-b border-gray-100" v-if="$page.props.auth.user.roles.some(r => ['admin', 'bendahara'].includes(r))">
                                <button @click="toggleMobileMenu('keuangan')" class="w-full flex items-center justify-between px-6 py-4 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span>Keuangan</span>
                                    </div>
                                    <svg class="h-4 w-4 text-gray-400 transition-transform duration-300 ease-in-out" :class="{'rotate-180': activeMobileMenu === 'keuangan'}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <transition
                                    enter-active-class="transition-[max-height,opacity] ease-in-out duration-300 overflow-hidden"
                                    enter-from-class="max-h-0 opacity-0"
                                    enter-to-class="max-h-96 opacity-100"
                                    leave-active-class="transition-[max-height,opacity] ease-in-out duration-300 overflow-hidden"
                                    leave-from-class="max-h-96 opacity-100"
                                    leave-to-class="max-h-0 opacity-0"
                                >
                                    <div v-show="activeMobileMenu === 'keuangan'" class="bg-gray-50/50 pb-2">
                                        <Link :href="route('fee-types.index')" class="flex items-center space-x-3 w-full px-10 py-3 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white transition-colors" :class="{'text-indigo-600 font-semibold bg-white': route().current('fee-types.*')}">
                                            <svg class="h-4 w-4" :class="route().current('fee-types.*') ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 00-2 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                                            <span>Jenis Pembayaran</span>
                                        </Link>
                                        <Link :href="route('student-bills.index')" class="flex items-center space-x-3 w-full px-10 py-3 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white transition-colors" :class="{'text-indigo-600 font-semibold bg-white': route().current('student-bills.*')}">
                                            <svg class="h-4 w-4" :class="route().current('student-bills.*') ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            <span>Tagihan Siswa</span>
                                        </Link>
                                        <Link :href="route('payments.index')" class="flex items-center space-x-3 w-full px-10 py-3 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white transition-colors" :class="{'text-indigo-600 font-semibold bg-white': route().current('payments.*')}">
                                            <svg class="h-4 w-4" :class="route().current('payments.*') ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                            <span>Transaksi Tagihan</span>
                                        </Link>
                                        <Link :href="route('salaries.index')" class="flex items-center space-x-3 w-full px-10 py-3 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white transition-colors" :class="{'text-indigo-600 font-semibold bg-white': route().current('salaries.*')}">
                                            <svg class="h-4 w-4" :class="route().current('salaries.*') ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                            <span>Penggajian</span>
                                        </Link>
                                        <Link :href="route('cash-transactions.index')" class="flex items-center space-x-3 w-full px-10 py-3 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white transition-colors" :class="{'text-indigo-600 font-semibold bg-white': route().current('cash-transactions.*')}">
                                            <svg class="h-4 w-4" :class="route().current('cash-transactions.*') ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                                            <span>Jurnal Kas</span>
                                        </Link>
                                    </div>
                                </transition>
                            </div>



                             <!-- Laporan Accordion -->
                             <div class="w-full border-b border-gray-100" v-if="$page.props.auth.user.roles.some(r => ['admin', 'bendahara'].includes(r))">
                                 <button @click="toggleMobileMenu('laporan')" class="w-full flex items-center justify-between px-6 py-4 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                     <div class="flex items-center space-x-3">
                                         <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                         <span>Laporan</span>
                                     </div>
                                     <svg class="h-4 w-4 text-gray-400 transition-transform duration-300 ease-in-out" :class="{'rotate-180': activeMobileMenu === 'laporan'}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                 </button>
                                 <transition
                                     enter-active-class="transition-[max-height,opacity] ease-in-out duration-300 overflow-hidden"
                                     enter-from-class="max-h-0 opacity-0"
                                     enter-to-class="max-h-96 opacity-100"
                                     leave-active-class="transition-[max-height,opacity] ease-in-out duration-300 overflow-hidden"
                                     leave-from-class="max-h-96 opacity-100"
                                     leave-to-class="max-h-0 opacity-0"
                                 >
                                     <div v-show="activeMobileMenu === 'laporan'" class="bg-gray-50/50 pb-2">
                                         <Link :href="route('reports.index')" class="flex items-center space-x-3 w-full px-10 py-3 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white transition-colors" :class="{'text-indigo-600 font-semibold bg-white': route().current('reports.index')}">
                                             <svg class="h-4 w-4" :class="route().current('reports.index') ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                             <span>Ringkasan Keuangan</span>
                                         </Link>
                                         <Link :href="route('reports.transactions')" class="flex items-center space-x-3 w-full px-10 py-3 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white transition-colors" :class="{'text-indigo-600 font-semibold bg-white': route().current('reports.transactions')}">
                                             <svg class="h-4 w-4" :class="route().current('reports.transactions') ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                             <span>Laporan Transaksi</span>
                                         </Link>
                                     </div>
                                 </transition>
                             </div>

                            <!-- Pengaturan Accordion -->
                            <div class="w-full border-b border-gray-100" v-if="$page.props.auth.user.roles.includes('admin')">
                                <button @click="toggleMobileMenu('pengaturan')" class="w-full flex items-center justify-between px-6 py-4 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        <span>Pengaturan</span>
                                    </div>
                                    <svg class="h-4 w-4 text-gray-400 transition-transform duration-300 ease-in-out" :class="{'rotate-180': activeMobileMenu === 'pengaturan'}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <transition
                                    enter-active-class="transition-[max-height,opacity] ease-in-out duration-300 overflow-hidden"
                                    enter-from-class="max-h-0 opacity-0"
                                    enter-to-class="max-h-96 opacity-100"
                                    leave-active-class="transition-[max-height,opacity] ease-in-out duration-300 overflow-hidden"
                                    leave-from-class="max-h-96 opacity-100"
                                    leave-to-class="max-h-0 opacity-0"
                                >
                                    <div v-show="activeMobileMenu === 'pengaturan'" class="bg-gray-50/50 pb-2">
                                        <Link :href="route('settings.index')" class="flex items-center space-x-3 w-full px-10 py-3 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white transition-colors" :class="{'text-indigo-600 font-semibold bg-white': route().current('settings.index')}">
                                            <svg class="h-4 w-4" :class="route().current('settings.index') ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /></svg>
                                            <span>Pengaturan Umum</span>
                                        </Link>
                                        <Link :href="route('users.index')" class="flex items-center space-x-3 w-full px-10 py-3 text-sm text-gray-600 hover:text-indigo-600 hover:bg-white transition-colors" :class="{'text-indigo-600 font-semibold bg-white': route().current('users.index')}">
                                            <svg class="h-4 w-4" :class="route().current('users.index') ? 'text-indigo-600' : 'text-gray-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                            <span>Kelola Akun</span>
                                        </Link>
                                    </div>
                                </transition>
                            </div>

                        </div>
                    </nav>
                </div>
            </div>
        </nav>

        <!-- Dynamic Header Slot -->
        <header v-if="$slots.header" class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Main Content -->
        <main class="py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 py-10 mt-auto">
            <div class="mx-auto max-w-7xl px-4 text-center">
                <p class="text-gray-500 text-sm font-medium tracking-wide">
                    {{ $page.props.settings?.copyright || '© 2026 Sistem Informasi Manajemen Keuangan. Dikembangkan secara profesional.' }}
                </p>
            </div>
        </footer>

        <!-- Global Notifications & Confirmations -->
        <Toast />
    </div>
</template>

<style scoped>
.mesh-aura {
    background-color: #003B73;
    background-image: 
        radial-gradient(at 0% 0%, hsla(197,100%,49%,0.15) 0px, transparent 50%),
        radial-gradient(at 100% 0%, hsla(282,88%,65%,0.15) 0px, transparent 50%),
        radial-gradient(at 100% 100%, hsla(204,100%,45%,0.1) 0px, transparent 50%),
        radial-gradient(at 0% 100%, hsla(242,100%,70%,0.1) 0px, transparent 50%);
    filter: blur(40px);
}

.noise-overlay {
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 250 250' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
    opacity: 0.04;
    mix-blend-mode: soft-light;
}

.glass-nav {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}
</style>
