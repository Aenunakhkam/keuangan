<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const visible = ref(false);
const message = ref('');
const type = ref<'success' | 'error' | 'info'>('success');
const timer = ref<any>(null);

const show = (msg: string, t: 'success' | 'error' | 'info' = 'success') => {
    if (timer.value) clearTimeout(timer.value);
    
    message.value = msg;
    type.value = t;
    visible.value = true;
    
    timer.value = setTimeout(() => {
        visible.value = false;
    }, 5000);
};

// Watch for flash messages from Inertia
watch(() => page.props.flash, (flash: any) => {
    if (flash?.success) {
        show(flash.success, 'success');
    }
    if (flash?.error) {
        show(flash.error, 'error');
    }
}, { deep: true, immediate: true });

onMounted(() => {
    // Initial check
    const flash = page.props.flash as any;
    if (flash?.success) show(flash.success, 'success');
    if (flash?.error) show(flash.error, 'error');
});

defineExpose({ show });
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="visible" class="fixed top-5 right-5 z-[100] w-full max-w-sm overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-2xl border border-gray-100 dark:border-gray-700 pointer-events-auto">
            <!-- Batik Accent Decor -->
            <div class="absolute top-0 right-0 w-16 h-16 opacity-5 pointer-events-none overflow-hidden">
                <svg viewBox="0 0 100 100" class="w-full h-full text-indigo-900 dark:text-white fill-current">
                    <path d="M50 0 L100 50 L50 100 L0 50 Z" />
                </svg>
            </div>

            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <!-- Success Icon -->
                        <div v-if="type === 'success'" class="h-10 w-10 bg-emerald-100 dark:bg-emerald-950 rounded-xl flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </div>
                        <!-- Error Icon -->
                        <div v-else-if="type === 'error'" class="h-10 w-10 bg-rose-100 dark:bg-rose-950 rounded-xl flex items-center justify-center text-rose-600 dark:text-rose-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <!-- Info Icon -->
                        <div v-else class="h-10 w-10 bg-indigo-100 dark:bg-indigo-950 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">
                            {{ type === 'success' ? 'Berhasil' : (type === 'error' ? 'Peringatan' : 'Informasi') }}
                        </p>
                        <p class="mt-1 text-sm font-semibold text-gray-600 dark:text-gray-400 leading-relaxed">
                            {{ message }}
                        </p>
                    </div>
                    <div class="ml-4 flex flex-shrink-0">
                        <button @click="visible = false" class="inline-flex rounded-lg bg-gray-50 dark:bg-gray-700 p-1 text-gray-400 hover:text-gray-500 transition-colors">
                            <span class="sr-only">Close</span>
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Progress Bar -->
            <div class="h-1 bg-gray-100 dark:bg-gray-700 w-full overflow-hidden">
                <div 
                    class="h-full transition-all duration-75 ease-linear"
                    :class="{
                        'bg-emerald-500': type === 'success',
                        'bg-rose-500': type === 'error',
                        'bg-indigo-500': type === 'info'
                    }"
                    :style="{ width: '100%' }"
                ></div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
@keyframes shrink {
    from { width: 100%; }
    to { width: 0%; }
}
.bg-progress {
    animation: shrink 5s linear forwards;
}
</style>
