<script setup lang="ts">
import { formatRupiah } from '@/Utils/formatRupiah';
/**
 * CurrencyInput.vue
 * 
 * A smart input component that:
 * - Displays numbers in Indonesian format (e.g., 1.065.000) while the user types
 * - Emits clean numeric values to the parent form (e.g., 1065000)
 * - Stores the raw number in v-model (modelValue) as a number type
 * - Supports negative numbers and optional text prefix (e.g., "Rp").
 */
import { ref, watch, onMounted } from 'vue';

const props = defineProps<{
    modelValue: number | string | null;
    placeholder?: string;
    id?: string;
    disabled?: boolean;
    class?: string;
    prefix?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: number): void;
}>();

// Format a raw number into Indonesian style: 1065000 → "1.065.000"
function formatDisplay(value: number | string | null): string {
    if (value === null || value === undefined || value === '') return '';
    // modelValue is always a raw number or SQL string (e.g., "5000.00").
    // We do not need to strip Indonesian formatting here.
    const num = Number(value);
    if (isNaN(num)) return '';
    return formatRupiah(num);
}

const displayValue = ref<string>(formatDisplay(props.modelValue));

// When external modelValue changes (e.g. from seeding or reset), update display
watch(() => props.modelValue, (newVal) => {
    const currentRaw = Number(String(displayValue.value).replace(/\./g, ''));
    const newRaw = Number(newVal);
    if (currentRaw !== newRaw) {
        displayValue.value = formatDisplay(newVal);
    }
});

onMounted(() => {
    displayValue.value = formatDisplay(props.modelValue);
});

function onInput(event: Event) {
    const input = event.target as HTMLInputElement;
    const isNegative = input.value.startsWith('-');
    
    // Strip all non-digit characters
    const raw = input.value.replace(/[^\d]/g, '');
    let numericValue = raw === '' ? 0 : parseInt(raw, 10);
    
    if (isNegative && numericValue !== 0) {
        numericValue = -numericValue;
    }

    // Update display with formatted string
    if (numericValue === 0 && raw === '') {
        displayValue.value = '';
    } else {
        displayValue.value = formatRupiah(numericValue);
    }

    // Set cursor to end after formatting to avoid jumps
    setTimeout(() => {
        input.value = displayValue.value;
        input.setSelectionRange(displayValue.value.length, displayValue.value.length);
    }, 0);

    // Emit clean number to parent
    emit('update:modelValue', numericValue);
}
</script>

<template>
    <div v-if="prefix" class="relative w-full">
        <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
            <span class="text-gray-400 font-medium text-[10px]">{{ prefix }}</span>
        </div>
        <input
            :id="id"
            type="text"
            inputmode="numeric"
            :value="displayValue"
            :placeholder="placeholder || '0'"
            :disabled="disabled"
            :class="['pl-6 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm', props.class || '']"
            @input="onInput"
        />
    </div>
    <input
        v-else
        :id="id"
        type="text"
        inputmode="numeric"
        :value="displayValue"
        :placeholder="placeholder || '0'"
        :disabled="disabled"
        :class="['border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm', props.class || '']"
        @input="onInput"
    />
</template>
