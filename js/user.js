// js/users.js

document.addEventListener('DOMContentLoaded', () => {
    loadUsers();
});

// 1. READ (Ambil Data)
async function loadUsers() {
    try {
        const res = await fetch('api/api_user.php');
        const json = await res.json();
        const tbody = document.getElementById('userTableBody');
        tbody.innerHTML = '';

        if (json.status === 'success') {
            json.data.forEach((user, index) => {
                // Tentukan Badge Warna
                const badgeClass = user.role === 'admin' ? 'badge-role-admin' : 'badge-role-cashier';
                
                // Proteksi Tombol Hapus: Jika itu diri sendiri, disable tombol hapus
                const isSelf = user.id == CURRENT_LOGIN_ID;
                const deleteBtnState = isSelf ? 'disabled title="Tidak bisa hapus diri sendiri"' : '';

                const tr = `
                    <tr>
                        <td>${index + 1}</td>
                        <td class="fw-bold">${user.username} ${isSelf ? '(Anda)' : ''}</td>
                        <td><span class="badge ${badgeClass}">${user.role.toUpperCase()}</span></td>
                        <td>${user.created_at}</td>
                        <td>
                            <button class="btn btn-sm btn-warning text-white me-1" onclick='openModal("edit", ${JSON.stringify(user)})'>
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})" ${deleteBtnState}>
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                            <button class="btn btn-sm btn-dark" onclick="alert('Fitur Reset Password akan dikembangkan nanti')" title="Reset Password">
                                <i class="bi bi-key-fill"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += tr;
            });
        }
    } catch (err) {
        console.error(err);
    }
}

// 2. MODAL CONTROL (Add vs Edit)
const userModal = new bootstrap.Modal(document.getElementById('userModal'));

function openModal(mode, data = null) {
    const title = document.getElementById('modalTitle');
    const idField = document.getElementById('userId');
    const nameField = document.getElementById('uName');
    const roleField = document.getElementById('uRole');
    const passField = document.getElementById('uPass');
    const hint = document.getElementById('editPassHint');

    if (mode === 'add') {
        // Mode Tambah: Bersihkan Form
        title.innerText = "Tambah User Baru";
        idField.value = '';
        nameField.value = '';
        roleField.value = 'cashier';
        passField.value = '';
        passField.required = true; // Password wajib kalau tambah baru
        hint.classList.add('d-none'); // Sembunyikan hint
    } else {
        // Mode Edit: Isi Form dengan data user
        title.innerText = "Edit User: " + data.username;
        idField.value = data.id;
        nameField.value = data.username;
        roleField.value = data.role;
        passField.value = ''; // Kosongkan password (biar aman)
        passField.required = false; // Password opsional kalau edit
        hint.classList.remove('d-none'); // Munculkan hint
    }
    
    userModal.show();
}

// 3. CREATE & UPDATE (Simpan)
async function saveUser() {
    const id = document.getElementById('userId').value;
    const username = document.getElementById('uName').value;
    const role = document.getElementById('uRole').value;
    const password = document.getElementById('uPass').value;

    if (!username) { alert("Username wajib diisi!"); return; }
    
    // Logika method: Jika ada ID berarti PUT (Edit), jika tidak ada berarti POST (Add)
    const method = id ? 'PUT' : 'POST';
    
    // Jika Mode Add (POST), Password wajib
    if (method === 'POST' && !password) {
        alert("Password wajib diisi untuk user baru!");
        return;
    }

    const payload = { id, username, role, password };

    try {
        const res = await fetch('api/api_user.php', {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await res.json();

        if (result.status === 'success') {
            alert(result.message);
            userModal.hide();
            loadUsers();
        } else {
            alert(result.message);
        }
    } catch (err) {
        console.error(err);
        alert("Error sistem.");
    }
}

// 4. DELETE
async function deleteUser(id) {
    if (confirm("Yakin ingin menghapus user ini secara permanen?")) {
        try {
            const res = await fetch('api/api_user.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            const result = await res.json();
            
            if (result.status === 'success') {
                loadUsers(); // Refresh tabel
            } else {
                alert(result.message);
            }
        } catch (err) {
            alert("Gagal menghapus.");
        }
    }
}