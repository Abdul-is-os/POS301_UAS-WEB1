<footer class="footer mt-auto py-3 bg-white shadow-sm border-top">
    <div class="container text-center">
        <span class="text-muted">
            &copy; <?php echo date("Y"); ?> 
            <strong>POS System</strong>. 
            Dibuat oleh 
            <a href="https://github.com/Abdul-is-os" target="_blank" class="text-decoration-none text-success fw-bold">
                Abdul-is-os 
            </a> (Abdul menggunakan berbaggai device dan akun berbeda untuk push github)
        </span>
    </div>
</footer>

<style>
    /* Agar footer selalu di bawah jika konten sedikit */
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    
    .main-content {
        flex: 1;
    }
    
    /* Sentuhan hover pada link footer */
    footer a:hover {
        text-decoration: underline !important;
        color: #397c67 !important; /* Warna Hijau Tema Kita */
    }
</style>