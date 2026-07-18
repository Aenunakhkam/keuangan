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
import Swal from 'sweetalert2';
import { debounce } from 'lodash';

const props = defineProps<{
    salaries: any;
    totalSubmittedAmount?: number;
    filters?: any;
}>();

const showingRejectModal = ref(false);
const selectedSalary = ref<any>(null);

const search = ref(props.filters?.search || '');
const month = ref(props.filters?.month || '');
const year = ref(props.filters?.year || '');
const status = ref(props.filters?.status || 'submitted');
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

watch([search, month, year, status, perPage], debounce(() => {
    router.get(route('salary-approvals.index'), {
        search: search.value,
        month: month.value,
        year: year.value,
        status: status.value,
        per_page: perPage.value,
    }, { preserveState: true, replace: true });
}, 300));

const rejectForm = useForm({
    rejection_note: ''
});

const openRejectModal = (salary: any) => {
    selectedSalary.value = salary;
    rejectForm.reset();
    showingRejectModal.value = true;
};

const submitReject = () => {
    if (!selectedSalary.value) return;
    rejectForm.post(route('salary-approvals.reject', selectedSalary.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showingRejectModal.value = false;
            Swal.fire('Berhasil!', 'Pengajuan gaji telah ditolak.', 'success');
        }
    });
};

const approveSalary = async (id: number) => {
    const result = await Swal.fire({
        title: 'Setujui Gaji',
        text: 'Apakah Anda yakin ingin menyetujui pengajuan gaji ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Setujui',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#10b981',
    });
    if (result.isConfirmed) {
        router.post(route('salary-approvals.approve', id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                Swal.fire('Berhasil!', 'Pengajuan gaji telah disetujui.', 'success');
            }
        });
    }
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

const submittableSelectedIds = computed(() => {
    return props.salaries.data
        .filter((sal: any) => selectedIds.value.includes(sal.id) && sal.approval_status === 'submitted')
        .map((sal: any) => sal.id);
});

