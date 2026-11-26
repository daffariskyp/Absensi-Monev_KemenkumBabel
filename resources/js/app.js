import Swal from 'sweetalert2';
import './bootstrap';
import '../css/app.css';

// Jadikan Swal global agar dapat diakses dari view Blade manapun
window.Swal = Swal;

// Visual feedback saat tombol diklik: efek shadow tactile (warna asli tombol tetap dipertahankan)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('button, a.btn, [role="button"], .tab-btn');
    if (btn && !btn.hasAttribute('disabled')) {
        btn.classList.add('btn-clicked');
        setTimeout(() => {
            btn.classList.remove('btn-clicked');
        }, 200);
    }
});

// Modal Logout start
document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.getElementById('logout-btn');

    logoutBtn.addEventListener('click', function(e) {
        e.preventDefault(); // Mencegah aksi default tombol

        // Menampilkan SweetAlert2 untuk konfirmasi logout
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirm Logout'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form logout jika dikonfirmasi
                document.getElementById('logout-form').submit();
            }
        });
    });
});
// Modal Logout end

// Modal delete user start
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete button click
    document.querySelectorAll('.delete-btn-user').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            const employeeId = this.getAttribute('data-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteUser(employeeId);
                }
            });
        });
    });

    // Function to handle delete request
    function deleteUser(employeeId) {
        fetch(`/admin/${employeeId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Deleted!',
                        'The employee has been deleted.',
                        'success'
                    ).then(() => {
                        location.reload(); // Reload the page
                    });
                } else {
                    Swal.fire(
                        'Error!',
                        'Something went wrong. Please try again.',
                        'error'
                    );
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire(
                    'Error!',
                    'Failed to delete the employee.',
                    'error'
                );
            });
    }
});
// Modal delete user start

// Fungsi untuk menampilkan SweetAlert
function showAlert(type, title, message) {
    Swal.fire({
        icon: type, // success, error, warning, info, question
        title: title,
        text: message,
        confirmButtonText: 'OK',
    });
}

// Fungsi global SweetAlert untuk Notifikasi Pengajuan Cuti Baru bagi Admin
window.notifyPendingLeaves = function(count, latestName, latestCategory) {
    if (!count || parseInt(count) <= 0) return;

    Swal.fire({
        icon: 'warning',
        title: '<span style="color:#1e3a8a; font-weight:800;">Pengajuan Cuti Menunggu Persetujuan!</span>',
        html: `
            <div style="text-align: left; font-size: 13px; color: #374151; line-height: 1.6;">
                <p style="margin-bottom: 8px;">Terdapat <b style="color: #2563eb; font-size: 16px;">${count}</b> pengajuan cuti/izin pegawai baru yang membutuhkan persetujuan Anda.</p>
                ${latestName ? `
                    <div style="background: #eff6ff; padding: 10px 14px; border-radius: 12px; border: 1px solid #bfdbfe; margin-top: 10px;">
                        <div style="font-weight: 700; color: #1e40af; font-size: 12px; text-transform: uppercase;">Pengajuan Terbaru:</div>
                        <div style="color: #1f2937; margin-top: 2px;"><b>${latestName}</b> - <span style="color:#4338ca; font-weight:600;">${latestCategory || 'Izin Pegawai'}</span></div>
                    </div>
                ` : ''}
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Tinjau Pengajuan Sekarang',
        cancelButtonText: 'Nanti Saja',
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#64748b',
        customClass: {
            popup: 'rounded-2xl shadow-2xl border border-blue-100',
            confirmButton: 'px-5 py-2.5 rounded-xl font-bold border-2 border-yellow-400 shadow-md text-white transition active:scale-95',
            cancelButton: 'px-4 py-2.5 rounded-xl font-semibold transition active:scale-95'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/administration?status=pending';
        }
    });
};

// Event DOMContentLoaded untuk memastikan elemen HTML sudah siap
document.addEventListener('DOMContentLoaded', () => {
    if (window.flashMessage) {
        showAlert(window.flashMessage.type, window.flashMessage.title, window.flashMessage.message);
    }
});
