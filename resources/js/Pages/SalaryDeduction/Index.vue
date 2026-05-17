<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import Swal from 'sweetalert2';
import CurrencyInput from '@/Components/CurrencyInput.vue';

const props = defineProps<{
    deductions: any[];
    filters: {
        month: number;
        year: number;
    };
}>();

const selectedMonth = ref(props.filters.month);
const selectedYear = ref(props.filters.year);

const months = [
    { value: 1, label: 'Januari' }, { value: 2, label: 'Februari' }, { value: 3, label: 'Maret' },
    { value: 4, label: 'April' }, { value: 5, label: 'Mei' }, { value: 6, label: 'Juni' },
    { value: 7, label: 'Juli' }, { value: 8, label: 'Agustus' }, { value: 9, label: 'September' },
    { value: 10, label: 'Oktober' }, { value: 11, label: 'November' }, { value: 12, label: 'Desember' }
];

const currentYear = new Date().getFullYear();
const years = Array.from({length: 5}, (_, i) => currentYear - 2 + i);

const filterReport = () => {
    router.get(route('salary-deductions.index'), {
        month: selectedMonth.value,
        year: selectedYear.value
    }, { preserveState: true, replace: true });
};

watch([selectedMonth, selectedYear], () => {
    filterReport();
});

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
};

// Form handling
const form = useForm({
    deductions: [] as any[]
});

onMounted(() => {
    // Deep copy to break reactivity from props directly
    form.deductions = JSON.parse(JSON.stringify(props.deductions));
});

watch(() => props.deductions, (newVal) => {
    form.deductions = JSON.parse(JSON.stringify(newVal));
}, { deep: true });

// Reactively calculate totals when inputs change using a deep watcher
watch(() => form.deductions, (newVal) => {
    if (!newVal) return;
    
    newVal.forEach((row) => {
        // Ensure numbers
        const simpanan_wajib = Number(row.simpanan_wajib) || 0;
        const simpanan_sukarela = Number(row.simpanan_sukarela) || 0;
        const angsuran_koperasi = Number(row.angsuran_koperasi) || 0;
        const dplk_slawi = Number(row.dplk_slawi) || 0;
        const dplk_kemantran = Number(row.dplk_kemantran) || 0;
        const pinjaman_bpd_jateng = Number(row.pinjaman_bpd_jateng) || 0;
        const bank_tgr = Number(row.bank_tgr) || 0;
        const premi_bpjs_anggota = Number(row.premi_bpjs_anggota) || 0;
        const dansos = Number(row.dansos) || 0;
        const lainnya_1 = Number(row.lainnya_1) || 0;
        const lainnya_2 = Number(row.lainnya_2) || 0;
        const denda_fingerprint = Number(row.denda_fingerprint) || 0;

        // Tab Khusus is always 8% of spj_netto
        const tab_khusus = Math.round(row.spj_netto * 0.08);

        const jumlah_koperasi = tab_khusus + simpanan_wajib + simpanan_sukarela + angsuran_koperasi;
        const jumlah_bpd = dplk_slawi + dplk_kemantran + pinjaman_bpd_jateng;
        
        const jumlah_potongan = jumlah_koperasi + jumlah_bpd + bank_tgr + premi_bpjs_anggota + dansos + lainnya_1 + lainnya_2 + denda_fingerprint;
        
        const gaji_bersih = row.spj_netto - jumlah_potongan;
        
        // Update safely to avoid infinite loops
        if (row.tab_khusus !== tab_khusus) row.tab_khusus = tab_khusus;
        if (row.jumlah_koperasi !== jumlah_koperasi) row.jumlah_koperasi = jumlah_koperasi;
        if (row.jumlah_bpd !== jumlah_bpd) row.jumlah_bpd = jumlah_bpd;
        if (row.jumlah_potongan !== jumlah_potongan) row.jumlah_potongan = jumlah_potongan;
        if (row.gaji_bersih !== gaji_bersih) row.gaji_bersih = gaji_bersih;
    });
}, { deep: true });

const submitBulk = () => {
    form.post(route('salary-deductions.storeBulk'), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire('Berhasil!', 'Data potongan & gaji bersih berhasil disimpan.', 'success');
        }
    });
};

</script>

