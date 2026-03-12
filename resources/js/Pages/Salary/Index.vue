<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import CurrencyInput from '@/Components/CurrencyInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { confirmDelete } from '@/Utils/sweetalert';
import Swal from 'sweetalert2';

const props = defineProps<{
    salaries: any;
    teachers: any[];
    teachingRate: number;
    transportAllowance: number;
}>();

const showingModal = ref(false);
const showingBulkModal = ref(false);
const modalMode = ref('create');
const selectedSalary = ref<any>(null);

const form = useForm({
    teacher_id: '',
    base_salary: 0,
    allowance: 0,
    transport_per_day: 0,
    days_present: 0,
    transport_allowance: 0,
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
    form.transport_allowance = Number(form.transport_per_day) * Number(form.days_present);
    form.net_salary = Number(form.base_salary) + Number(form.allowance) + Number(form.transport_allowance) - Number(form.deduction);
};

const updateSalaryFields = () => {
    const selectedTeacher = props.teachers.find(t => t.id === form.teacher_id);
    if (selectedTeacher) {
        form.base_salary = selectedTeacher.teaching_hours * props.teachingRate;
        
        let totalAllowance = 0;
        if (selectedTeacher.positions && selectedTeacher.positions.length > 0) {
            totalAllowance = selectedTeacher.positions.reduce((sum: number, pos: any) => sum + Number(pos.allowance), 0);
        }
        form.allowance = totalAllowance;
        form.transport_per_day = props.transportAllowance;
        form.days_present = 0; // Default 0 as requested by user to be input manually
        form.transport_allowance = 0;
        
        calculateNet();
    } else {
        form.base_salary = 0;
        form.allowance = 0;
        form.transport_allowance = 0;
        calculateNet();
    }
};
const openCreateModal = () => {
    modalMode.value = 'create';
    form.reset();
    form.clearErrors();
    form.month = new Date().getMonth() + 1;
    form.year = new Date().getFullYear();
    showingModal.value = true;
};

const openEditModal = (salary: any) => {
    modalMode.value = 'edit';
    selectedSalary.value = salary;
    form.teacher_id = salary.teacher_id;
    form.base_salary = salary.base_salary;
    form.allowance = salary.allowance;
    form.transport_per_day = salary.transport_per_day || 0;
    form.days_present = salary.days_present || 0;
    form.transport_allowance = salary.transport_allowance;
    form.deduction = salary.deduction;
    form.deduction_description = salary.deduction_description || '';
    form.net_salary = salary.net_salary;
    form.month = salary.month;
    form.year = salary.year;
    form.status = salary.status;
    form.clearErrors();
    showingModal.value = true;
};

const submit = () => {
    if (modalMode.value === 'create') {
        form.post(route('salaries.store'), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    } else {
        form.put(route('salaries.update', selectedSalary.value.id), {
            onSuccess: () => {
                showingModal.value = false;
                form.reset();
            },
        });
    }
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
</script>

<template>
    <Head title="Penggajian" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Penggajian Guru</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Kelola slip gaji dan pembayaran honorarium pengajar.</p>
                </div>
                <div class="flex space-x-3">
                    <button 
                        @click="showingBulkModal = true"
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
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#003B73] border-b border-indigo-800">
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center w-16">No</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Guru</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center">Periode</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center">Hadir</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center">Tgl Gajian</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Gaji Bersih</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        <tr v-for="(sal, index) in salaries.data" :key="sal.id" class="hover:bg-gray-50 transition-all duration-200">
                            <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-gray-300">{{ (salaries.current_page - 1) * salaries.per_page + index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ sal.teacher.name }}</div>
                                <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest">{{ sal.teacher.nip || 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-gray-500">{{ sal.month }}/{{ sal.year }}</td>
                            <td class="px-6 py-4 text-center font-bold text-gray-700">{{ sal.days_present || 0 }} Hari</td>
                            <td class="px-6 py-4 text-center font-bold text-gray-500 text-xs">
                                {{ sal.payment_date ? new Date(sal.payment_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-black text-indigo-600">Rp {{ Number(sal.net_salary || 0).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest" :class="sal.status === 'paid' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600'">
                                    {{ sal.status === 'paid' ? 'Dibayarkan' : 'Menunggu' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="printSalary(sal.id)" class="p-2 text-sky-600 hover:bg-sky-50 rounded-xl transition-all" title="Cetak Struk">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                </button>
                                <button v-if="sal.status === 'pending'" @click="openEditModal(sal)" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button v-if="sal.status === 'pending'" @click="processPayment(sal.id)" class="px-3 py-1.5 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase shadow-lg shadow-emerald-500/20 hover:scale-105 transition-all">
                                    Bayar
                                </button>
                                <button @click="deleteSalary(sal.id)" class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Salary Modal -->
        <Modal :show="showingModal" @close="showingModal = false">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">
                    {{ modalMode === 'create' ? 'Buat Slip Gaji' : 'Edit Slip Gaji' }}
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
                            <p class="text-[10px] text-gray-500 bg-indigo-50 px-3 py-2 rounded-xl text-indigo-700 font-medium">Nilai Gaji Pokok & Tunjangan akan terisi otomatis berdasarkan profil jabatan & jam mengajar guru.</p>
                        </div>
                        <div class="space-y-1.5">
                            <InputLabel value="Gaji Pokok (Otomatis)" />
                            <TextInput v-model="form.base_salary" type="number" @input="calculateNet" class="w-full bg-gray-100" readonly />
                        </div>
                        <div class="space-y-1.5 md:col-span-1">
                            <InputLabel value="Tunjangan (Otomatis)" />
                            <TextInput v-model="form.allowance" type="number" @input="calculateNet" class="w-full bg-gray-100" readonly />
                        </div>
                        <div class="space-y-1.5 md:col-span-1">
                            <InputLabel value="Tarif Transport / Hari" />
                            <CurrencyInput v-model="form.transport_per_day" @update:modelValue="calculateNet" class="w-full" />
                        </div>
                        <div class="space-y-1.5 md:col-span-1">
                            <InputLabel value="Jumlah Hari Hadir" />
                            <TextInput v-model="form.days_present" type="number" min="0" max="31" @input="calculateNet" class="w-full" placeholder="0" />
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <div class="flex justify-between items-center bg-sky-50 p-3 rounded-xl border border-sky-100">
                                <span class="text-xs font-bold text-sky-700 uppercase">Total Transport</span>
                                <span class="text-sm font-black text-sky-800">Rp {{ Number(form.transport_allowance).toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</span>
                            </div>
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <InputLabel value="Potongan Tambahan" />
                            <CurrencyInput v-model="form.deduction" @update:modelValue="calculateNet" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="0" />
                        </div>
                        <div v-if="form.deduction > 0" class="space-y-1.5 md:col-span-2 animate-fade-in">
                            <InputLabel value="Keterangan Potongan" />
                            <TextInput v-model="form.deduction_description" type="text" list="deductionOptions" class="w-full" placeholder="Ketik atau pilih keterangan potongan..." />
                            <datalist id="deductionOptions">
                                <option value="Pinjaman / Kasbon"></option>
                                <option value="BPJS Kesehatan"></option>
                                <option value="BPJS Ketenagakerjaan"></option>
                                <option value="Koperasi"></option>
                                <option value="Infaq / Zakat"></option>
                            </datalist>
                        </div>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-2xl">
                        <div class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Total Gaji Bersih</div>
                        <div class="text-3xl font-black text-indigo-600">Rp {{ form.net_salary.toLocaleString('id-ID', {maximumFractionDigits: 0}) }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <InputLabel value="Bulan" />
                            <TextInput v-model="form.month" type="number" min="1" max="12" />
                        </div>
                        <div class="space-y-1.5">
                            <InputLabel value="Tahun" />
                            <TextInput v-model="form.year" type="number" />
                        </div>
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
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <InputLabel value="Bulan" />
                            <TextInput v-model="bulkForm.month" type="number" min="1" max="12" />
                        </div>
                        <div class="space-y-1.5">
                            <InputLabel value="Tahun" />
                            <TextInput v-model="bulkForm.year" type="number" />
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <SecondaryButton @click="showingBulkModal = false">Batal</SecondaryButton>
                        <PrimaryButton :disabled="bulkForm.processing">Generate Sekarang</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
