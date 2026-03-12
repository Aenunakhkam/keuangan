<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header class="mb-8">
            <h2 class="text-xl font-black text-gray-900 dark:text-gray-100 tracking-tight">
                Keamanan Password
            </h2>

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 font-medium leading-relaxed">
                Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk menjaga keamanan data.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="space-y-6">
            <div class="space-y-1.5">
                <InputLabel for="current_password" value="Kata Sandi Saat Ini" class="font-bold text-gray-700 dark:text-gray-300" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200"
                    autocomplete="current-password"
                    placeholder="••••••••"
                />

                <InputError
                    :message="form.errors.current_password"
                    class="mt-2"
                />
            </div>

            <div class="space-y-1.5">
                <InputLabel for="password" value="Kata Sandi Baru" class="font-bold text-gray-700 dark:text-gray-300" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200"
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                />

                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div class="space-y-1.5">
                <InputLabel
                    for="password_confirmation"
                    value="Konfirmasi Kata Sandi Baru"
                    class="font-bold text-gray-700 dark:text-gray-300"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200"
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi baru"
                />

                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                />
            </div>

            <div class="flex items-center gap-4 pt-4">
                <PrimaryButton 
                    :disabled="form.processing"
                    class="px-8 py-3.5 rounded-2xl font-black shadow-xl shadow-indigo-500/20 transform transition-transform hover:scale-[1.02] active:scale-[0.98]"
                >
                    Perbarui Password
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
                        <span class="text-sm font-black">Berhasil</span>
                    </div>
                </Transition>
            </div>
        </form>
    </section>
</template>
