<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import { debounce } from 'lodash';

const props = defineProps<{
    salaries: any;
    filters?: any;
    error?: string;
}>();

const month = ref(props.filters?.month || '');
const year = ref(props.filters?.year || '');

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

watch([month, year], debounce(() => {
    router.get(route('my-salaries.index'), {
        month: month.value,
        year: year.value,
    }, { preserveState: true, replace: true });
}, 300));

const formatRupiah = (value: number) => {
    if (value === undefined || value === null || value === 0) return 'Rp -';
    return 'Rp ' + Number(value).toLocaleString('id-ID', { maximumFractionDigits: 0 });
};

const printSalary = (id: number) => {
    window.open(route('salaries.print', id), '_blank');
};
</script>

<template>
    <Head title="Slip Gaji Saya" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Slip Gaji Saya</h2>
                    <p class="text-sm text-gray-500 font-medium mt-1">Daftar slip gaji Anda yang telah dipublikasikan.</p>
                </div>
            </div>
        </template>

        <div v-if="error" class="bg-red-50 text-red-600 p-6 rounded-3xl border border-red-200 mb-6 font-bold">
            {{ error }}
        </div>

        <div v-else class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
            <!-- Filter -->
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/10 flex flex-col sm:flex-row items-center gap-4">
                <div class="relative w-full max-w-xs">
                    <span class="text-xs font-bold text-gray-500 mb-1 block">Filter Periode</span>
                    <input 
                        v-model="filterPeriod" 
                        type="month" 
                        class="block w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"
                    />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#003B73] border-b border-indigo-800">
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center w-12">No</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center">Periode</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center">Tgl Gajian</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Gaji Bersih</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-center">Status Pembayaran</th>
                            <th class="px-6 py-4 text-xs font-black text-white uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                        <tr v-if="salaries && salaries.data" v-for="(sal, index) in salaries.data" :key="sal.id" class="hover:bg-gray-50 transition-all duration-200">
                            <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-gray-300 w-12">
                                {{ (salaries.current_page - 1) * salaries.per_page + index + 1 }}
                            </td>
                            <td class="px-6 py-4 text-center font-black text-gray-900">{{ sal.month }}/{{ sal.year }}</td>
                            <td class="px-6 py-4 text-center font-bold text-gray-500 text-xs">
                                {{ sal.payment_date ? new Date(sal.payment_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-black text-indigo-600">{{ formatRupiah(sal.final_net_salary) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest" :class="sal.status === 'paid' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600'">
                                    {{ sal.status === 'paid' ? 'Dibayarkan' : 'Menunggu' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <button @click="printSalary(sal.id)" class="px-4 py-2 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl text-xs font-black uppercase transition-all inline-flex items-center space-x-2" title="Cetak Slip">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    <span>Cetak Slip</span>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!salaries || !salaries.data || salaries.data.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 font-medium">Belum ada slip gaji yang dipublikasikan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div v-if="salaries && salaries.links && salaries.links.length > 3" class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <div class="text-sm text-gray-500 font-medium">
                    Menampilkan <span class="font-black text-gray-900">{{ salaries.from || 0 }}</span> s.d <span class="font-black text-gray-900">{{ salaries.to || 0 }}</span> dari <span class="font-black text-gray-900">{{ salaries.total }}</span> slip
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
    </AuthenticatedLayout>
</template>
