<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import CurrencyInput from '@/Components/CurrencyInput.vue';
import debounce from 'lodash/debounce';
import { confirmDelete } from '@/Utils/sweetalert';
import axios from 'axios';
import Swal from 'sweetalert2';

const props = defineProps<{
    teachers: any;
    positions: any;
    filters: any;
}>();

const search = ref(props.filters?.search || '');
const perPage = ref(props.filters?.per_page || '10');

watch(search, debounce((value) => {
    router.get(route('teachers.index'), { search: value, per_page: perPage.value }, { preserveState: true, replace: true });
}, 300));

watch(perPage, (value) => {
    router.get(route('teachers.index'), { search: search.value, per_page: value }, { preserveState: true, replace: true });
});

const showingModal = ref(false);
const showingViewModal = ref(false);
const modalMode = ref('create'); 
const selectedTeacher = ref<any>(null);
const viewingTeacher = ref<any>(null);

const form = useForm({
    name: '',
    email: '',
    nipty: '',
    nipy: '',
    birth_place: '',
    birth_date: '',
    education: '',
    major: '',
    unit: '',
    service_years: 0,
    service_months: 0,
    grade: '',
    basic_salary: 0,
    other_allowance: 0,
    joined_date: '',
    gender: '',
    position_ids: [] as number[],
});

const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    form.clearErrors();
    showingModal.value = true;
};

const openEditModal = (teacher: any) => {
    modalMode.value = 'edit';
    selectedTeacher.value = teacher;
    form.name = teacher.name;
    form.email = teacher.email || '';
    form.nipty = teacher.nipty;
    form.nipy = teacher.nipy;
    form.birth_place = teacher.birth_place;
    form.birth_date = teacher.birth_date;
    form.education = teacher.education;
    form.major = teacher.major || '';
    form.unit = teacher.unit;
    form.service_years = teacher.service_years || 0;
    form.service_months = teacher.service_months || 0;
    form.grade = teacher.grade;
    form.basic_salary = teacher.basic_salary || 0;
    form.other_allowance = teacher.other_allowance || 0;
    form.joined_date = teacher.joined_date;
    form.gender = teacher.gender || '';
    form.position_ids = teacher.positions ? teacher.positions.map((p: any) => p.id) : [];
    form.clearErrors();
    showingModal.value = true;
};

const openViewModal = (teacher: any) => {
    viewingTeacher.value = teacher;
    showingViewModal.value = true;
};

const submit = () => {
    if (modalMode.value === 'create') {
        form.post(route('teachers.store'), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('teachers.update', selectedTeacher.value.id), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    }
};

const deleteTeacher = async (teacher: any) => {
    const result = await confirmDelete(
        'Hapus Data Guru',
        `Apakah Anda yakin ingin menghapus data guru ${teacher.name}? Tindakan ini tidak dapat dibatalkan.`
    );

    if (result.isConfirmed) {
        router.delete(route('teachers.destroy', teacher.id));
    }
};

const fileInput = ref<HTMLInputElement | null>(null);

const triggerFileInput = () => {
    fileInput.value?.click();
};

const handleImport = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (!file) return;

    Swal.fire({
        title: 'Mengimpor Data...',
        text: 'Harap tunggu sebentar, sistem sedang memproses data.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const formData = new FormData();
    formData.append('file', file);

    router.post(route('teachers.import'), formData, {
        onSuccess: (page) => {
            if (page.props.flash?.error) {
                if (fileInput.value) fileInput.value.value = '';
                return;
            }
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: 'Data pegawai berhasil diimpor!',
                timer: 2000,
                showConfirmButton: false
            });
            if (fileInput.value) fileInput.value.value = '';
        },
        onError: (errors) => {
            let errMsg = 'Terjadi kesalahan saat mengimpor data.';
            if (errors.file) {
                errMsg = errors.file;
            }
            Swal.fire({
                icon: 'error',
                title: 'Gagal Impor',
                html: errMsg
            });
            if (fileInput.value) fileInput.value.value = '';
        }
    });
};

