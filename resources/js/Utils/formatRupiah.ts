export const formatRupiah = (value: number | string | null | undefined): string => {
    if (value === null || value === undefined || value === '') return 'Rp 0';
    const num = Number(value);
    if (isNaN(num)) return 'Rp 0';
    
    // Explicitly format with dots as thousand separator to avoid browser locale inconsistencies
    const formatted = num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return `Rp ${formatted}`;
};

export const formatNumber = (value: number | string | null | undefined): string => {
    if (value === null || value === undefined || value === '') return '0';
    const num = Number(value);
    if (isNaN(num)) return '0';
    
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};
