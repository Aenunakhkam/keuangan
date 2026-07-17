<script setup lang="ts">
import { formatRupiah } from '@/Utils/formatRupiah';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { debounce } from 'lodash';

const props = defineProps<{
    config: any;
    categories: any[];
    teachers: any;
    filters: any;
}>();

const search = ref(props.filters.search || '');
const perPage = ref(props.filters.per_page || 10);

watch(search, debounce((value) => {
    router.get(route('bpjs.index'), { search: value, per_page: perPage.value }, { preserveState: true, replace: true });
}, 300));

const updatePerPage = (value: string) => {
    router.get(route('bpjs.index'), { search: search.value, per_page: value }, { preserveState: true, replace: true });
};

// Config Form
const configForm = useForm({
    umk_reference: props.config.umk_reference,
    health_school_percent: props.config.health_school_percent,
    health_employee_percent: props.config.health_employee_percent,
    naker_school_percent: props.config.naker_school_percent,
    naker_employee_percent: props.config.naker_employee_percent,
});

const saveConfig = () => {
    configForm.put(route('bpjs.config.update'), {
        preserveScroll: true,
    });
};

const formatInputCurrency = (val: any) => {
    if (!val && val !== 0) return '';
    return formatRupiah(val);
};

const handleUmkInput = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const rawValue = target.value.replace(/\D/g, '');
    configForm.umk_reference = rawValue ? Number(rawValue) : 0;
};

// Teacher Category Form
const showingModal = ref(false);
const selectedTeacher = ref<any>(null);
const teacherForm = useForm({
    bpjs_category_id: null as number | null,
});

const openEditModal = (teacher: any) => {
    selectedTeacher.value = teacher;
    teacherForm.bpjs_category_id = teacher.bpjs_category_id;
    showingModal.value = true;
};

const submitTeacherUpdate = () => {
    teacherForm.put(route('bpjs.teacher.update', selectedTeacher.value.id), {
        onSuccess: () => {
            showingModal.value = false;
        },
    });
};

// Calculations Helper
const calculateBpjs = (category: any) => {
    if (!category || !category.code || category.code === 'E') return { allowance: 0, health: 0, naker: 0 };

    const umk = Number(configForm.umk_reference);
    const healthTotalPercent = Number(configForm.health_school_percent) + Number(configForm.health_employee_percent);
    const nakerTotalPercent = Number(configForm.naker_school_percent) + Number(configForm.naker_employee_percent);

    let allowancePercent = 0;
    if (category.code === 'A') {
        allowancePercent = Number(configForm.health_school_percent) + Number(configForm.naker_school_percent);
    } else if (category.code === 'B') {
        allowancePercent = healthTotalPercent + nakerTotalPercent;
    } else if (category.code === 'C') {
        allowancePercent = healthTotalPercent;
    } else if (category.code === 'D') {
        allowancePercent = nakerTotalPercent;
    }

    const allowance = Math.round(umk * allowancePercent / 100);
    const healthDeduction = category.has_health ? Math.round(umk * healthTotalPercent / 100) : 0;
    const nakerDeduction = category.has_naker ? Math.round(umk * nakerTotalPercent / 100) : 0;

    return {
        allowance,
        health: healthDeduction,
        naker: nakerDeduction
    };
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};
</script>

