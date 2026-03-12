import Swal from 'sweetalert2';

export const confirmDelete = (title: string, message: string) => {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48', // rose-600
        cancelButtonColor: '#64748b',  // slate-500
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        background: '#ffffff',
        customClass: {
            popup: 'rounded-[2rem] border-none shadow-2xl font-sans',
            title: 'text-2xl font-black text-gray-900 uppercase tracking-tight',
            confirmButton: 'rounded-2xl px-8 py-3.5 font-bold uppercase tracking-widest transition-all',
            cancelButton: 'rounded-2xl px-8 py-3.5 font-bold uppercase tracking-widest transition-all',
        }
    });
};

export const toastSuccess = (message: string) => {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: 'success',
        title: message
    });
};