<template>
    <Head title="Potongan Gaji Pegawai" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Potongan Gaji & Take Home Pay</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Kelola potongan Koperasi, BPD, Sektoral dan hasilkan Gaji Bersih akhir.</p>
                </div>
                <button @click="submitBulk" :disabled="form.processing" class="px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700 disabled:opacity-50 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Semua Potongan' }}</span>
                </button>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Filters -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Pilih Bulan</label>
                    <select v-model="selectedMonth" class="rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                        <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Pilih Tahun</label>
                    <select v-model="selectedYear" class="rounded-xl border-gray-200 focus:ring-indigo-500 focus:border-indigo-500">
                        <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                    </select>
                </div>
            </div>

            <!-- Tabel Form Potongan -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden relative">
                <div class="overflow-x-auto pb-4">
                    <table class="w-full text-left border-collapse min-w-max">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-slate-500 uppercase text-center w-12 border-r border-gray-200 bg-slate-50 whitespace-nowrap sticky left-0 z-10 shadow-[1px_0_0_#e5e7eb]">No</th>
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-slate-500 uppercase w-32 border-r border-gray-200 bg-slate-50 whitespace-nowrap sticky left-12 z-10 shadow-[1px_0_0_#e5e7eb]">NIPY</th>
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-slate-500 uppercase w-48 border-r border-gray-200 bg-slate-50 whitespace-nowrap sticky left-44 z-10 shadow-[1px_0_0_#e5e7eb]">Nama Pegawai</th>
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-slate-500 uppercase w-48 border-r border-gray-200 bg-slate-50 whitespace-nowrap sticky left-92 z-10 shadow-[1px_0_0_#e5e7eb]">Jabatan</th>
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-slate-500 uppercase text-right border-r border-gray-200 bg-slate-50 whitespace-nowrap">Netto Awal (SPJ)</th>
                                
                                <th colspan="5" class="px-4 py-2 text-[10px] font-black text-indigo-700 uppercase text-center border-b border-gray-200 bg-indigo-50 whitespace-nowrap">Kluster Koperasi</th>
                                <th colspan="4" class="px-4 py-2 text-[10px] font-black text-emerald-700 uppercase text-center border-b border-gray-200 bg-emerald-50 whitespace-nowrap">Kluster BPD</th>
                                <th colspan="6" class="px-4 py-2 text-[10px] font-black text-amber-700 uppercase text-center border-b border-gray-200 bg-amber-50 whitespace-nowrap">Sektoral & Lainnya</th>
                                
                                <th rowspan="2" class="px-4 py-4 text-[10px] font-black text-rose-700 uppercase text-right border-l border-gray-200 bg-rose-50 whitespace-nowrap">Total Potongan</th>
                                <th rowspan="2" class="px-4 py-4 text-xs font-black text-emerald-700 uppercase text-right border-l border-gray-200 bg-emerald-100 whitespace-nowrap">Gaji Bersih (THP)</th>
                            </tr>
                            <tr class="border-b border-gray-200">
                                <!-- Koperasi -->
                                <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase text-right bg-indigo-50/30 whitespace-nowrap border-l border-gray-200">Tab. Khusus (8%)</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase text-right bg-indigo-50/30 whitespace-nowrap">Simp. Wajib</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase text-right bg-indigo-50/30 whitespace-nowrap">Simp. Sukarela</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase text-right bg-indigo-50/30 whitespace-nowrap">Angsuran Kop.</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-indigo-600 uppercase text-right bg-indigo-100/50 whitespace-nowrap">Jml Koperasi</th>

                                <!-- BPD -->
                                <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase text-right bg-emerald-50/30 whitespace-nowrap border-l border-gray-200">DPLK Slawi</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase text-right bg-emerald-50/30 whitespace-nowrap">DPLK Kemantran</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase text-right bg-emerald-50/30 whitespace-nowrap">Pinj. BPD Jtg</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-emerald-600 uppercase text-right bg-emerald-100/50 whitespace-nowrap">Jml BPD</th>

                                <!-- Sektoral & Lainnya -->
                                <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase text-right bg-amber-50/30 whitespace-nowrap border-l border-gray-200">Bank TGR</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase text-right bg-amber-50/30 whitespace-nowrap">BPJS Angg+</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase text-right bg-amber-50/30 whitespace-nowrap">Dansos</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase text-right bg-amber-50/30 whitespace-nowrap">Lainnya 1</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase text-right bg-amber-50/30 whitespace-nowrap">Lainnya 2</th>
                                <th class="px-3 py-2 text-[10px] font-bold text-slate-500 uppercase text-right bg-amber-50/30 whitespace-nowrap">Denda Finger</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-if="form.deductions.length === 0">
                                <td colspan="17" class="px-6 py-20 text-center text-gray-500 font-bold italic">Belum ada data gaji untuk bulan ini. Silakan proses penggajian dulu.</td>
                            </tr>
                            <tr v-for="(row, index) in form.deductions" :key="row.salary_id" class="hover:bg-slate-50 transition-colors group">
                                <td class="px-4 py-3 text-center font-bold text-slate-500 text-xs border-r border-gray-100 bg-white group-hover:bg-slate-50 sticky left-0 z-10 whitespace-nowrap">{{ index + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-slate-600 text-xs border-r border-gray-100 bg-white group-hover:bg-slate-50 sticky left-12 z-10 whitespace-nowrap">{{ row.teacher_nipy }}</td>
                                <td class="px-4 py-3 font-bold text-slate-800 text-xs border-r border-gray-100 bg-white group-hover:bg-slate-50 sticky left-44 z-10 whitespace-nowrap">{{ row.teacher_name }}</td>
                                <td class="px-4 py-3 font-medium text-slate-600 text-[10px] border-r border-gray-100 bg-white group-hover:bg-slate-50 sticky left-92 z-10 whitespace-nowrap max-w-[12rem] truncate">{{ row.teacher_position }}</td>
                                <td class="px-4 py-3 text-right font-medium text-slate-600 text-xs border-r border-gray-200 bg-slate-50/50 whitespace-nowrap">{{ formatCurrency(row.spj_netto) }}</td>
                                
                                <!-- Koperasi Inputs -->
                                <td class="px-2 py-2 border-l border-gray-200 bg-indigo-50/10"><CurrencyInput prefix="Rp" disabled v-model="row.tab_khusus" class="w-24 text-right text-xs bg-gray-100 border-transparent rounded focus:ring-0 text-gray-500 cursor-not-allowed py-1.5" /></td>
                                <td class="px-2 py-2 bg-indigo-50/10"><CurrencyInput prefix="Rp" v-model="row.simpanan_wajib" class="w-24 text-right text-xs border-gray-200 rounded focus:ring-indigo-500 focus:border-indigo-500 py-1.5" /></td>
                                <td class="px-2 py-2 bg-indigo-50/10"><CurrencyInput prefix="Rp" v-model="row.simpanan_sukarela" class="w-24 text-right text-xs border-gray-200 rounded focus:ring-indigo-500 focus:border-indigo-500 py-1.5" /></td>
                                <td class="px-2 py-2 bg-indigo-50/10"><CurrencyInput prefix="Rp" v-model="row.angsuran_koperasi" class="w-24 text-right text-xs border-gray-200 rounded focus:ring-indigo-500 focus:border-indigo-500 py-1.5" /></td>
                                <td class="px-4 py-3 text-right font-bold text-indigo-700 text-xs bg-indigo-50/50 whitespace-nowrap">{{ formatCurrency(row.jumlah_koperasi) }}</td>

                                <!-- BPD Inputs -->
                                <td class="px-2 py-2 border-l border-gray-200 bg-emerald-50/10"><CurrencyInput prefix="Rp" v-model="row.dplk_slawi" class="w-24 text-right text-xs border-gray-200 rounded focus:ring-emerald-500 focus:border-emerald-500 py-1.5" /></td>
                                <td class="px-2 py-2 bg-emerald-50/10"><CurrencyInput prefix="Rp" v-model="row.dplk_kemantran" class="w-24 text-right text-xs border-gray-200 rounded focus:ring-emerald-500 focus:border-emerald-500 py-1.5" /></td>
                                <td class="px-2 py-2 bg-emerald-50/10"><CurrencyInput prefix="Rp" v-model="row.pinjaman_bpd_jateng" class="w-24 text-right text-xs border-gray-200 rounded focus:ring-emerald-500 focus:border-emerald-500 py-1.5" /></td>
                                <td class="px-4 py-3 text-right font-bold text-emerald-700 text-xs bg-emerald-50/50 whitespace-nowrap">{{ formatCurrency(row.jumlah_bpd) }}</td>

                                <!-- Sektoral Inputs -->
                                <td class="px-2 py-2 border-l border-gray-200 bg-amber-50/10"><CurrencyInput prefix="Rp" v-model="row.bank_tgr" class="w-24 text-right text-xs border-gray-200 rounded focus:ring-amber-500 focus:border-amber-500 py-1.5" /></td>
                                <td class="px-2 py-2 bg-amber-50/10"><CurrencyInput prefix="Rp" v-model="row.premi_bpjs_anggota" class="w-24 text-right text-xs border-gray-200 rounded focus:ring-amber-500 focus:border-amber-500 py-1.5" /></td>
                                <td class="px-2 py-2 bg-amber-50/10"><CurrencyInput prefix="Rp" v-model="row.dansos" class="w-24 text-right text-xs border-gray-200 rounded focus:ring-amber-500 focus:border-amber-500 py-1.5" /></td>
                                <td class="px-2 py-2 bg-amber-50/10"><CurrencyInput prefix="Rp" v-model="row.lainnya_1" class="w-24 text-right text-xs border-gray-200 rounded focus:ring-amber-500 focus:border-amber-500 py-1.5" /></td>
                                <td class="px-2 py-2 bg-amber-50/10"><CurrencyInput prefix="Rp" v-model="row.lainnya_2" class="w-24 text-right text-xs border-gray-200 rounded focus:ring-amber-500 focus:border-amber-500 py-1.5" /></td>
                                <td class="px-2 py-2 bg-amber-50/10"><CurrencyInput prefix="Rp" v-model="row.denda_fingerprint" class="w-24 text-right text-xs border-gray-200 rounded focus:ring-amber-500 focus:border-amber-500 py-1.5" /></td>

                                <!-- Final -->
                                <td class="px-4 py-3 text-right font-black text-rose-600 text-xs border-l border-gray-200 bg-rose-50/50 whitespace-nowrap">{{ formatCurrency(row.jumlah_potongan) }}</td>
                                <td class="px-4 py-3 text-right font-black text-emerald-600 text-sm border-l border-gray-200 bg-emerald-100/50 whitespace-nowrap">{{ formatCurrency(row.gaji_bersih) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="flex justify-end mt-4">
                <button @click="submitBulk" :disabled="form.processing" class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-bold flex items-center space-x-2 hover:bg-indigo-700 shadow-lg shadow-indigo-600/30 transition-all">
                    <span>Simpan Perubahan Potongan</span>
                </button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
</style>