// Auto-calculate Golongan (Grade) & Basic Salary based on Education and Service Years
watch([() => form.education, () => form.service_years], debounce(async ([newEdu, newYears]) => {
    if (!newEdu) return;

    let autoGrade = '';
    
    switch (newEdu) {
        case 'SD': autoGrade = 'I'; break;
        case 'SMA/SMK': autoGrade = 'II'; break;
        case 'D3': autoGrade = 'III'; break;
        case 'S1': autoGrade = 'IV'; break;
        case 'S2': autoGrade = 'V'; break;
        case 'S3': autoGrade = 'VI'; break;
    }

    if (autoGrade) {
        form.grade = autoGrade;
    }

    // Fetch Nominal from Master Matrix
    try {
        const response = await axios.get(route('salary-scales.calculate'), {
            params: { education: newEdu, years: newYears }
        });
        if (response.data.amount > 0) {
            form.basic_salary = response.data.amount;
        }
    } catch (error) {
        console.error('Gagal mengambil data gaji pokok:', error);
    }
}, 300));

// Auto-calculate Masa Kerja (Service Years & Months) based on Joined Date
watch(() => form.joined_date, (newDate) => {
    if (!newDate) {
        form.service_years = 0;
        form.service_months = 0;
        return;
    }

    const start = new Date(newDate);
    const end = new Date();

    if (start > end) {
        form.service_years = 0;
        form.service_months = 0;
        return;
    }

    let years = end.getFullYear() - start.getFullYear();
    let months = end.getMonth() - start.getMonth();

    if (months < 0) {
        years--;
        months += 12;
    }

    form.service_years = years;
    form.service_months = months;
});
</script>

