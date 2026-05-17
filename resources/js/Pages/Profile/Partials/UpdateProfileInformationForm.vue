<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: Boolean;
    status?: String;
}>();

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email || '',
});
</script>

<template>
    <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Left Panel: Header -->
        <div class="md:col-span-1">
            <h2 class="text-lg font-black text-gray-900 dark:text-gray-100 tracking-tight">
                Informasi Profil
            </h2>

            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 font-medium leading-relaxed">
                Perbarui informasi nama profil dan alamat email terdaftar akun Anda.
            </p>
        </div>

        <!-- Right Panel: Form Fields inside modern card -->
        <div class="md:col-span-2">
            <form
                @submit.prevent="form.patch(route('profile.update'))"
                class="space-y-6 bg-gray-50/50 dark:bg-gray-900/10 p-6 sm:p-8 rounded-3xl border border-gray-100 dark:border-gray-700/60 shadow-sm"
            >
                <div class="space-y-1.5">
                    <InputLabel for="name" value="Nama Lengkap" class="font-bold text-gray-700 dark:text-gray-300" />

                    <TextInput
                        id="name"
                        type="text"
                        class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Masukkan nama lengkap"
                    />

                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <InputLabel for="registered_email" value="Email Terdaftar" class="font-bold text-gray-700 dark:text-gray-300" />

                        <input
                            id="registered_email"
                            type="email"
                            class="block w-full px-5 py-3.5 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-400 cursor-not-allowed shadow-sm focus:outline-none transition-all duration-200"
                            :value="user.email"
                            disabled
                        />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="email" value="Ganti Email Baru" class="font-bold text-gray-700 dark:text-gray-300" />

                        <TextInput
                            id="email"
                            type="email"
                            class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="nama@keuangan.test"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                </div>

                <div v-if="mustVerifyEmail && user.email_verified_at === null" class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-100 dark:border-amber-900/30">
                    <p class="text-sm text-amber-800 dark:text-amber-200 font-medium">
                        Alamat email Anda belum diverifikasi.
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="text-amber-600 font-bold underline hover:text-amber-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </Link>
                    </p>

                    <div
                        v-show="status === 'verification-link-sent'"
                        class="mt-2 text-sm font-bold text-green-600 dark:text-green-400"
                    >
                        Link verifikasi baru telah dikirim ke alamat email Anda.
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <PrimaryButton 
                        :disabled="form.processing"
                        class="px-8 py-3.5 rounded-2xl font-black shadow-xl shadow-indigo-500/20 transform transition-transform hover:scale-[1.02] active:scale-[0.98]"
                    >
                        Simpan Perubahan
                    </PrimaryButton>

                    <Transition
                        enter-active-class="transition ease-out duration-300"
                        enter-from-class="opacity-0 translate-x-2"
                        leave-active-class="transition ease-in duration-200"
                        leave-to-class="opacity-0"
                    >
                        <div
                            v-if="form.recentlySuccessful"
                            class="flex items-center space-x-2 text-emerald-600 dark:text-emerald-400 animate-in fade-in slide-in-from-left-4"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-black">Tersimpan</span>
                        </div>
                    </Transition>
                </div>
            </form>
        </div>
    </section>
</template>
