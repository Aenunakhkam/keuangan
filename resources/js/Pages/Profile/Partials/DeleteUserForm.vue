<script setup lang="ts">
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => {
            form.reset();
        },
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header class="mb-8">
            <h2 class="text-xl font-black text-rose-600 dark:text-rose-400 tracking-tight">
                Hapus Akun
            </h2>

            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 font-medium leading-relaxed">
                Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Sebelum menghapus akun, silakan unduh data atau informasi apa pun yang ingin Anda simpan.
            </p>
        </header>

        <DangerButton 
            @click="confirmUserDeletion"
            class="px-8 py-3.5 rounded-2xl font-black shadow-xl shadow-rose-500/20 transform transition-transform hover:scale-[1.02] active:scale-[0.98]"
        >
            Hapus Akun Permanen
        </DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-2">
                    Apakah Anda yakin ingin menghapus akun?
                </h2>

                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium leading-relaxed mb-6">
                    Setelah akun dihapus, semua data tidak dapat dikembalikan. Silakan masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun secara permanen.
                </p>

                <div class="space-y-2">
                    <InputLabel
                        for="password"
                        value="Kata Sandi Verifikasi"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all duration-200"
                        placeholder="Masukkan kata sandi Anda"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <SecondaryButton 
                        @click="closeModal"
                        class="px-6 py-3 rounded-2xl font-bold border-gray-200"
                    >
                        Batalkan
                    </SecondaryButton>

                    <DangerButton
                        class="px-6 py-3 rounded-2xl font-black shadow-lg shadow-rose-500/20"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Ya, Hapus Akun
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
