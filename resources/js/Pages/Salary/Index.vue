<script setup lang="ts">
import { formatRupiah } from '@/Utils/formatRupiah';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import CurrencyInput from '@/Components/CurrencyInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { confirmDelete } from '@/Utils/sweetalert';
import Swal from 'sweetalert2';
import { debounce } from 'lodash';

const props = defineProps<{
    salaries: any;
    teachers: any[];
    teachingRate: number;
    schoolLogo?: string | null;
    schoolName?: string;
    schoolAddress?: string;
    totalSubmittedAmount?: number;
    filters?: any;
}>();

const showingModal = ref(false);
const showingViewModal = ref(false);
const showingBulkModal = ref(false);
const selectedSalary = ref<any>(null);

const search = ref(props.filters?.search || '');
const month = ref(props.filters?.month || '');
const year = ref(props.filters?.year || '');
const perPage = ref(props.filters?.per_page || 10);

const filterPeriod = ref('');
if (props.filters?.month && props.filters?.year) {
    filterPeriod.value = `${props.filters.year}-${String(props.filters.month).padStart(2, '0')}`;
}

watch(filterPeriod, (val) => {
    if (val) {
        const [y, m] = val.split('-');
        month.value = Number(m);
        year.value = Number(y);
    } else {
        month.value = '';
        year.value = '';
    }
});

const formPeriod = ref('');
watch(formPeriod, (val) => {
    if (val) {
        const [y, m] = val.split('-');
        form.month = Number(m);
        form.year = Number(y);
    }
});

const bulkPeriod = ref('');
watch(bulkPeriod, (val) => {
    if (val) {
        const [y, m] = val.split('-');
        bulkForm.month = Number(m);
        bulkForm.year = Number(y);
    }
});

watch([search, month, year, perPage], debounce(() => {
    router.get(route('salaries.index'), {
        search: search.value,
        month: month.value,
        year: year.value,
        per_page: perPage.value,
    }, { preserveState: true, replace: true });
}, 300));

const printBulk = () => {
    const params = new URLSearchParams();
    if (search.value) params.append('search', search.value);
    if (month.value) params.append('month', month.value);
    if (year.value) params.append('year', year.value);
    
    window.open(route('salaries.print-bulk') + '?' + params.toString(), '_blank');
};

const form = useForm({
    teacher_id: '',
    base_salary: 0,
    allowance: 0,
    days_present: 0,
    deduction: 0,
    deduction_description: '',
    net_salary: 0,
    month: new Date().getMonth() + 1,
    year: new Date().getFullYear(),
    status: 'pending',
});

const bulkForm = useForm({
    month: new Date().getMonth() + 1,
    year: new Date().getFullYear(),
});

const calculateNet = () => {
    form.net_salary = Number(form.base_salary) + Number(form.allowance);
};

const updateSalaryFields = () => {
    const selectedTeacher = props.teachers.find(t => t.id === form.teacher_id);
    if (selectedTeacher) {
        form.base_salary = Number(selectedTeacher.basic_salary || 0);
        form.allowance = Number(selectedTeacher.allowance || 0);
        form.days_present = 0; // Default 0 as requested by user to be input manually
        calculateNet();
    } else {
        form.base_salary = 0;
        form.allowance = 0;
        calculateNet();
    }
};
const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    const today = new Date();
    const m = String(today.getMonth() + 1).padStart(2, '0');
    const y = today.getFullYear();
    formPeriod.value = `${y}-${m}`;
    form.month = today.getMonth() + 1;
    form.year = today.getFullYear();
    showingModal.value = true;
};

const openBulkModal = () => {
    bulkForm.reset();
    const today = new Date();
    const m = String(today.getMonth() + 1).padStart(2, '0');
    const y = today.getFullYear();
    bulkPeriod.value = `${y}-${m}`;
    bulkForm.month = today.getMonth() + 1;
    bulkForm.year = today.getFullYear();
    showingBulkModal.value = true;
};

const openViewModal = (salary: any) => {
    selectedSalary.value = salary;
    showingViewModal.value = true;
};

