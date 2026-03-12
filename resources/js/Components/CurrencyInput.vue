<script setup lang="ts">
/**
 * CurrencyInput.vue
 * 
 * A smart input component that:
 * - Displays numbers in Indonesian format (e.g., 1.065.000) while the user types
 * - Emits clean numeric values to the parent form (e.g., 1065000)
 * - Stores the raw number in v-model (modelValue) as a number type
 */
import { ref, watch, onMounted } from 'vue';

const props = defineProps<{
    modelValue: number | string | null;
    placeholder?: string;
    id?: string;
    disabled?: boolean;
    class?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: number): void;
}>();

// Format a raw number into Indonesian style: 1065000 → "1.065.000"
function formatDisplay(value: number | string | null): string {
    if (value === null || value === undefined || value === '') return '';
    const num = Number(String(value).replace(/\./g, '').replace(',', '.'));
    if (isNaN(num)) return '';
    return num.toLocaleString('id-ID');
}

const displayValue = ref<string>(formatDisplay(props.modelValue));

// When external modelValue changes (e.g. from seeding or reset), update display
watch(() => props.modelValue, (newVal) => {
    // Only update display if the raw numeric value genuinely changed
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
    // Strip all non-digit characters
    const raw = input.value.replace(/[^\d]/g, '');
    const numericValue = raw === '' ? 0 : parseInt(raw, 10);

    // Update display with formatted string
    displayValue.value = numericValue === 0 && raw === '' ? '' : numericValue.toLocaleString('id-ID');

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
    <input
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