const processBulkApprove = async () => {
    if (submittableSelectedIds.value.length === 0) return;
    const result = await Swal.fire({
        title: 'Setujui Massal',
        text: `Setujui ${submittableSelectedIds.value.length} pengajuan gaji yang terpilih?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Setujui Massal',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#10b981',
    });
    if (result.isConfirmed) {
        router.post(route('salary-approvals.bulk-approve'), {
            ids: submittableSelectedIds.value
        }, {
            preserveScroll: true,
            onSuccess: () => {
                selectedIds.value = [];
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Gaji massal telah berhasil disetujui.',
                    icon: 'success',
                    confirmButtonColor: '#10b981',
                });
            }
        });
    }
};

</script>

<template>
    <Head title="Persetujuan Gaji" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Persetujuan Gaji (Bendahara)</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Review dan setujui pengajuan slip gaji dari Admin.</p>
                    <div v-if="totalSubmittedAmount" class="mt-2.5 inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50/80 rounded-xl border border-blue-200/60 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-blue-600 font-bold text-xs">Total Pengajuan Bulan Ini:</span>
                        <span class="text-blue-700 font-black text-sm">{{ formatRupiah(totalSubmittedAmount || 0) }}</span>
                    </div>
                </div>
            </div>
        </template>

        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <!-- Bar Pencarian & Filter -->
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

                    <!-- Kalender Periode -->
                    <div class="relative max-w-xs">
                        <input 
                            v-model="filterPeriod" 
                            type="month" 
                            class="block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"
                        />
                    </div>
                    
                    <!-- Filter Status -->
                    <div class="relative max-w-xs">
                        <select 
                            v-model="status" 
                            class="block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"
                        >
                            <option value="">Semua Status</option>
                            <option value="submitted">Perlu Persetujuan (Diajukan)</option>
                            <option value="approved">Disetujui</option>
                            <option value="rejected">Ditolak</option>
                            <option value="pending">Draf Admin</option>
                        </select>
                    </div>

                    <!-- Limit Select -->
                    <div class="flex items-center space-x-2 text-xs font-bold text-gray-700 dark:text-gray-300">
                        <span>Lihat:</span>
                        <div class="relative">
                            <select 
                                v-model="perPage" 
                                class="appearance-none block pl-3 pr-8 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all cursor-pointer shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700/50"
                            >
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="all">Semua</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button 
                        v-if="submittableSelectedIds.length > 0"
                        @click="processBulkApprove"
                        class="w-full md:w-auto px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-emerald-500/20 hover:bg-emerald-700 transition-all flex items-center justify-center space-x-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Setujui Terpilih ({{ submittableSelectedIds.length }})</span>
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
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest">Pegawai</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center">Periode</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Gaji Bersih</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        <tr v-for="sal in salaries.data" :key="sal.id" class="hover:bg-gray-50 transition-all duration-200" :class="{'bg-indigo-50/30 dark:bg-indigo-900/10': selectedIds.includes(sal.id)}">
                            <td class="px-6 py-4 text-center">
                                <input 
                                    type="checkbox" 
                                    :value="sal.id" 
                                    v-model="selectedIds" 
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" 
                                />
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ sal.teacher?.name }}</div>
                                <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest">
                                    {{ [sal.teacher?.nipty, sal.teacher?.nipy].filter(Boolean).join(' / ') || 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-gray-500">{{ sal.month }}/{{ sal.year }}</td>
                            <td class="px-6 py-4 text-right font-black text-indigo-600">{{ formatRupiah(sal.final_net_salary) }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center gap-1.5">
                                    <span v-if="sal.approval_status === 'approved'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-200">
                                        Disetujui
                                    </span>
                                    <span v-else-if="sal.approval_status === 'rejected'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-rose-50 text-rose-700 border border-rose-200">
                                        Ditolak
                                    </span>
                                    <span v-else-if="sal.approval_status === 'submitted'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-200">
                                        Perlu Persetujuan
                                    </span>
                                    <span v-else class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-600 border border-gray-200">
                                        Draf Admin
                                    </span>

                                    <div v-if="sal.approval_status === 'rejected' && sal.rejection_note" class="text-[9px] text-rose-600 font-bold bg-rose-50/50 px-2 py-1.5 rounded-lg w-full max-w-[140px] border border-rose-100/80 mt-1 text-left whitespace-normal leading-tight">
                                        Catatan: {{ sal.rejection_note }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center space-x-2 whitespace-nowrap">
                                <template v-if="sal.approval_status === 'submitted'">
                                    <button @click="approveSalary(sal.id)" class="px-3 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-xl text-[10px] font-black uppercase transition-all inline-flex items-center space-x-1" title="Setujui">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Setujui</span>
                                    </button>
                                    <button @click="openRejectModal(sal)" class="px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl text-[10px] font-black uppercase transition-all inline-flex items-center space-x-1" title="Tolak">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span>Tolak</span>
                                    </button>
                                </template>
                                <span v-else class="text-xs text-gray-400 font-medium italic">Tidak ada aksi</span>
                            </td>
                        </tr>
                        <tr v-if="salaries.data.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 font-medium">Tidak ada data pengajuan gaji.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div v-if="salaries.links && salaries.links.length > 3" class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <div class="text-sm text-gray-500 font-medium">
                    Menampilkan <span class="font-black text-gray-900">{{ salaries.from || 0 }}</span> s.d <span class="font-black text-gray-900">{{ salaries.to || 0 }}</span> dari <span class="font-black text-gray-900">{{ salaries.total }}</span>
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

        <!-- Reject Modal -->
        <Modal :show="showingRejectModal" @close="showingRejectModal = false">
            <div class="p-8">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-6">Tolak Pengajuan Gaji</h2>
                <form @submit.prevent="submitReject" class="space-y-6">
                    <div class="space-y-1.5">
                        <InputLabel value="Alasan Penolakan" />
                        <TextInput 
                            v-model="rejectForm.rejection_note" 
                            type="text" 
                            class="w-full"
                            placeholder="Masukkan alasan penolakan (misal: Nominal salah, dsb)"
                            required
                        />
                        <div v-if="rejectForm.errors.rejection_note" class="text-red-500 text-xs mt-1">{{ rejectForm.errors.rejection_note }}</div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <SecondaryButton @click="showingRejectModal = false">Batal</SecondaryButton>
                        <PrimaryButton class="bg-rose-600 hover:bg-rose-700 focus:bg-rose-700 active:bg-rose-900" :disabled="rejectForm.processing">Tolak Pengajuan</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
