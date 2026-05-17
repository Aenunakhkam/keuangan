<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { debounce } from 'lodash';

const props = defineProps<{
    teachers: any;
    filters: any;
}>();

const search = ref(props.filters.search || '');
const perPage = ref(props.filters.per_page || 10);

watch(search, debounce((value) => {
    router.get(route('salary-calculations.index'), { search: value, per_page: perPage.value }, { preserveState: true, replace: true });
}, 300));

const updatePerPage = (value: string) => {
    router.get(route('salary-calculations.index'), { search: search.value, per_page: value }, { preserveState: true, replace: true });
};

const showingModal = ref(false);
const selectedTeacher = ref<any>(null);

const form = useForm({
    teaching_hours: 0,
    discipline_percentage: 0,
});

const openEditModal = (teacher: any) => {
    selectedTeacher.value = teacher;
    form.teaching_hours = teacher.teaching_hours;
    form.discipline_percentage = teacher.discipline_percentage;
    showingModal.value = true;
};

const submit = () => {
    form.put(route('salary-calculations.update', selectedTeacher.value.id), {
        onSuccess: () => {
            showingModal.value = false;
        },
    });
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};

const formatPercent = (value: number) => {
    return value.toLocaleString('id-ID', { minimumFractionDigits: 2 }) + '%';
};
</script>

<template>
    <Head title="Daftar Perhitungan Gaji" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Daftar Perhitungan Gaji</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mt-1">Kelola parameter gaji riil dan sanksi disiplin pegawai.</p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Search & Filters -->
                <div class="flex flex-col md:flex-row gap-4 justify-between items-center bg-white/50 dark:bg-gray-800/50 backdrop-blur-xl p-6 rounded-3xl border border-white dark:border-gray-700 shadow-xl">
                    <div class="relative w-full md:w-96">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Cari nama pegawai..." 
                            class="block w-full pl-11 pr-4 py-3 bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-700 rounded-2xl text-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200"
                        />
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Tampilkan</label>
                        <select 
                            v-model="perPage" 
                            @change="updatePerPage(($event.target as HTMLSelectElement).value)"
                            class="bg-white dark:bg-gray-900 border-gray-100 dark:border-gray-700 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8"
                        >
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-[#003B73] border-b border-indigo-800">
                                    <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-white uppercase tracking-widest text-center border-r border-indigo-800/30 w-12">No</th>
                                    <th rowspan="2" class="px-6 py-4 text-[10px] font-black text-white uppercase tracking-widest border-r border-indigo-800/30">Nama Pegawai</th>
                                    <th colspan="3" class="px-6 py-2 text-[10px] font-black text-white uppercase tracking-widest text-center border-b border-indigo-800/30">Gaji Pokok (Rp.)</th>
                                    <th rowspan="2" class="px-6 py-4 text-[10px] font-black text-white uppercase tracking-widest text-center border-r border-indigo-800/30">Sanksi Disiplin</th>
                                    <th rowspan="2" class="px-6 py-4 text-[10px] font-black text-white uppercase tracking-widest text-right border-r border-indigo-800/30">Gapok Setelah Absen</th>
                                    <th rowspan="2" class="px-6 py-4 text-[10px] font-black text-white uppercase tracking-widest text-center w-20">Aksi</th>
                                </tr>
                                <tr class="bg-[#003B73]">
                                    <th class="px-4 py-3 text-[9px] font-black text-indigo-200 uppercase tracking-widest text-center border-r border-indigo-800/30">Normatif</th>
                                    <th class="px-4 py-3 text-[9px] font-black text-indigo-200 uppercase tracking-widest text-center border-r border-indigo-800/30">Tugas Jampel</th>
                                    <th class="px-4 py-3 text-[9px] font-black text-indigo-200 uppercase tracking-widest text-center border-r border-indigo-800/30">Gaji Pokok Riil</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="(teacher, index) in teachers.data" :key="teacher.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                    <td class="px-4 py-4 text-center font-bold text-gray-900 dark:text-gray-300 border-r border-gray-50 dark:border-gray-700">
                                        {{ (teachers.current_page - 1) * teachers.per_page + index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 border-r border-gray-50 dark:border-gray-700">
                                        <div class="font-black text-gray-900 dark:text-white text-sm">{{ teacher.name }}</div>
                                        <div class="text-[9px] font-bold text-indigo-500 uppercase tracking-tighter">{{ teacher.education }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-gray-700 dark:text-gray-400 border-r border-gray-50 dark:border-gray-700 bg-gray-50/30">
                                        {{ formatCurrency(teacher.basic_salary_normative).replace('Rp', '').trim() }}
                                    </td>
                                    <td class="px-4 py-4 text-center font-black text-blue-600 dark:text-blue-400 border-r border-gray-50 dark:border-gray-700">
                                        {{ teacher.teaching_hours }}
                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-gray-900 dark:text-white border-r border-gray-50 dark:border-gray-700 bg-gray-50/30">
                                        {{ formatCurrency(teacher.basic_salary_real).replace('Rp', '').trim() }}
                                    </td>
                                    <td class="px-6 py-4 text-center border-r border-gray-50 dark:border-gray-700">
                                        <div class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black" 
                                            :class="teacher.discipline_percentage > 0 ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100'">
                                            {{ formatPercent(teacher.discipline_percentage) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-gray-900 dark:text-white border-r border-gray-50 dark:border-gray-700">
                                        {{ formatCurrency(teacher.after_absent) }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button @click="openEditModal(teacher)" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all shadow-sm hover:shadow-indigo-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="teachers.data.length === 0">
                                    <td colspan="8" class="px-6 py-20 text-center text-gray-500 font-bold">Belum ada data pegawai.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                                Menampilkan <span class="font-black text-gray-900 dark:text-white">{{ teachers.from || 0 }}</span> sampai <span class="font-black text-gray-900 dark:text-white">{{ teachers.to || 0 }}</span> dari <span class="font-black text-gray-900 dark:text-white">{{ teachers.total }}</span> pegawai
                            </div>
                            <div class="flex items-center space-x-2">
                                <template v-for="(link, k) in teachers.links" :key="k">
                                    <div v-if="link.url === null" class="px-4 py-2 text-sm text-gray-400 font-bold bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700" v-html="link.label" />
                                    <Link 
                                        v-else 
                                        :href="link.url" 
                                        class="px-4 py-2 text-sm font-black rounded-xl transition-all" 
                                        :class="link.active ? 'bg-[#003B73] text-white shadow-lg' : 'text-gray-600 hover:bg-white hover:shadow-md border border-transparent hover:border-gray-100'" 
                                        v-html="link.label"
                                    />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <Modal :show="showingModal" @close="showingModal = false">
            <div v-if="selectedTeacher" class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">
                    Update Parameter Gaji: {{ selectedTeacher.name }}
                </h2>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-1.5">
                        <InputLabel for="teaching_hours" value="Tugas Jampel (Jam Pelajaran)" />
                        <TextInput id="teaching_hours" v-model="form.teaching_hours" type="number" class="block w-full" required />
                        <p class="text-[10px] text-gray-500 italic mt-1">Standar jam pelajaran penuh adalah 32.</p>
                        <InputError :message="form.errors.teaching_hours" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="discipline_percentage" value="Sanksi Disiplin (%)" />
                        <div class="relative">
                            <TextInput id="discipline_percentage" v-model="form.discipline_percentage" type="number" step="0.01" class="block w-full pr-12" required />
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400 font-black">%</div>
                        </div>
                        <InputError :message="form.errors.discipline_percentage" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <SecondaryButton @click="showingModal = false">Batal</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Simpan Perubahan</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
