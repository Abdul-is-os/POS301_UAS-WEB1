document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault(); // Mencegah halaman refresh sendiri

    const usernameIn = document.getElementById('username').value;
    const passwordIn = document.getElementById('password').value;
    const errorAlert = document.getElementById('error-alert');

    // Sembunyikan error lama
    errorAlert.classList.add('d-none');
    errorAlert.innerText = ""; // Bersihkan teks

    try {
        // MENGIRIM DATA KE BACKEND (api_login.php)
        const response = await fetch('api/api_login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: usernameIn,
                password: passwordIn
            })
        });

        const result = await response.json();

        if (result.status === 'success') {
            // Jika Database bilang OK, kita pindah ke index.php
            window.location.href = 'index.php';
        } else {
            // Jika Salah Password / User tidak ada
            errorAlert.textContent = result.message;
            errorAlert.classList.remove('d-none');
        }

    } catch (error) {
        console.error('Error:', error);
        errorAlert.textContent = "Gagal terhubung ke server.";
        errorAlert.classList.remove('d-none');
    }
});