const terbilang = (nilai: number): string => {
    const bil = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
    if (nilai < 12) return bil[nilai];
    if (nilai < 20) return bil[nilai - 10] + " Belas";
    if (nilai < 100) return bil[Math.floor(nilai / 10)] + " Puluh " + bil[nilai % 10];
    if (nilai < 200) return "Seratus " + terbilang(nilai - 100);
    if (nilai < 1000) return bil[Math.floor(nilai / 100)] + " Ratus " + terbilang(nilai % 100);
    if (nilai < 2000) return "Seribu " + terbilang(nilai - 1000);
    if (nilai < 1000000) return terbilang(Math.floor(nilai / 1000)) + " Ribu " + terbilang(nilai % 1000);
    if (nilai < 1000000000) return terbilang(Math.floor(nilai / 1000000)) + " Juta " + terbilang(nilai % 1000000);
    return "";
};

const getTerbilang = (num: number): string => {
    if (!num || num <= 0) return "Nol Rupiah";
    const result = terbilang(Math.floor(num)).replace(/\s+/g, ' ').trim();
    return result + " Rupiah";
};


const getLogoUrl = (logo: string | null | undefined) => {
    if (!logo) return '';
    let clean = logo.replace(/^\/?storage\/?/, '');
    clean = clean.replace(/^\//, '');
    return '/storage/' + clean;
};

const submit = () => {
    form.post(route('salaries.store'), {
        onSuccess: () => {
            showingModal.value = false;
            form.reset();
        },
    });
};

const submitBulk = () => {
    bulkForm.post(route('salaries.generate-bulk'), {
        onSuccess: () => {
            showingBulkModal.value = false;
            bulkForm.reset();
        },
    });
};

const processPayment = async (id: number) => {
    const result = await Swal.fire({
        title: 'Konfirmasi Pembayaran',
        text: 'Tandai gaji ini sebagai sudah dibayarkan?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Bayarkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#003B73',
        cancelButtonColor: '#6b7280',
    });
    if (result.isConfirmed) {
        router.post(route('salaries.process-payment', id));
    }
};

const deleteSalary = async (id: number) => {
    const result = await confirmDelete('Hapus Data Gaji', 'Hapus data slip gaji ini?');
    if (result.isConfirmed) {
        router.delete(route('salaries.destroy', id));
    }
};

const printSalary = (id: number) => {
    window.open(route('salaries.print', id), '_blank');
};

const selectedIds = ref<number[]>([]);

const toggleAll = (event: Event) => {
    const checked = (event.target as HTMLInputElement).checked;
    if (checked) {
        selectedIds.value = props.salaries.data.map((sal: any) => sal.id);
    } else {
        selectedIds.value = [];
    }
};

const isAllSelected = computed(() => {
    if (props.salaries.data.length === 0) return false;
    return props.salaries.data.every((sal: any) => selectedIds.value.includes(sal.id));
});

const pendingSelectedIds = computed(() => {
    return props.salaries.data
        .filter((sal: any) => selectedIds.value.includes(sal.id) && sal.status === 'pending')
        .map((sal: any) => sal.id);
});

const processBulkPayment = async () => {
    if (pendingSelectedIds.value.length === 0) return;
    const result = await Swal.fire({
        title: 'Bayar Massal',
        text: `Bayarkan ${pendingSelectedIds.value.length} slip gaji yang terpilih secara massal?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Bayar Massal',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#003B73',
        cancelButtonColor: '#6b7280',
    });
    if (result.isConfirmed) {
        router.post(route('salaries.bulk-pay'), {
            ids: pendingSelectedIds.value
        }, {
            onSuccess: () => {
                selectedIds.value = [];
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Gaji massal telah berhasil dibayarkan.',
                    icon: 'success',
                    confirmButtonColor: '#003B73',
                });
            }
        });
    }
};

const processBulkDelete = async () => {
    if (selectedIds.value.length === 0) return;
    const result = await confirmDelete(
        'Hapus Massal',
        `Hapus ${selectedIds.value.length} data slip gaji yang terpilih secara massal?`
    );
    if (result.isConfirmed) {
        router.post(route('salaries.bulk-delete'), {
            ids: selectedIds.value
        }, {
            onSuccess: () => {
                selectedIds.value = [];
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data gaji terpilih telah berhasil dihapus secara massal.',
                    icon: 'success',
                    confirmButtonColor: '#003B73',
                });
            }
        });
    }
};

const submittableSelectedIds = computed(() => {
    return props.salaries.data
        .filter((sal: any) => selectedIds.value.includes(sal.id) && ['pending', 'rejected'].includes(sal.approval_status))
        .map((sal: any) => sal.id);
});

const publishableSelectedIds = computed(() => {
    return props.salaries.data
        .filter((sal: any) => selectedIds.value.includes(sal.id) && sal.approval_status === 'approved' && !sal.is_published)
        .map((sal: any) => sal.id);
});

const processSubmit = async () => {
    if (submittableSelectedIds.value.length === 0) return;
    const result = await Swal.fire({
        title: 'Ajukan ke Bendahara',
        text: `Ajukan ${submittableSelectedIds.value.length} slip gaji yang terpilih?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Ajukan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#003B73',
    });
    if (result.isConfirmed) {
        router.post(route('salaries.submit'), { ids: submittableSelectedIds.value }, {
            onSuccess: () => {
                selectedIds.value = [];
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Gaji telah diajukan ke Bendahara.',
                    icon: 'success',
                    confirmButtonColor: '#003B73',
                });
            }
        });
    }
};

