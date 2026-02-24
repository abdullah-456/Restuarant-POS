import Swal from 'sweetalert2';

window.Alert = {
    success: (message, timer = 2000) => {
        return Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: message,
            timer: timer,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    },
    error: (message) => {
        return Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            confirmButtonColor: '#3b82f6'
        });
    },
    confirm: (title, message, icon = 'warning') => {
        return Swal.fire({
            title: title,
            text: message,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, proceed',
            cancelButtonText: 'Cancel'
        });
    },
    toast: (message, icon = 'success') => {
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
        return Toast.fire({
            icon: icon,
            title: message
        });
    }
};
