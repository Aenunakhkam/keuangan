<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, inject } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { confirmDelete } from '@/Utils/sweetalert';

const props = defineProps<{
    settings: any;
}>();


const form = useForm({
    app_name:            props.settings.app_name || '',
    school_name:         props.settings.school_name || '',
    copyright:           props.settings.copyright || '',
    bank_name:           props.settings.bank_name || '',
    bank_account_number: props.settings.bank_account_number || '',
    bank_account_name:   props.settings.bank_account_name || '',
    teaching_rate_per_hour: props.settings.teaching_rate_per_hour || '',
    transport_allowance: props.settings.transport_allowance || '',
    logo: null as File | null,
});

const logoPreview = ref(props.settings.logo_url || null);

const handleLogoChange = (e: any) => {
    const file = e.target.files[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('settings.store'), {
        forceFormData: true,
        onSuccess: () => {
            // Success toast is handled via flash message in layout
        },
    });
};

const deleteLogo = async () => {
    const result = await confirmDelete(
        'Hapus Logo Sekolah',
        'Apakah Anda yakin ingin menghapus logo sekolah? Tampilan navbar dan login akan kembali ke default.'
    );

    if (result.isConfirmed) {
        router.delete(route('settings.logo.destroy'), {
            onSuccess: () => {
                logoPreview.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Pengaturan Umum" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Pengaturan Umum</h2>
            <p class="text-sm text-gray-500 font-medium mt-1">Sesuaikan identitas sekolah dan tampilan aplikasi.</p>
        </template>

        <div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="p-8">
                    <form @submit.prevent="submit" class="space-y-8">
                        <!-- Logo Sekolah -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white border-b border-gray-50 pb-2">Identitas Visual</h3>
                            <div class="flex items-center space-x-8">
                                <div class="relative group">
                                    <div class="w-32 h-32 rounded-3xl bg-gray-50 dark:bg-gray-900 border-2 border-dashed border-gray-200 dark:border-gray-700 flex items-center justify-center overflow-hidden">
                                        <img v-if="logoPreview" :src="logoPreview" class="w-full h-full object-contain" />
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <label for="logo" class="absolute inset-0 cursor-pointer bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-3xl">
                                        <span class="text-white text-xs font-black uppercase tracking-widest">Ubah Logo</span>
                                    </label>
                                    <input type="file" id="logo" class="hidden" @change="handleLogoChange" accept="image/*" />
                                </div>
                                <div class="flex-1 space-y-1">
                                    <p class="text-sm font-black text-gray-700 dark:text-gray-300">Logo Sekolah</p>
                                    <p class="text-xs text-gray-500">Format: JPG, PNG, atau SVG. Rekomendasi ukuran 512x512px.</p>
                                    <div v-if="logoPreview" class="mt-2">
                                        <button @click="deleteLogo" type="button" class="text-xs font-bold text-rose-600 hover:text-rose-700 underline underline-offset-4">Hapus Logo Sekarang</button>
                                    </div>
                                    <InputError :message="form.errors.logo" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Rekening Bank -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white border-b border-gray-50 pb-2">Informasi Pembayaran Transfer</h3>
                            <p class="text-xs text-gray-400">Data ini akan tampil di dashboard siswa sebagai panduan pembayaran via transfer.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <InputLabel for="bank_name" value="Nama Bank" />
                                    <TextInput id="bank_name" v-model="form.bank_name" type="text" class="block w-full" placeholder="Contoh: BRI, BNI, Mandiri" />
                                    <InputError :message="form.errors.bank_name" />
                                </div>
                                <div class="space-y-1.5">
                                    <InputLabel for="bank_account_number" value="Nomor Rekening" />
                                    <TextInput id="bank_account_number" v-model="form.bank_account_number" type="text" class="block w-full" placeholder="Contoh: 1234567890" />
                                    <InputError :message="form.errors.bank_account_number" />
                                </div>
                                <div class="space-y-1.5 md:col-span-2">
                                    <InputLabel for="bank_account_name" value="Atas Nama Rekening" />
                                    <TextInput id="bank_account_name" v-model="form.bank_account_name" type="text" class="block w-full" placeholder="Contoh: SMK Negeri 1 Contoh" />
                                    <InputError :message="form.errors.bank_account_name" />
                                </div>
                            </div>
                        </div>

                        <!-- Data Keuangan Sekolah -->
                        <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white border-b border-gray-50 pb-2">Pengaturan Gaji & Keuangan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5 md:col-span-1">
                                    <InputLabel for="teaching_rate_per_hour" value="Harga Honor Mengajar Per Jam (Rp)" />
                                    <TextInput id="teaching_rate_per_hour" v-model="form.teaching_rate_per_hour" type="number" step="1000" class="block w-full" placeholder="Contoh: 50000" />
                                    <p class="text-[10px] text-gray-500 font-medium">Nominal pengali jam mengajar (Jam × Harga).</p>
                                    <InputError :message="form.errors.teaching_rate_per_hour" />
                                </div>
                                <div class="space-y-1.5 md:col-span-1">
                                    <InputLabel for="transport_allowance" value="Uang Transportasi Global (Rp)" />
                                    <TextInput id="transport_allowance" v-model="form.transport_allowance" type="number" step="1000" class="block w-full" placeholder="Contoh: 200000" />
                                    <p class="text-[10px] text-gray-500 font-medium">Nominal tambahan Uang Transportasi untuk semua guru.</p>
                                    <InputError :message="form.errors.transport_allowance" />
                                </div>
                            </div>
                        </div>

                        <!-- Copyright -->

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <InputLabel for="app_name" value="Nama Aplikasi" />
                                <TextInput id="app_name" v-model="form.app_name" type="text" class="block w-full" placeholder="E-Keuangan" />
                                <InputError :message="form.errors.app_name" />
                            </div>
                            <div class="space-y-1.5">
                                <InputLabel for="school_name" value="Nama Sekolah" />
                                <TextInput id="school_name" v-model="form.school_name" type="text" class="block w-full" placeholder="SMK Negeri 1 Contoh" />
                                <InputError :message="form.errors.school_name" />
                            </div>
                            <div class="space-y-1.5 md:col-span-2">
                                <InputLabel for="copyright" value="Teks Copyright (Footer)" />
                                <TextInput id="copyright" v-model="form.copyright" type="text" class="block w-full" placeholder="© 2026 SMK Negeri 1 Contoh" />
                                <InputError :message="form.errors.copyright" />
                            </div>
                        </div>

                        <div class="flex justify-end pt-6 border-t border-gray-50">
                            <PrimaryButton :disabled="form.processing">Simpan Perubahan</PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