<template>
    <Head title="Pengelolaan BPJS" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight text-green-600">Pengelolaan BPJS Pegawai</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mt-1">Konfigurasi UMK, Persentase Iuran, dan Kategori Kepesertaan.</p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <!-- BPJS Master Config -->
                <div class="bg-white dark:bg-gray-800 rounded-[40px] shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden relative group">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-green-400 to-emerald-600"></div>
                    <div class="p-8 md:p-12">
                        <div class="flex items-center space-x-4 mb-10">
                            <div class="p-4 bg-green-50 dark:bg-green-900/30 rounded-2xl text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">INDEK TUNJANGAN DAN RINCIAN KEWAJIBAN IURAN BPJS</h3>
                                <p class="text-sm font-bold text-green-600">BERDASAR UMK YANG BERLAKU</p>
                            </div>
                        </div>

                        <form @submit.prevent="saveConfig" class="grid grid-cols-1 md:grid-cols-3 gap-10">
                            <div class="space-y-4">
                                <InputLabel for="umk_reference" value="Nilai UMK Acuan" class="text-green-700 font-black uppercase tracking-widest text-[10px]" />
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-gray-400 font-bold">Rp</div>
                                    <input 
                                        id="umk_reference" 
                                        :value="formatInputCurrency(configForm.umk_reference)"
                                        @input="handleUmkInput"
                                        type="text" 
                                        class="block w-full pl-12 pr-4 py-4 bg-gray-50 dark:bg-gray-900 border-transparent rounded-[20px] font-black text-lg focus:ring-4 focus:ring-green-500/10 focus:bg-white transition-all border-2 border-transparent focus:border-green-500" 
                                    />
                                </div>
                                <div class="text-sm font-black text-green-600 px-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Terbaca: {{ formatCurrency(Number(configForm.umk_reference)) }}
                                </div>
                            </div>

                            <div class="space-y-6 bg-blue-50/50 dark:bg-blue-900/10 p-6 rounded-[30px] border border-blue-100 dark:border-blue-900/30">
                                <div class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-4 flex items-center">
                                    <span class="w-4 h-4 bg-blue-600 rounded-full mr-2"></span> BPJS Kesehatan
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel value="Sekolah (4%)" class="text-[9px] mb-2" />
                                        <TextInput v-model="configForm.health_school_percent" type="number" step="0.01" class="text-center font-black" />
                                    </div>
                                    <div>
                                        <InputLabel value="Pegawai (1%)" class="text-[9px] mb-2" />
                                        <TextInput v-model="configForm.health_employee_percent" type="number" step="0.01" class="text-center font-black" />
                                    </div>
                                </div>
                                <div class="text-[10px] font-bold text-blue-400 text-center">Total Iuran: {{ Number(configForm.health_school_percent) + Number(configForm.health_employee_percent) }}% ({{ formatCurrency(Math.round(Number(configForm.umk_reference) * (Number(configForm.health_school_percent) + Number(configForm.health_employee_percent)) / 100)) }})</div>
                            </div>

                            <div class="space-y-6 bg-orange-50/50 dark:bg-orange-900/10 p-6 rounded-[30px] border border-orange-100 dark:border-orange-900/30">
                                <div class="text-[10px] font-black text-orange-600 uppercase tracking-widest mb-4 flex items-center">
                                    <span class="w-4 h-4 bg-orange-600 rounded-full mr-2"></span> BPJS Ketenagakerjaan
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel value="Sekolah (6.24%)" class="text-[9px] mb-2" />
                                        <TextInput v-model="configForm.naker_school_percent" type="number" step="0.01" class="text-center font-black" />
                                    </div>
                                    <div>
                                        <InputLabel value="Pegawai (3%)" class="text-[9px] mb-2" />
                                        <TextInput v-model="configForm.naker_employee_percent" type="number" step="0.01" class="text-center font-black" />
                                    </div>
                                </div>
                                <div class="text-[10px] font-bold text-orange-400 text-center">Total Iuran: {{ Number(configForm.naker_school_percent) + Number(configForm.naker_employee_percent) }}% ({{ formatCurrency(Math.round(Number(configForm.umk_reference) * (Number(configForm.naker_school_percent) + Number(configForm.naker_employee_percent)) / 100)) }})</div>
                            </div>

                            <div class="md:col-span-3 flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
                                <PrimaryButton :disabled="configForm.processing" class="bg-green-600 hover:bg-green-700 !rounded-2xl px-10 py-4 shadow-xl shadow-green-500/20">
                                    Update Konfigurasi & Rumus
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Matriks Kategori (Read Only Summary Cards) -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mt-8">
                    <div v-for="cat in categories" :key="cat.id" class="bg-white dark:bg-gray-800 p-6 rounded-[32px] border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 group overflow-hidden relative">
                        <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                            <span class="text-6xl font-black">{{ cat.code }}</span>
                        </div>
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Kategori</div>
                        <div class="text-2xl font-black text-gray-900 dark:text-white mb-4">{{ cat.code }}</div>
                        <p class="text-[10px] text-gray-500 leading-tight mb-6 min-h-[30px]">{{ cat.name }}</p>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="font-bold text-gray-400">Tunjangan</span>
                                <span class="font-black text-green-600">{{ formatCurrency(calculateBpjs(cat).allowance) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="font-bold text-gray-400">Pot. KES</span>
                                <span class="font-black text-rose-500">{{ formatCurrency(calculateBpjs(cat).health) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[10px]">
                                <span class="font-bold text-gray-400">Pot. NAKER</span>
                                <span class="font-black text-rose-500">{{ formatCurrency(calculateBpjs(cat).naker) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Matriks Kategori (Read Only Summary Table) -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mt-8 overflow-x-auto">
                    <table class="w-full border-collapse border border-black text-xs font-bold text-black text-center" style="font-family: Arial, sans-serif; min-width: 800px;">
                        <thead>
                            <tr>
                                <th colspan="6" class="p-2 border border-black text-sm uppercase">INDEK TUNJANGAN DAN RINCIAN KEWAJIBAN IURAN BPJS BERDASAR UMK YANG BERLAKU</th>
                            </tr>
                            <tr>
                                <th class="border-l border-r border-black p-1 bg-white"></th>
                                <th class="border-l border-r border-black p-1 bg-white"></th>
                                <th class="border-l border-r border-black p-1 bg-white"></th>
                                <th colspan="2" class="border border-black p-1 bg-white">{{ Number(configForm.health_school_percent) + Number(configForm.health_employee_percent) + Number(configForm.naker_school_percent) + Number(configForm.naker_employee_percent) }}%</th>
                                <th class="border-l border-r border-black p-1 bg-white"></th>
                            </tr>
                            <tr>
                                <th rowspan="2" class="p-2 border border-black align-middle bg-white w-12">No</th>
                                <th rowspan="2" class="p-2 border border-black align-middle bg-white w-32">UMK</th>
                                <th class="p-2 border border-black uppercase bg-white">TUNJANGAN BPJS<br>Guru</th>
                                <th colspan="2" class="p-2 border border-black uppercase bg-white">PERINCIAN</th>
                                <th class="p-2 border border-black uppercase bg-white">TUNJANGAN BPJS<br>Staf</th>
                            </tr>
                            <tr>
                                <th class="p-2 border border-black bg-[#00b050]">BPJS Kes & Naker</th>
                                <th class="p-2 border border-black bg-[#00b050]">BPJS Kes</th>
                                <th class="p-2 border border-black bg-[#00b050]">BPJS Naker</th>
                                <th class="p-2 border border-black bg-[#00b050]">BPJS Kes & Naker</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <!-- Kategori A -->
                            <tr>
                                <td class="border-l border-r border-black"></td>
                                <td class="border-l border-r border-black"></td>
                                <td class="p-1 border border-black bg-[#00b050]">{{ Number(configForm.health_school_percent) + Number(configForm.naker_school_percent) }}%</td>
                                <td class="p-1 border border-black bg-[#00b050]">{{ Number(configForm.health_school_percent) + Number(configForm.health_employee_percent) }}%</td>
                                <td class="p-1 border border-black bg-[#00b050]">{{ Number(configForm.naker_school_percent) + Number(configForm.naker_employee_percent) }}%</td>
                                <td class="p-1 border border-black bg-[#00b050]">{{ Number(configForm.health_school_percent) + Number(configForm.naker_school_percent) }}%</td>
                            </tr>
                            <tr>
                                <td class="p-2 border border-black align-middle text-sm font-normal">1</td>
                                <td class="p-2 border border-black align-middle text-sm font-normal">{{ formatCurrency(Number(configForm.umk_reference)).replace('Rp', '').trim() }}</td>
                                <td class="p-2 border border-black bg-white font-normal">{{ formatCurrency(calculateBpjs(categories.find(c => c.code === 'A')).allowance).replace('Rp', '').trim() }}</td>
                                <td class="p-2 border border-black bg-white font-normal">{{ formatCurrency(calculateBpjs(categories.find(c => c.code === 'A')).health).replace('Rp', '').trim() }}</td>
                                <td class="p-2 border border-black bg-white font-normal">{{ formatCurrency(calculateBpjs(categories.find(c => c.code === 'A')).naker).replace('Rp', '').trim() }}</td>
                                <td class="p-2 border border-black bg-white font-normal">{{ formatCurrency(calculateBpjs(categories.find(c => c.code === 'A')).allowance).replace('Rp', '').trim() }}</td>
                            </tr>

                            <!-- Kategori B -->
                            <tr>
                                <td class="border-l border-r border-black"></td>
                                <td class="border-l border-r border-black"></td>
                                <td class="p-1 border border-black bg-[#00b0f0]">BPJS Kes &Naker</td>
                                <td class="p-1 border border-black bg-[#00b0f0]">{{ Number(configForm.health_school_percent) + Number(configForm.health_employee_percent) }}%</td>
                                <td class="p-1 border border-black bg-[#00b0f0]">{{ Number(configForm.naker_school_percent) + Number(configForm.naker_employee_percent) }}%</td>
                                <td class="p-1 border border-black bg-[#00b0f0]">BPJS Kes & Naker</td>
                            </tr>
                            <tr>
                                <td class="border-l border-r border-black"></td>
                                <td class="border-l border-r border-black"></td>
                                <td class="p-1 border border-black bg-[#00b0f0]">{{ Number(configForm.health_school_percent) + Number(configForm.health_employee_percent) + Number(configForm.naker_school_percent) + Number(configForm.naker_employee_percent) }}%</td>
                                <td class="p-2 border border-black bg-white font-normal" rowspan="2">{{ formatCurrency(calculateBpjs(categories.find(c => c.code === 'B')).health).replace('Rp', '').trim() }}</td>
                                <td class="p-2 border border-black bg-white font-normal" rowspan="2">{{ formatCurrency(calculateBpjs(categories.find(c => c.code === 'B')).naker).replace('Rp', '').trim() }}</td>
                                <td class="p-1 border border-black bg-[#00b0f0]">{{ Number(configForm.health_school_percent) + Number(configForm.health_employee_percent) + Number(configForm.naker_school_percent) + Number(configForm.naker_employee_percent) }}%</td>
                            </tr>
                            <tr>
                                <td class="border-l border-r border-black"></td>
                                <td class="border-l border-r border-black"></td>
                                <td class="p-2 border border-black bg-white font-normal">{{ formatCurrency(calculateBpjs(categories.find(c => c.code === 'B')).allowance).replace('Rp', '').trim() }}</td>
                                <td class="p-2 border border-black bg-white font-normal">{{ formatCurrency(calculateBpjs(categories.find(c => c.code === 'B')).allowance).replace('Rp', '').trim() }}</td>
                            </tr>

                            <!-- Kategori C -->
                            <tr>
                                <td class="border-l border-r border-black"></td>
                                <td class="border-l border-r border-black"></td>
                                <td class="p-1 border border-black bg-[#ffff00]">BPJS Kes</td>
                                <td colspan="2" class="p-1 border border-black bg-[#ffff00]">BPJS Kes</td>
                                <td class="p-1 border border-black bg-[#ffff00]">BPJS Kes</td>
                            </tr>
                            <tr>
                                <td class="border-l border-r border-black"></td>
                                <td class="border-l border-r border-black"></td>
                                <td class="p-1 border border-black bg-[#ffff00]">{{ Number(configForm.health_school_percent) + Number(configForm.health_employee_percent) }}%</td>
                                <td class="p-1 border border-black bg-[#ffff00]">{{ Number(configForm.health_school_percent) + Number(configForm.health_employee_percent) }}%</td>
                                <td class="p-1 border border-black bg-[#ffff00]">0%</td>
                                <td class="p-1 border border-black bg-[#ffff00]">{{ Number(configForm.health_school_percent) + Number(configForm.health_employee_percent) }}%</td>
                            </tr>
                            <tr>
                                <td class="border-l border-r border-black"></td>
                                <td class="border-l border-r border-black"></td>
                                <td class="p-2 border border-black bg-white font-normal">{{ formatCurrency(calculateBpjs(categories.find(c => c.code === 'C')).allowance).replace('Rp', '').trim() }}</td>
                                <td class="p-2 border border-black bg-white font-normal">{{ formatCurrency(calculateBpjs(categories.find(c => c.code === 'C')).health).replace('Rp', '').trim() }}</td>
                                <td class="p-2 border border-black bg-white font-normal">-</td>
                                <td class="p-2 border border-black bg-white font-normal">{{ formatCurrency(calculateBpjs(categories.find(c => c.code === 'C')).allowance).replace('Rp', '').trim() }}</td>
                            </tr>

                            <!-- Kategori D -->
                            <tr>
                                <td class="border-l border-r border-black"></td>
                                <td class="border-l border-r border-black"></td>
                                <td class="p-1 border border-black bg-[#ffc000]">BPJS Naker</td>
                                <td class="p-1 border border-black bg-[#ffc000]"></td>
                                <td class="p-1 border border-black bg-[#ffc000]">BPJS Naker</td>
                                <td class="p-1 border border-black bg-[#ffc000]">BPJS Naker</td>
                            </tr>
                            <tr>
                                <td class="border-l border-r border-black"></td>
                                <td class="border-l border-r border-black"></td>
                                <td class="p-1 border border-black bg-[#ffc000]">{{ Number(configForm.naker_school_percent) + Number(configForm.naker_employee_percent) }}%</td>
                                <td class="p-1 border border-black bg-[#ffc000]">0%</td>
                                <td class="p-1 border border-black bg-[#ffc000]">{{ Number(configForm.naker_school_percent) + Number(configForm.naker_employee_percent) }}%</td>
                                <td class="p-1 border border-black bg-[#ffc000]">{{ Number(configForm.naker_school_percent) + Number(configForm.naker_employee_percent) }}%</td>
                            </tr>
                            <tr>
                                <td class="border-l border-r border-black"></td>
                                <td class="border-l border-r border-black"></td>
                                <td class="p-2 border border-black bg-white font-normal">{{ formatCurrency(calculateBpjs(categories.find(c => c.code === 'D')).allowance).replace('Rp', '').trim() }}</td>
                                <td class="p-2 border border-black bg-white font-normal">-</td>
                                <td class="p-2 border border-black bg-white font-normal">{{ formatCurrency(calculateBpjs(categories.find(c => c.code === 'D')).naker).replace('Rp', '').trim() }}</td>
                                <td class="p-2 border border-black bg-white font-normal">{{ formatCurrency(calculateBpjs(categories.find(c => c.code === 'D')).allowance).replace('Rp', '').trim() }}</td>
                            </tr>

                            <!-- Kategori E -->
                            <tr>
                                <td class="border-l border-r border-black border-b"></td>
                                <td class="border-l border-r border-black border-b"></td>
                                <td class="p-1 border border-black bg-white font-normal">Belum BPJS Kes&Naker</td>
                                <td class="p-1 border border-black bg-white font-normal"></td>
                                <td class="p-1 border border-black bg-white font-normal"></td>
                                <td class="p-1 border border-black bg-white font-normal">Blm BPJS Kes&Naker</td>
                            </tr>
                            <tr>
                                <td class="border-0"></td>
                                <td class="border-0"></td>
                                <td class="p-1 border border-black bg-white font-normal text-center">0%</td>
                                <td class="p-1 border border-black bg-white font-normal text-center">0%</td>
                                <td class="p-1 border border-black bg-white font-normal text-center">0%</td>
                                <td class="p-1 border border-black bg-white font-normal text-center">0%</td>
                            </tr>
                            <tr>
                                <td class="border-0"></td>
                                <td class="border-0"></td>
                                <td class="p-2 border border-black bg-white font-normal">-</td>
                                <td class="p-2 border border-black bg-white font-normal">-</td>
                                <td class="p-2 border border-black bg-white font-normal">-</td>
                                <td class="p-2 border border-black bg-white font-normal">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Teacher List & Assignment -->
                <div class="bg-white dark:bg-gray-800 rounded-[40px] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-8 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div class="flex items-center space-x-3">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white">Daftar Kepesertaan Pegawai</h3>
                            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-[10px] font-black text-gray-500 uppercase">{{ teachers.total }} Pegawai</span>
                        </div>
                        <div class="flex items-center space-x-4 w-full md:w-auto">
                            <div class="relative flex-grow">
                                <input v-model="search" type="text" placeholder="Cari nama pegawai..." class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-900 border-transparent rounded-2xl text-sm focus:ring-green-500" />
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </div>
                            </div>
                            <select v-model="perPage" @change="updatePerPage(($event.target as HTMLSelectElement).value)" class="bg-gray-50 dark:bg-gray-900 border-transparent rounded-2xl text-sm focus:ring-green-500 py-3">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/50">
                                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Pegawai</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status / Kategori</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Tunjangan BPJS</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Potongan KES</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Potongan NAKER</th>
                                    <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="teacher in teachers.data" :key="teacher.id" class="hover:bg-green-50/30 dark:hover:bg-green-900/10 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="font-black text-gray-900 dark:text-white">{{ teacher.name }}</div>
                                        <div class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ teacher.education }}</div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <div v-if="teacher.bpjs_category" class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black"
                                            :class="{
                                                'bg-green-50 text-green-600 border border-green-100': ['A', 'B'].includes(teacher.bpjs_category.code),
                                                'bg-blue-50 text-blue-600 border border-blue-100': teacher.bpjs_category.code === 'C',
                                                'bg-orange-50 text-orange-600 border border-orange-100': teacher.bpjs_category.code === 'D',
                                                'bg-gray-50 text-gray-400 border border-gray-100': teacher.bpjs_category.code === 'E',
                                            }">
                                            Kategori {{ teacher.bpjs_category.code }}
                                        </div>
                                        <div v-else class="text-[10px] font-bold text-gray-400 italic">Belum diset</div>
                                    </td>
                                    <td class="px-8 py-6 text-right font-black text-emerald-600">
                                        {{ formatCurrency(calculateBpjs(teacher.bpjs_category).allowance) }}
                                    </td>
                                    <td class="px-8 py-6 text-right font-black text-rose-500">
                                        {{ formatCurrency(calculateBpjs(teacher.bpjs_category).health) }}
                                    </td>
                                    <td class="px-8 py-6 text-right font-black text-orange-500">
                                        {{ formatCurrency(calculateBpjs(teacher.bpjs_category).naker) }}
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <button @click="openEditModal(teacher)" class="p-2.5 text-green-600 hover:bg-green-50 rounded-xl transition-all border border-transparent hover:border-green-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="px-8 py-8 bg-gray-50/50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex justify-center space-x-2">
                            <template v-for="(link, k) in teachers.links" :key="k">
                                <div v-if="link.url === null" class="px-4 py-2 text-sm text-gray-400" v-html="link.label" />
                                <Link v-else :href="link.url" class="px-4 py-2 text-sm font-black rounded-xl transition-all" :class="link.active ? 'bg-green-600 text-white shadow-lg' : 'text-gray-600 hover:bg-white border border-transparent hover:border-gray-100'" v-html="link.label" />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment Modal -->
        <Modal :show="showingModal" @close="showingModal = false">
            <div v-if="selectedTeacher" class="p-10">
                <div class="flex items-center space-x-4 mb-8">
                    <div class="h-14 w-14 bg-green-50 rounded-2xl flex items-center justify-center text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">Set Kategori BPJS</h2>
                        <p class="text-sm text-gray-500">Pilih skema kepesertaan untuk <strong>{{ selectedTeacher.name }}</strong></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 mb-10">
                    <button v-for="cat in categories" :key="cat.id" 
                        @click="teacherForm.bpjs_category_id = teacherForm.bpjs_category_id === cat.id ? null : cat.id"
                        class="p-5 rounded-3xl border-2 text-left transition-all duration-200 group"
                        :class="teacherForm.bpjs_category_id === cat.id ? 'border-green-500 bg-green-50 ring-4 ring-green-500/10' : 'border-gray-100 hover:border-green-200 bg-white'">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-black uppercase tracking-widest" :class="teacherForm.bpjs_category_id === cat.id ? 'text-green-600' : 'text-gray-400'">Kategori {{ cat.code }}</span>
                            <div v-if="teacherForm.bpjs_category_id === cat.id" class="h-6 w-6 bg-green-500 rounded-full flex items-center justify-center text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            </div>
                        </div>
                        <div class="text-sm font-black text-gray-900 group-hover:text-green-700 mb-1">{{ cat.name }}</div>
                        <div class="flex items-center space-x-4">
                            <span class="text-[10px] font-bold text-gray-400">Tunjangan: <span class="text-green-600">{{ formatCurrency(calculateBpjs(cat).allowance) }}</span></span>
                            <span class="text-[10px] font-bold text-gray-400">Potongan: <span class="text-rose-500">{{ formatCurrency(calculateBpjs(cat).health + calculateBpjs(cat).naker) }}</span></span>
                        </div>
                    </button>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                    <SecondaryButton @click="showingModal = false" class="!rounded-2xl px-8">Batal</SecondaryButton>
                    <PrimaryButton @click="submitTeacherUpdate" :disabled="teacherForm.processing" class="bg-green-600 hover:bg-green-700 !rounded-2xl px-12 py-4">
                        Simpan Perubahan
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

    </AuthenticatedLayout>
</template>
