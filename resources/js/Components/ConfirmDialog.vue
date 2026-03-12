<script setup lang="ts">
import { ref } from 'vue';

const isOpen = ref(false);
const title = ref('');
const message = ref('');
const confirmText = ref('');
const cancelText = ref('');
let resolveCallback: ((value: boolean) => void) | null = null;

const confirm = (options: {
    title?: string;
    message: string;
    confirmText?: string;
    cancelText?: string;
}) => {
    title.value = options.title || 'Konfirmasi';
    message.value = options.message;
    confirmText.value = options.confirmText || 'Ya, Lanjutkan';
    cancelText.value = options.cancelText || 'Batal';
    isOpen.value = true;

    return new Promise<boolean>((resolve) => {
        resolveCallback = resolve;
    });
};

const handleConfirm = () => {
    isOpen.value = false;
    if (resolveCallback) {
        resolveCallback(true);
        resolveCallback = null;
    }
};

const handleCancel = () => {
    isOpen.value = false;
    if (resolveCallback) {
        resolveCallback(false);
        resolveCallback = null;
    }
};

defineExpose({ confirm });
</script>

<template>
    <Transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="isOpen" class="fixed inset-0 z-[110] overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-indigo-950/40 backdrop-blur-md transition-opacity" @click="handleCancel"></div>

                <!-- Modal -->
                <Transition
                    enter-active-class="ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white dark:bg-gray-800 p-8 text-left shadow-[0_20px_50px_rgba(0,0,0,0.3)] transition-all sm:my-8 sm:w-full sm:max-w-lg border border-white/20">
                        <!-- Batik Decoration -->
                        <div class="absolute -right-10 -top-10 w-40 h-40 opacity-5 pointer-events-none">
                            <svg viewBox="0 0 100 100" class="w-full h-full text-indigo-900 dark:text-white fill-current">
                                <circle cx="50" cy="50" r="45" stroke="currentColor" stroke-width="2" fill="none" />
                                <path d="M50 5 L50 95 M5 50 L95 50" stroke="currentColor" stroke-width="1" />
                            </svg>
                        </div>

                        <div class="relative z-10 sm:flex sm:items-start">
                            <div class="mx-auto flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 sm:mx-0">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12V15.75z" />
                                </svg>
                            </div>
                            <div class="mt-4 text-center sm:ml-6 sm:mt-0 sm:text-left">
                                <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ title }}</h3>
                                <div class="mt-3">
                                    <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 leading-relaxed">{{ message }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-8 flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3">
                            <button
                                type="button"
                                @click="handleCancel"
                                class="mt-3 inline-flex w-full justify-center rounded-2xl bg-gray-100 dark:bg-gray-700 px-6 py-3.5 text-sm font-black text-gray-600 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-all sm:mt-0 sm:w-auto"
                            >
                                {{ cancelText }}
                            </button>
                            <button
                                type="button"
                                @click="handleConfirm"
                                class="inline-flex w-full justify-center rounded-2xl bg-rose-600 px-6 py-3.5 text-sm font-black text-white uppercase tracking-widest shadow-xl shadow-rose-200 dark:shadow-none hover:bg-rose-700 hover:scale-105 active:scale-95 transition-all sm:w-auto"
                            >
                                {{ confirmText }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </Transition>
</template>
