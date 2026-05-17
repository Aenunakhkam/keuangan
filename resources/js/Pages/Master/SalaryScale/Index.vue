<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import CurrencyInput from '@/Components/CurrencyInput.vue';
import { confirmDelete } from '@/Utils/sweetalert';

const props = defineProps<{
    scales: any[];
}>();

const showingModal = ref(false);
const modalMode = ref('create');
const selectedScale = ref<any>(null);

const form = useForm({
    grade: 'IV',
    mkg: 0,
    amount: 0,
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    showingModal.value = true;
};

const openEditModal = (scale: any) => {
    modalMode.value = 'edit';
    selectedScale.value = scale;
    form.grade = scale.grade;
    form.mkg = scale.mkg;
    form.amount = scale.amount;
    showingModal.value = true;
};

const submit = () => {
    form.post(route('salary-scales.store'), {
        onSuccess: () => {
            showingModal.value = false;
            form.reset();
        },
    });
};

const deleteScale = async (scale: any) => {
    const result = await confirmDelete(
        'Hapus Aturan Gaji',
        `Hapus aturan gaji untuk Golongan ${scale.grade} MKG ${scale.mkg}?`
    );

    if (result.isConfirmed) {
        router.delete(route('salary-scales.destroy', scale.id));
    }
};

const grades = ['I', 'II', 'III', 'IV', 'V'];
</script>

<template>
    <Head title="Master Gaji Pokok" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Master Gaji Pokok</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Atur matriks gaji berdasarkan Golongan dan Masa Kerja (MKG).</p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="px-5 py-3 bg-[#003B73] text-white rounded-2xl text-sm font-black shadow-xl transform transition-transform hover:scale-[1.02] flex items-center space-x-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Aturan Gaji</span>
                </button>
            </div>
        </template>

        <div class="space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100">
                                <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-center w-20">No</th>
                                <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Golongan</th>
                                <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">MKG (Tahun)</th>
                                <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Rentang MKG</th>
                                <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Nominal Gaji Pokok</th>
                                <th class="px-6 py-4 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                            <tr v-for="(scale, index) in scales" :key="scale.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-center font-bold text-gray-400 text-sm">{{ index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-black">Golongan {{ scale.grade }}</span>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">{{ scale.mkg }} Thn</td>
                                <td class="px-6 py-4 text-sm text-gray-500 font-medium">
                                    {{ scale.mkg }} s.d {{ scale.mkg + 1 }} Tahun
                                </td>
                                <td class="px-6 py-4 font-black text-emerald-600">
                                    Rp {{ Number(scale.amount).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button @click="openEditModal(scale)" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteScale(scale)" class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="scales.length === 0">
                                <td colspan="6" class="px-6 py-20 text-center text-gray-500 font-bold italic">Belum ada data matriks gaji pokok.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Penjelasan Logika -->
            <div class="bg-indigo-50 p-8 rounded-3xl border border-indigo-100 border-dashed">
                <h4 class="text-sm font-black text-indigo-900 uppercase tracking-widest mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    Informasi Logika Gaji
                </h4>
                <ul class="text-sm text-indigo-700 space-y-2 font-medium">
                    <li>• Gaji Pokok ditentukan berdasarkan persilangan **Golongan** dan **Masa Kerja (MKG)**.</li>
                    <li>• Aturan 2 Tahunan: Nilai MKG 0 akan berlaku untuk masa kerja 0 s.d 1 tahun. Nilai MKG 2 berlaku untuk 2 s.d 3 tahun, dst.</li>
                    <li>• Golongan I: SLTP | II: SLTA | III: D1-D3 | IV: D4-S1 | V: S2.</li>
                </ul>
            </div>
        </div>

        <Modal :show="showingModal" @close="showingModal = false">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 mb-6 tracking-tight">
                    {{ modalMode === 'create' ? 'Tambah Aturan Gaji' : 'Edit Aturan Gaji' }}
                </h2>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-1.5">
                        <InputLabel for="grade" value="Golongan" />
                        <select 
                            id="grade" 
                            v-model="form.grade" 
                            class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"
                        >
                            <option v-for="g in grades" :key="g" :value="g">Golongan {{ g }}</option>
                        </select>
                        <InputError :message="form.errors.grade" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="mkg" value="Masa Kerja Golongan (Tahun)" />
                        <TextInput id="mkg" v-model="form.mkg" type="number" min="0" step="2" class="block w-full" placeholder="Contoh: 0, 2, 4..." />
                        <p class="text-[10px] text-gray-400 italic mt-1">* Gunakan angka genap untuk kenaikan 2 tahunan.</p>
                        <InputError :message="form.errors.mkg" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="amount" value="Nominal Gaji Pokok" />
                        <CurrencyInput id="amount" v-model="form.amount" class="block w-full" />
                        <InputError :message="form.errors.amount" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <SecondaryButton @click="showingModal = false">Batal</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Simpan Aturan</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