<template>
    <Head title="Data Pegawai" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Data Pegawai</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Kelola data tenaga pendidik dan kependidikan di sini.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Unduh Template -->
                    <a 
                        :href="route('teachers.template')"
                        class="px-4 py-3 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30 rounded-2xl text-sm font-black transform transition-transform hover:scale-[1.02] active:scale-[0.98] flex items-center space-x-2 shadow-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Unduh Template</span>
                    </a>

                    <!-- Import Pegawai -->
                    <button 
                        @click="triggerFileInput"
                        class="px-4 py-3 bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30 rounded-2xl text-sm font-black transform transition-transform hover:scale-[1.02] active:scale-[0.98] flex items-center space-x-2 shadow-sm"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>Import Pegawai</span>
                    </button>
                    <input 
                        type="file" 
                        ref="fileInput" 
                        class="hidden" 
                        @change="handleImport" 
                        accept=".csv" 
                    />

                    <!-- Tambah Pegawai -->
                    <button 
                        @click="openCreateModal"
                        class="px-5 py-3 bg-[#003B73] text-white rounded-2xl text-sm font-black shadow-xl shadow-indigo-500/20 transform transition-transform hover:scale-[1.02] active:scale-[0.98] flex items-center space-x-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Pegawai</span>
                    </button>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Filter -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="max-w-md w-full">
                    <TextInput v-model="search" placeholder="Cari Guru atau NIP..." class="block w-full" />
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm font-bold text-gray-500">Lihat:</span>
                    <select 
                        v-model="perPage"
                        class="rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm font-bold text-gray-700 dark:text-gray-300 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all px-4 py-2.5"
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
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#003B73] border-b border-indigo-800">
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center w-16">No</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Nama / Pend.</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center">JK</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">NIPTY / NIPY</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Unit / Gol</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Masa Kerja</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Jabatan</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="(teacher, index) in teachers.data" :key="teacher.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                            <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-gray-300">{{ (teachers.current_page - 1) * teachers.per_page + index + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-gray-700 dark:text-gray-300">
                                <div class="font-black text-gray-900 dark:text-white">{{ teacher.name }}</div>
                                <div v-if="teacher.education" class="text-[10px] font-bold text-emerald-600 uppercase">{{ teacher.education }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span v-if="teacher.gender" class="text-xs font-bold text-gray-600">
                                    {{ teacher.gender === 'Laki-laki' ? 'L' : 'P' }}
                                </span>
                                <span v-else class="text-xs text-gray-300">-</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-bold text-gray-900 dark:text-white">TY: {{ teacher.nipty || '-' }}</div>
                                <div class="text-xs font-medium text-gray-500 italic">Y: {{ teacher.nipy || '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-bold text-indigo-600">{{ teacher.unit || '-' }}</div>
                                <div class="text-xs font-medium text-gray-500">{{ teacher.grade || '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-gray-700">
                                {{ teacher.service_years }} Thn {{ teacher.service_months }} Bln
                            </td>
                            <td class="px-6 py-4">
                                <div v-if="teacher.positions && teacher.positions.length > 0" class="flex flex-wrap gap-1">
                                    <span v-for="pos in teacher.positions" :key="pos.id" class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ pos.name }}
                                    </span>
                                </div>
                                <div v-else class="text-xs text-gray-400 italic">Tanpa Jabatan</div>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1">
                                <button @click="openViewModal(teacher)" class="p-2 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-xl transition-all" title="Lihat Detail">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <button @click="openEditModal(teacher)" class="p-2 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all" title="Edit Data">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button @click="deleteTeacher(teacher)" class="p-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition-all" title="Hapus Data">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                            <tr v-if="teachers.data.length === 0">
                                <td colspan="8" class="px-6 py-20 text-center text-gray-500 font-bold">Belum ada data guru.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Bar -->
                <div v-if="teachers.links && teachers.links.length > 3" class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <div class="text-sm text-gray-500 font-medium">
                        Menampilkan <span class="font-black text-gray-900">{{ teachers.from || 0 }}</span> s.d <span class="font-black text-gray-900">{{ teachers.to || 0 }}</span> dari <span class="font-black text-gray-900">{{ teachers.total }}</span> pegawai
                    </div>
                    <div class="flex items-center space-x-1">
                        <template v-for="(link, k) in teachers.links" :key="k">
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
        </div>

        <!-- Modal Form -->
        <Modal :show="showingModal" @close="showingModal = false">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">
                    {{ modalMode === 'create' ? 'Tambah Guru Baru' : 'Edit Data Guru' }}
                </h2>

                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Identitas Utama -->
                    <div class="space-y-1.5 md:col-span-2">
                        <InputLabel for="name" value="Nama Lengkap" />
                        <TextInput id="name" v-model="form.name" type="text" class="block w-full" required />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <InputLabel for="email" value="Alamat Email Akun (Opsional, bawaan dari sistem jika kosong)" />
                        <TextInput id="email" v-model="form.email" type="email" class="block w-full" placeholder="email@contoh.com" />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="nipty" value="NIPTY" />
                        <TextInput id="nipty" v-model="form.nipty" type="text" class="block w-full" />
                        <InputError :message="form.errors.nipty" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="nipy" value="NIPY" />
                        <TextInput id="nipy" v-model="form.nipy" type="text" class="block w-full" />
                        <InputError :message="form.errors.nipy" />
                    </div>

                    <!-- Kelahiran -->
                    <div class="space-y-1.5">
                        <InputLabel for="birth_place" value="Tempat Lahir" />
                        <TextInput id="birth_place" v-model="form.birth_place" type="text" class="block w-full" />
                        <InputError :message="form.errors.birth_place" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="birth_date" value="Tanggal Lahir" />
                        <TextInput id="birth_date" v-model="form.birth_date" type="date" class="block w-full" />
                        <InputError :message="form.errors.birth_date" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="gender" value="Jenis Kelamin" />
                        <select 
                            id="gender" 
                            v-model="form.gender" 
                            class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200"
                        >
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                        <InputError :message="form.errors.gender" />
                    </div>

                    <!-- Jabatan & Kepegawaian -->
                    <div class="space-y-1.5 md:col-span-2">
                        <InputLabel value="Jabatan & Kepegawaian" class="text-xs font-black uppercase tracking-widest text-indigo-600 border-b border-indigo-100 pb-2 mb-2 mt-4" />
                    </div>

                    <div class="space-y-1.5 md:col-span-2">
                        <InputLabel for="position_ids" value="Jabatan Struktural (Pilih yang sesuai)" />
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                            <label v-for="pos in positions" :key="pos.id" class="flex items-center space-x-3 cursor-pointer p-3 rounded-2xl border border-gray-100 hover:bg-gray-50 transition-colors">
                                <input type="checkbox" :value="pos.id" v-model="form.position_ids" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-5 h-5 transition-all">
                                <div>
                                    <span class="block text-sm font-bold text-gray-900">{{ pos.name }}</span>
                                    <span class="block text-[10px] font-medium text-emerald-600">+ Rp {{ Number(pos.allowance).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</span>
                                </div>
                            </label>
                        </div>
                        <InputError :message="form.errors.position_ids" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="education" value="Pendidikan Terakhir" />
                        <select 
                            id="education" 
                            v-model="form.education" 
                            class="block w-full px-5 py-3.5 rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200"
                        >
                            <option value="">Pilih Pendidikan</option>
                            <option value="SD">SD / SLTP</option>
                            <option value="SMA/SMK">SMA / SMK / SLTA</option>
                            <option value="D3">D1 - D3 (Diploma)</option>
                            <option value="S1">D4 / S1 (Sarjana)</option>
                            <option value="S2">S2 (Magister)</option>
                            <option value="S3">S3 (Doktor)</option>
                        </select>
                        <InputError :message="form.errors.education" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="major" value="Jurusan (Prodi)" />
                        <TextInput id="major" v-model="form.major" type="text" class="block w-full" placeholder="Contoh: Pendidikan Matematika, Teknik Informatika" />
                        <InputError :message="form.errors.major" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="unit" value="Unit Kerja" />
                        <TextInput id="unit" v-model="form.unit" type="text" class="block w-full" placeholder="Contoh: Tata Usaha, Lab, dll" />
                        <InputError :message="form.errors.unit" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="joined_date" value="Tanggal Mulai Bekerja" />
                        <TextInput id="joined_date" v-model="form.joined_date" type="date" class="block w-full" />
                        <InputError :message="form.errors.joined_date" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="grade" value="Golongan (Otomatis)" />
                        <TextInput id="grade" v-model="form.grade" type="text" class="block w-full" placeholder="Otomatis dari Pendidikan" />
                        <InputError :message="form.errors.grade" />
                    </div>

                    <!-- Masa Kerja Input Manual -->
                    <div class="space-y-1.5">
                        <InputLabel for="service_years" value="Masa Kerja - Tahun (Otomatis)" />
                        <TextInput id="service_years" v-model="form.service_years" type="number" min="0" class="block w-full" />
                        <InputError :message="form.errors.service_years" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="service_months" value="Masa Kerja - Bulan (Otomatis)" />
                        <TextInput id="service_months" v-model="form.service_months" type="number" min="0" max="11" class="block w-full" />
                        <InputError :message="form.errors.service_months" />
                    </div>

                    <!-- Penggajian Spesifik -->
                    <div class="space-y-1.5 md:col-span-2">
                        <InputLabel value="Penyesuaian Gaji Dasar" class="text-xs font-black uppercase tracking-widest text-indigo-600 border-b border-indigo-100 pb-2 mb-2 mt-4" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="basic_salary" value="Gaji Pokok Dasar (Individual)" />
                        <CurrencyInput id="basic_salary" v-model="form.basic_salary" class="block w-full" />
                        <p class="text-[10px] text-gray-500 italic">Nilai dasar spesifik pegawai ini.</p>
                        <InputError :message="form.errors.basic_salary" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="other_allowance" value="Tunjangan Tetap Lainnya" />
                        <CurrencyInput id="other_allowance" v-model="form.other_allowance" class="block w-full" />
                        <p class="text-[10px] text-gray-500 italic">Tunjangan tetap di luar jabatan.</p>
                        <InputError :message="form.errors.other_allowance" />
                    </div>

                    <div class="md:col-span-2 mt-4 flex justify-end space-x-3">
                        <SecondaryButton @click="showingModal = false">Batal</SecondaryButton>
                        <PrimaryButton :disabled="form.processing">
                            {{ modalMode === 'create' ? 'Simpan' : 'Perbarui' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- View Detail Modal -->
        <Modal :show="showingViewModal" @close="showingViewModal = false">
            <div v-if="viewingTeacher" class="p-8">
                <div class="flex items-center justify-between mb-8 border-b border-gray-100 pb-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-600 flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-indigo-200">
                            {{ viewingTeacher.name.charAt(0) }}
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ viewingTeacher.name }}</h2>
                            <p class="text-sm font-bold text-indigo-600 uppercase tracking-widest">{{ viewingTeacher.nipty || 'NIPTY Belum Ada' }}</p>
                        </div>
                    </div>
                    <button @click="showingViewModal = false" class="p-2 text-gray-400 hover:bg-gray-100 rounded-xl transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Data Pribadi -->
                    <div class="space-y-6">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest border-l-4 border-indigo-500 pl-3">Data Pribadi & Identitas</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase mb-1">NIPTY</div>
                                <div class="text-sm font-bold text-gray-900">{{ viewingTeacher.nipty || '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase mb-1">NIPY</div>
                                <div class="text-sm font-bold text-gray-900">{{ viewingTeacher.nipy || '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase mb-1">Akun Email</div>
                                <div class="text-sm font-bold text-gray-900">{{ viewingTeacher.email || '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase mb-1">Jenis Kelamin</div>
                                <div class="text-sm font-bold text-gray-900">{{ viewingTeacher.gender || '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase mb-1">Tempat Lahir</div>
                                <div class="text-sm font-bold text-gray-900">{{ viewingTeacher.birth_place || '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase mb-1">Tanggal Lahir</div>
                                <div class="text-sm font-bold text-gray-900">{{ viewingTeacher.birth_date || '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Kepegawaian -->
                    <div class="space-y-6">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest border-l-4 border-emerald-500 pl-3">Data Kepegawaian</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase mb-1">Pendidikan</div>
                                <div class="text-sm font-bold text-gray-900">{{ viewingTeacher.education || '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase mb-1">Jurusan (Prodi)</div>
                                <div class="text-sm font-bold text-gray-900">{{ viewingTeacher.major || '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase mb-1">Unit Kerja</div>
                                <div class="text-sm font-bold text-gray-900">{{ viewingTeacher.unit || '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase mb-1">Masa Kerja</div>
                                <div class="text-sm font-bold text-gray-900">{{ viewingTeacher.service_years }} Thn {{ viewingTeacher.service_months }} Bln</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase mb-1">Golongan</div>
                                <div class="text-sm font-bold text-gray-900">{{ viewingTeacher.grade || '-' }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-emerald-600 uppercase mb-1">Gaji Pokok</div>
                                <div class="text-sm font-extrabold text-emerald-600">Rp {{ Number(viewingTeacher.basic_salary || 0).toLocaleString('id-ID') }}</div>
                            </div>
                            <div v-if="viewingTeacher.other_allowance > 0">
                                <div class="text-[10px] font-black text-emerald-600 uppercase mb-1">Tunjangan Tetap</div>
                                <div class="text-sm font-extrabold text-emerald-600">Rp {{ Number(viewingTeacher.other_allowance || 0).toLocaleString('id-ID') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Jabatan Struktural -->
                    <div class="md:col-span-2 space-y-4">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest border-l-4 border-blue-500 pl-3">Jabatan Struktural & Tunjangan</h3>
                        <div v-if="viewingTeacher.positions && viewingTeacher.positions.length > 0" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div v-for="pos in viewingTeacher.positions" :key="pos.id" class="p-4 bg-gray-50 rounded-2xl border border-gray-100 flex justify-between items-center">
                                <span class="text-sm font-bold text-gray-700">{{ pos.name }}</span>
                                <div class="text-right">
                                    <div class="text-xs font-black text-emerald-600">Rp {{ Number(pos.allowance).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-400 italic bg-gray-50 p-4 rounded-2xl text-center">Tidak menjabat posisi struktural tambahan.</div>
                    </div>

                    <!-- Konfigurasi BPJS Pegawai -->
                    <div class="md:col-span-2 space-y-4">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest border-l-4 border-indigo-500 pl-3">Informasi & Estimasi BPJS</h3>
                        <div v-if="viewingTeacher.bpjs_info" class="p-5 bg-indigo-50/50 dark:bg-gray-700/20 rounded-3xl border border-indigo-100/50 dark:border-gray-600 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div>
                                <span class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block mb-1">Kategori BPJS</span>
                                <span class="text-base font-black text-gray-900 dark:text-white">
                                    {{ viewingTeacher.bpjs_info.category_code ? 'Kategori ' + viewingTeacher.bpjs_info.category_code + ' - ' : '' }}{{ viewingTeacher.bpjs_info.category_name }}
                                </span>
                                <div class="flex items-center space-x-3 mt-2">
                                    <span 
                                        class="px-2.5 py-1 text-[10px] font-black rounded-lg uppercase tracking-wider flex items-center space-x-1"
                                        :class="viewingTeacher.bpjs_info.has_health ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-100 text-gray-400 dark:bg-gray-800'"
                                    >
                                        <span>Kesehatan:</span>
                                        <span>{{ viewingTeacher.bpjs_info.has_health ? 'Aktif' : 'Non-Aktif' }}</span>
                                    </span>
                                    <span 
                                        class="px-2.5 py-1 text-[10px] font-black rounded-lg uppercase tracking-wider flex items-center space-x-1"
                                        :class="viewingTeacher.bpjs_info.has_naker ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-gray-100 text-gray-400 dark:bg-gray-800'"
                                    >
                                        <span>Ketenagakerjaan:</span>
                                        <span>{{ viewingTeacher.bpjs_info.has_naker ? 'Aktif' : 'Non-Aktif' }}</span>
                                    </span>
                                </div>
                            </div>
                            
                            <div v-if="viewingTeacher.bpjs_info.category_name !== 'Belum Diatur' && viewingTeacher.bpjs_info.category_name !== 'Non-BPJS'" class="grid grid-cols-2 sm:grid-cols-3 gap-4 w-full md:w-auto">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-2xl border border-gray-100 dark:border-gray-700 min-w-[120px]">
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-wider">Tunjangan BPJS</div>
                                    <div class="text-xs font-extrabold text-emerald-600 mt-1">Rp {{ Number(viewingTeacher.bpjs_info.bpjs_allowance).toLocaleString('id-ID') }}</div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-2xl border border-gray-100 dark:border-gray-700 min-w-[120px]">
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-wider">Potongan BPJS Kes</div>
                                    <div class="text-xs font-extrabold text-rose-600 mt-1">Rp {{ Number(viewingTeacher.bpjs_info.bpjs_health).toLocaleString('id-ID') }}</div>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-2xl border border-gray-100 dark:border-gray-700 min-w-[120px]">
                                    <div class="text-[9px] font-black text-gray-400 uppercase tracking-wider">Potongan BPJS TK</div>
                                    <div class="text-xs font-extrabold text-rose-600 mt-1">Rp {{ Number(viewingTeacher.bpjs_info.bpjs_naker).toLocaleString('id-ID') }}</div>
                                </div>
                            </div>
                            <div v-else class="text-sm font-bold text-gray-400 dark:text-gray-500 italic">
                                Belum ada estimasi iuran karena kategori BPJS non-aktif.
                            </div>
                        </div>
                    </div>


                </div>

                <div class="mt-10 flex justify-end">
                    <PrimaryButton @click="showingViewModal = false">Tutup Detail</PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