const processPublish = async () => {
    if (publishableSelectedIds.value.length === 0) return;
    const result = await Swal.fire({
        title: 'Kirim Slip ke Pegawai',
        text: `Kirim ${publishableSelectedIds.value.length} slip gaji yang terpilih ke masing-masing pegawai?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kirim',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#10b981',
    });
    if (result.isConfirmed) {
        router.post(route('salaries.publish'), { ids: publishableSelectedIds.value }, {
            onSuccess: () => {
                selectedIds.value = [];
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Slip gaji telah dikirim/publish ke pegawai.',
                    icon: 'success',
                    confirmButtonColor: '#10b981',
                });
            }
        });
    }
};
</script>

<template>
    <Head title="Penggajian" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Manajemen Penggajian</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Kelola data slip gaji guru dan staf.</p>
                    <div v-if="totalSubmittedAmount" class="mt-2.5 inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50/80 rounded-xl border border-amber-200/60 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-amber-600 font-bold text-xs">Total Nominal Menunggu Bendahara:</span>
                        <span class="text-amber-700 font-black text-sm">{{ formatRupiah(totalSubmittedAmount || 0) }}</span>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button 
                        @click="openBulkModal"
                        class="px-5 py-3 bg-white border border-gray-200 rounded-2xl text-sm font-black shadow-sm transition-all hover:bg-gray-50 flex items-center space-x-2"
                    >
                        <span>Generate Bulk</span>
                    </button>
                    <button 
                        @click="openCreateModal"
                        class="px-5 py-3 bg-[#003B73] text-white rounded-2xl text-sm font-black shadow-xl shadow-blue-500/20 transform transition-transform hover:scale-[1.02] flex items-center space-x-2"
                    >
                        <span>Tambah Slip</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <!-- Bar Pencarian & Filter Periode -->
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <!-- Search Input -->
                    <div class="relative flex-1 max-w-xs">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input 
                            v-model="search" 
                            type="text" 
                            placeholder="Cari nama pegawai..." 
                            class="block w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"
                        />
                    </div>

                    <!-- Kalender Periode (Month Picker) -->
                    <div class="relative max-w-xs">
                        <input 
                            v-model="filterPeriod" 
                            type="month" 
                            class="block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"
                        />
                    </div>

                    <!-- Show Limit Select (Exactly like the uploaded image) -->
                    <div class="flex items-center space-x-2 text-xs font-bold text-gray-700 dark:text-gray-300">
                        <span>Lihat:</span>
                        <div class="relative">
                            <select 
                                v-model="perPage" 
                                class="appearance-none block pl-3 pr-8 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700/50"
                                style="min-width: 70px;"
                            >
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="all">Semua</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-2.5 pointer-events-none text-gray-500">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0 md:ml-auto w-full md:w-auto justify-end">
                    <button 
                        v-if="submittableSelectedIds.length > 0"
                        @click="processSubmit"
                        class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center justify-center space-x-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                        <span>Ajukan ({{ submittableSelectedIds.length }})</span>
                    </button>

                    <button 
                        v-if="publishableSelectedIds.length > 0"
                        @click="processPublish"
                        class="px-4 py-2.5 bg-teal-600 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-teal-500/20 hover:bg-teal-700 transition-all flex items-center justify-center space-x-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11.25l1.5 1.5.75-.75V8.758l2.276-.61a3 3 0 10-3.675-3.675l-.61 2.277H12l-.75.75 1.5 1.5M7.151 7.15a3 3 0 00-4.029 3.758A3 3 0 003 14.25v2.25H1.5l-.75.75 1.5 1.5.75-.75V19.5h2.25l.75-.75-1.5-1.5h1.5l.75-.75v-1.5c0-.828.672-1.5 1.5-1.5.828 0 1.5.672 1.5 1.5v2.25l-.75.75 1.5 1.5.75-.75v-1.5l1.5-1.5-.75-.75h-1.5v-2.25a3 3 0 00-3-3.75z" />
                        </svg>
                        <span>Kirim Slip ({{ publishableSelectedIds.length }})</span>
                    </button>
                    <button 
                        v-if="pendingSelectedIds.length > 0"
                        @click="processBulkPayment"
                        class="w-full md:w-auto px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-emerald-500/20 hover:bg-emerald-700 transition-all flex items-center justify-center space-x-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Bayar Terpilih ({{ pendingSelectedIds.length }})</span>
                    </button>

                    <button 
                        v-if="selectedIds.length > 0"
                        @click="processBulkDelete"
                        class="w-full md:w-auto px-5 py-2.5 bg-rose-600 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-rose-500/20 hover:bg-rose-700 transition-all flex items-center justify-center space-x-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.24 9m4.768 0L12 19M4.5 12h15m0 0H8.25m11.25 0v11.25A2.25 2.25 0 0 1 17.25 25H6.75A2.25 2.25 0 0 1 4.5 22.75V12h15Z" />
                        </svg>
                        <span>Hapus Terpilih ({{ selectedIds.length }})</span>
                    </button>

                    <button 
                        @click="printBulk"
                        class="w-full md:w-auto px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition-all flex items-center justify-center space-x-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Slip Massal</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#003B73] border-b border-indigo-800">
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center w-16">
                                <input 
                                    type="checkbox" 
                                    :checked="isAllSelected" 
                                    @change="toggleAll" 
                                    class="rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" 
                                    :disabled="salaries.data.length === 0"
                                />
                            </th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center w-12">No</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Guru</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center">Periode</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center">Tgl Gajian</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Gaji Bersih</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center">Status Keseluruhan</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        <tr v-for="(sal, index) in salaries.data" :key="sal.id" class="hover:bg-gray-50 transition-all duration-200" :class="{'bg-indigo-50/30 dark:bg-indigo-900/10': selectedIds.includes(sal.id)}">
                            <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-gray-300 w-16">
                                <input 
                                    type="checkbox" 
                                    :value="sal.id" 
                                    v-model="selectedIds" 
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" 
                                />
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-gray-300 w-12">
                                {{ (Number(salaries.current_page) - 1) * Number(salaries.per_page) + Number(index) + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ sal.teacher.name }}</div>
                                <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest">
                                    {{ [sal.teacher.nipty, sal.teacher.nipy].filter(Boolean).join(' / ') || 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-gray-500">{{ sal.month }}/{{ sal.year }}</td>
                            <td class="px-6 py-4 text-center font-bold text-gray-500 text-xs">
                                {{ sal.payment_date ? new Date(sal.payment_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-black text-indigo-600">{{ formatRupiah(sal.final_net_salary || 0) }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center gap-1.5">
                                    <span v-if="sal.status === 'paid'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-200">
                                        Lunas Terbayar
                                    </span>
                                    <span v-else-if="sal.approval_status === 'approved'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-200">
                                        Disetujui (Belum Dibayar)
                                    </span>
                                    <span v-else-if="sal.approval_status === 'rejected'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-rose-50 text-rose-700 border border-rose-200">
                                        Ditolak Bendahara
                                    </span>
                                    <span v-else-if="sal.approval_status === 'submitted'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-50 text-amber-600 border border-amber-200">
                                        Menunggu Persetujuan
                                    </span>
                                    <span v-else class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-600 border border-gray-200">
                                        Draf Admin
                                    </span>

                                    <span v-if="sal.is_published" class="px-2 py-0.5 bg-teal-50 text-teal-600 rounded text-[9px] font-bold uppercase tracking-widest border border-teal-200 shadow-sm mt-0.5">Terpublikasi</span>
                                    
                                    <div v-if="sal.approval_status === 'rejected' && sal.rejection_note" class="text-[9px] text-rose-600 font-bold bg-rose-50/50 px-2 py-1.5 rounded-lg w-full max-w-[140px] border border-rose-100/80 mt-1 text-left whitespace-normal leading-tight">
                                        Catatan: {{ sal.rejection_note }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <button v-if="sal.approval_status === 'approved'" @click="printSalary(sal.id)" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-xl text-[10px] font-black uppercase transition-all inline-flex items-center space-x-1" title="Cetak Slip">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    <span>Cetak</span>
                                </button>
                                <button v-if="sal.approval_status === 'pending' || sal.approval_status === 'rejected'" @click="openViewModal(sal)" class="px-3 py-1.5 bg-sky-50 text-sky-600 hover:bg-sky-100 rounded-xl text-[10px] font-black uppercase transition-all inline-flex items-center space-x-1" title="Pratinjau Slip">
                                    <span>Pratinjau</span>
                                </button>
                                <button v-if="sal.status === 'pending' && sal.approval_status === 'approved'" @click="processPayment(sal.id)" class="px-3 py-1.5 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase shadow-lg shadow-emerald-500/20 hover:scale-105 transition-all">
                                    Bayar
                                </button>
                                <button @click="deleteSalary(sal.id)" class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition-all inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div v-if="salaries.links && salaries.links.length > 3" class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <div class="text-sm text-gray-500 font-medium">
                    Menampilkan <span class="font-black text-gray-900">{{ salaries.from || 0 }}</span> s.d <span class="font-black text-gray-900">{{ salaries.to || 0 }}</span> dari <span class="font-black text-gray-900">{{ salaries.total }}</span> slip gaji
                </div>
                <div class="flex items-center space-x-1">
                    <template v-for="(link, k) in salaries.links" :key="k">
                        <div v-if="link.url === null" class="px-4 py-2 text-sm text-gray-400 border border-transparent" v-html="link.label"></div>
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

        <!-- Add Salary Modal -->
        <Modal :show="showingModal" @close="showingModal = false">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">
                    Buat Slip Gaji
                </h2>
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-1.5">
                        <InputLabel value="Pilih Guru" />
                        <select v-model="form.teacher_id" @change="updateSalaryFields" class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 bg-gray-50 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                            <option value="" disabled>-- Pilih Guru --</option>
                            <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5 md:col-span-2">
                            <p class="text-[10px] text-gray-500 bg-indigo-50 px-3 py-2 rounded-xl text-indigo-700 font-medium">Nilai Gaji Pokok & Tunjangan akan terisi otomatis berdasarkan data Gaji Pokok profil & Jabatan Struktural guru.</p>
                        </div>
                        <div class="space-y-1.5 md:col-span-1">
                            <InputLabel value="Gaji Pokok (Otomatis)" />
                            <TextInput v-model="form.base_salary" type="number" @input="calculateNet" class="w-full bg-gray-100" readonly />
                        </div>
                        <div class="space-y-1.5 md:col-span-1">
                            <InputLabel value="Tunjangan (Otomatis)" />
                            <TextInput v-model="form.allowance" type="number" @input="calculateNet" class="w-full bg-gray-100" readonly />
                        </div>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-2xl">
                        <div class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nilai Netto (Sebelum Potongan)</div>
                        <div class="text-3xl font-black text-indigo-600">{{ formatRupiah(form.net_salary) }}</div>
                    </div>
                    <div class="space-y-1.5">
                        <InputLabel value="Periode Gaji (Bulan & Tahun)" />
                        <input 
                            v-model="formPeriod" 
                            type="month" 
                            class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 bg-gray-50 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold"
                        />
                    </div>
                    <div class="flex justify-end space-x-3">
                        <SecondaryButton @click="showingModal = false">Batal</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">Simpan Slip</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Bulk Modal -->
        <Modal :show="showingBulkModal" @close="showingBulkModal = false">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">Generate Gaji Massal</h2>
                <form @submit.prevent="submitBulk" class="space-y-6">
                    <div class="space-y-1.5">
                        <InputLabel value="Pilih Periode Gaji Massal (Bulan & Tahun)" />
                        <input 
                            v-model="bulkPeriod" 
                            type="month" 
                            class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 bg-gray-50 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold"
                        />
                    </div>
                    <div class="flex justify-end space-x-3">
                        <SecondaryButton @click="showingBulkModal = false">Batal</SecondaryButton>
                        <PrimaryButton :disabled="bulkForm.processing">Generate Sekarang</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- View Slip Gaji Modal -->
        <Modal :show="showingViewModal" @close="showingViewModal = false" max-width="4xl">
            <div class="p-8 max-h-[90vh] overflow-y-auto bg-gray-50/30 dark:bg-gray-900/10">
                <!-- Kop Surat Slip Gaji & Actions -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-200 dark:border-gray-800 pb-6 mb-6 gap-4">
                    <div class="flex items-center space-x-4">
                        <div v-if="schoolLogo" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-gray-150 p-1">
                            <img :src="getLogoUrl(schoolLogo)" alt="Logo" class="w-full h-full object-contain rounded-xl">
                        </div>
                        <div v-else class="p-3 bg-indigo-50 dark:bg-indigo-950/50 rounded-2xl border border-indigo-100 dark:border-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight uppercase">{{ schoolName || 'SMK NU 1 ISLAMIYAH KRAMAT' }}</h2>
                            <p class="text-xs text-gray-500 font-medium mt-0.5">{{ schoolAddress || 'Jl. Garuda No. 39 - Kemantran' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3 w-full md:w-auto justify-end">
                        <button 
                            @click="printSalary(selectedSalary?.id)" 
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-lg shadow-indigo-500/20 inline-flex items-center space-x-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Cetak PDF</span>
                        </button>
                        <button 
                            @click="showingViewModal = false" 
                            class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-black uppercase tracking-wider transition-all"
                        >
                            Tutup
                        </button>
                    </div>
                </div>

                <div v-if="selectedSalary" class="space-y-6">
                    <!-- Judul Slip Gaji -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm gap-2">
                        <div>
                            <span class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest block">Dokumen Resmi</span>
                            <h3 class="text-base font-black text-gray-900 dark:text-white uppercase">SLIP GAJI BULANAN PEGAWAI</h3>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 bg-indigo-50 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 text-xs font-black rounded-lg">
                                PERIODE {{ selectedSalary.month }}/{{ selectedSalary.year }}
                            </span>
                            <span 
                                class="px-3 py-1 text-xs font-black rounded-lg uppercase tracking-wider"
                                :class="selectedSalary.status === 'paid' ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400' : 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400'"
                            >
                                {{ selectedSalary.status === 'paid' ? 'Lunas / Paid' : 'Menunggu / Pending' }}
                            </span>
                        </div>
                    </div>

                    <!-- Info Pegawai Grid -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-900/30 border-b border-gray-100 dark:border-gray-700">
                            <h4 class="text-xs font-black text-gray-500 uppercase tracking-widest">INFORMASI PENERIMA</h4>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Nama Lengkap</span>
                                <span class="text-sm font-black text-gray-900 dark:text-white">{{ selectedSalary.teacher?.name }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">NIP / NIPY</span>
                                <span class="text-sm font-black text-gray-900 dark:text-white font-mono">
                                    {{ [selectedSalary.teacher?.nipty, selectedSalary.teacher?.nipy].filter(Boolean).join(' / ') || '-' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Jabatan</span>
                                <span class="text-sm font-black text-gray-900 dark:text-white">
                                    {{ selectedSalary.teacher?.positions && selectedSalary.teacher.positions.length > 0 ? selectedSalary.teacher.positions.map((p: any) => p.name).join(', ') : 'Guru Pengajar' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Rincian Slip Gaji Grid (Widescreen layout) -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        
                        <!-- PENDAPATAN (5 Kolom) -->
                        <div class="lg:col-span-5 flex flex-col bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 bg-indigo-50/50 dark:bg-indigo-950/20 border-b border-indigo-100/30 dark:border-indigo-900/20">
                                <h4 class="text-xs font-black text-indigo-700 dark:text-indigo-400 uppercase tracking-widest">A. PENDAPATAN</h4>
                            </div>
                            <div class="p-6 flex-1 flex flex-col justify-between">
                                <table class="w-full text-sm">
                                    <tbody>
                                        <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                            <td class="py-3 font-bold text-gray-600 dark:text-gray-400">Gaji Pokok & Tunjangan (Netto SPJ)</td>
                                            <td class="py-3 text-right font-black text-gray-900 dark:text-white font-mono">
                                                {{ formatRupiah(selectedSalary.salary_deduction ? selectedSalary.salary_deduction.spj_netto : selectedSalary.net_salary) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                <div class="bg-indigo-50/30 dark:bg-indigo-950/10 p-4 rounded-xl border border-indigo-100/30 dark:border-indigo-900/20 flex justify-between items-center mt-8">
                                    <span class="text-xs font-black text-indigo-700 dark:text-indigo-400 uppercase tracking-wider">TOTAL PENDAPATAN</span>
                                    <span class="text-base font-black text-indigo-800 dark:text-indigo-300 font-mono">
                                        {{ formatRupiah(selectedSalary.salary_deduction ? selectedSalary.salary_deduction.spj_netto : selectedSalary.net_salary) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- POTONGAN (7 Kolom) -->
                        <div class="lg:col-span-7 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 bg-rose-50/50 dark:bg-rose-950/20 border-b border-rose-100/30 dark:border-rose-900/20">
                                <h4 class="text-xs font-black text-rose-700 dark:text-rose-400 uppercase tracking-widest">B. POTONGAN KOPERASI & SOSIAL</h4>
                            </div>
                            <div class="p-6">
                                <div v-if="selectedSalary.salary_deduction" class="space-y-1">
                                    <table class="w-full text-xs">
                                        <tbody>
                                            <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all">
                                                <td class="py-2.5 font-bold text-gray-600 dark:text-gray-400">1. TABUNGAN KHUSUS / SHR</td>
                                                <td class="py-2.5 text-right font-black text-gray-900 dark:text-white font-mono">{{ formatRupiah(selectedSalary.salary_deduction.tab_khusus) }}</td>
                                            </tr>
                                            <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all">
                                                <td class="py-2.5 font-bold text-gray-600 dark:text-gray-400">2. SIMPANAN WAJIB</td>
                                                <td class="py-2.5 text-right font-black text-gray-900 dark:text-white font-mono">{{ formatRupiah(selectedSalary.salary_deduction.simpanan_wajib) }}</td>
                                            </tr>
                                            <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all">
                                                <td class="py-2.5 font-bold text-gray-600 dark:text-gray-400">3. SIMPANAN MANASUKA</td>
                                                <td class="py-2.5 text-right font-black text-gray-900 dark:text-white font-mono">{{ formatRupiah(selectedSalary.salary_deduction.simpanan_sukarela) }}</td>
                                            </tr>
                                            <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all">
                                                <td class="py-2.5 font-bold text-gray-600 dark:text-gray-400">4. ANGSURAN PINJAMAN KOPERASI</td>
                                                <td class="py-2.5 text-right font-black text-gray-900 dark:text-white font-mono">{{ formatRupiah(selectedSalary.salary_deduction.angsuran_koperasi) }}</td>
                                            </tr>
                                            <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all">
                                                <td class="py-2.5 font-bold text-gray-600 dark:text-gray-400">5. DPLK BPD SLAWI</td>
                                                <td class="py-2.5 text-right font-black text-gray-900 dark:text-white font-mono">{{ formatRupiah(selectedSalary.salary_deduction.dplk_slawi) }}</td>
                                            </tr>
                                            <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all">
                                                <td class="py-2.5 font-bold text-gray-600 dark:text-gray-400">6. DPLK BPD KEMANTRAN</td>
                                                <td class="py-2.5 text-right font-black text-gray-900 dark:text-white font-mono">{{ formatRupiah(selectedSalary.salary_deduction.dplk_kemantran) }}</td>
                                            </tr>
                                            <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all">
                                                <td class="py-2.5 font-bold text-gray-600 dark:text-gray-400">7. PINJ. BANK JATENG (KEMANTRAN)</td>
                                                <td class="py-2.5 text-right font-black text-gray-900 dark:text-white font-mono">{{ formatRupiah(selectedSalary.salary_deduction.pinjaman_bpd_jateng) }}</td>
                                            </tr>
                                            <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all">
                                                <td class="py-2.5 font-bold text-gray-600 dark:text-gray-400">8. BANK TGR</td>
                                                <td class="py-2.5 text-right font-black text-gray-900 dark:text-white font-mono">{{ formatRupiah(selectedSalary.salary_deduction.bank_tgr) }}</td>
                                            </tr>
                                            <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all">
                                                <td class="py-2.5 font-bold text-gray-600 dark:text-gray-400">9. PREMI BPJS ANGGOTA (+)</td>
                                                <td class="py-2.5 text-right font-black text-gray-900 dark:text-white font-mono">{{ formatRupiah(selectedSalary.salary_deduction.premi_bpjs_anggota) }}</td>
                                            </tr>
                                            <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all">
                                                <td class="py-2.5 font-bold text-gray-600 dark:text-gray-400">10. DANA SOSIAL (DANSOS)</td>
                                                <td class="py-2.5 text-right font-black text-gray-900 dark:text-white font-mono">{{ formatRupiah(selectedSalary.salary_deduction.dansos) }}</td>
                                            </tr>
                                            <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all">
                                                <td class="py-2.5 font-bold text-gray-600 dark:text-gray-400">11. DENDA FINGERPRINT</td>
                                                <td class="py-2.5 text-right font-black text-gray-900 dark:text-white font-mono">{{ formatRupiah(selectedSalary.salary_deduction.denda_fingerprint) }}</td>
                                            </tr>
                                            <tr class="border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all">
                                                <td class="py-2.5 font-bold text-gray-600 dark:text-gray-400">12. POTONGAN LAINNYA 1</td>
                                                <td class="py-2.5 text-right font-black text-gray-900 dark:text-white font-mono">{{ formatRupiah(selectedSalary.salary_deduction.lainnya_1) }}</td>
                                            </tr>
                                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-all">
                                                <td class="py-2.5 font-bold text-gray-600 dark:text-gray-400">13. POTONGAN LAINNYA 2</td>
                                                <td class="py-2.5 text-right font-black text-gray-900 dark:text-white font-mono">{{ formatRupiah(selectedSalary.salary_deduction.lainnya_2) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-else class="text-center py-12 text-gray-400 italic text-sm">
                                    Belum ada data rincian potongan terdaftar untuk bulan ini.
                                </div>

                                <div class="bg-rose-50/30 dark:bg-rose-950/10 p-4 rounded-xl border border-rose-100/30 dark:border-rose-900/20 flex justify-between items-center mt-6">
                                    <span class="text-xs font-black text-rose-700 dark:text-rose-400 uppercase tracking-wider">TOTAL POTONGAN</span>
                                    <span class="text-base font-black text-rose-800 dark:text-rose-300 font-mono">
                                        {{ formatRupiah(selectedSalary.salary_deduction ? selectedSalary.salary_deduction.jumlah_potongan : 0) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- GAJI BERSIH (TAKE HOME PAY) & TERBILANG -->
                    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 text-white rounded-xl border border-slate-800 p-6 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-indigo-300 uppercase tracking-widest block">GAJI BERSIH DITERIMA (TAKE HOME PAY)</span>
                            <div class="text-3xl font-black tracking-tight text-white font-mono flex items-baseline">
                                <span class="text-xl font-bold mr-1">Rp</span>
                                <span>{{ formatRupiah(selectedSalary.salary_deduction ? selectedSalary.salary_deduction.gaji_bersih : selectedSalary.net_salary) }}</span>
                            </div>
                        </div>
                        <div class="flex-1 max-w-xl bg-white/5 backdrop-blur-md px-6 py-3 rounded-lg border border-white/10">
                            <span class="text-[9px] font-black text-indigo-300 uppercase block tracking-widest mb-0.5">Terbilang</span>
                            <span class="text-xs font-bold italic leading-relaxed text-indigo-100 block">
                                "{{ getTerbilang(selectedSalary.salary_deduction ? selectedSalary.salary_deduction.gaji_bersih : selectedSalary.net_salary) }}"
                            </span>
                        </div>
                    </div>

                    <!-- Digital Signature Mock / Footer Info -->
                    <div class="flex justify-between items-center text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider pt-4 border-t border-gray-100 dark:border-gray-800">
                        <span>Dicek & Validasi Oleh Bendahara Sekolah</span>
                        <span>Dicetak otomatis oleh Sistem Keuangan</span>
                    </div>

                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
