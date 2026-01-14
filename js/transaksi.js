// File: js/transaksi.js

let allProducts = [];
let cart = [];

// Kita ambil data user dari variabel global yang didefinisikan di PHP
const currentCashier = GLOBAL_USER.username;
const currentUserId  = GLOBAL_USER.id;

// 1. Ambil Data Produk saat Load
document.addEventListener('DOMContentLoaded', () => {
    fetchProducts();
    const searchInput = document.getElementById('searchInput');
    if(searchInput) searchInput.focus();
});

async function fetchProducts() {
    try {
        const res = await fetch('api/api_produk.php');
        const json = await res.json();
        if(json.status === 'success') {
            allProducts = json.data;
        }
    } catch (err) { console.error(err); }
}

// 2. Logic Pencarian
document.getElementById('searchInput').addEventListener('keyup', function(e) {
    const keyword = this.value.toLowerCase();
    const resultBox = document.getElementById('searchResultBox');
    
    // Jika input kosong, sembunyikan kotak hasil
    if(keyword.length < 1) {
        resultBox.style.display = 'none';
        return;
    }

    // Filter produk berdasarkan nama
    const filtered = allProducts.filter(p => p.name.toLowerCase().includes(keyword));
    const statusBox = document.getElementById('statusSearch');

    if(filtered.length > 0) {
        // Tampilkan jumlah barang ditemukan di kanan atas
        statusBox.innerHTML = `<span class="text-success fw-bold">${filtered.length} Barang Ditemukan</span>`;
        
        let html = '';
        filtered.forEach(p => {
            // PERHATIKAN BAGIAN INI:
            // Kita menambahkan onclick="addToCart(...)" agar bisa diklik
            html += `
                <div class="search-item" onclick="addToCart(${p.id})">
                    <div class="fw-bold">${p.name}</div>
                    <small class="text-muted">Rp ${parseInt(p.selling_price).toLocaleString('id-ID')} | Stok: ${p.stock}</small>
                </div>
            `;
        });
        
        resultBox.innerHTML = html;
        resultBox.style.display = 'block';
    } else {
        statusBox.innerHTML = `<span class="text-danger">Barang tidak ditemukan</span>`;
        resultBox.style.display = 'none';
    }

    // Fitur tambahan: Tekan Enter jika hanya ada 1 hasil
    if(e.key === 'Enter' && filtered.length === 1) {
        addToCart(filtered[0].id);
        this.value = '';
        resultBox.style.display = 'none';
    }
});

// 3. Tambah ke Keranjang
function addToCart(id) {
    console.log("Mencoba tambah ID:", id);
    
    const product = allProducts.find(p => p.id == id);
    
    // cek produk jika ada
    if (!product) {
        console.error("Produk tidak ditemukan di data lokal untuk ID:", id);
        return;
    }

    // Cek Stok
    if(product.stock <= 0) {
        alert("Stok Habis!");
        return;
    }

    const existing = cart.find(item => item.id == id);
    if(existing) {
        if(existing.qty < product.stock) {
            existing.qty++;
        } else {
            alert("Stok Maksimal Tercapai");
        }
    } else {
        cart.push({...product, qty: 1});
    }

    document.getElementById('searchInput').value = '';
    document.getElementById('searchResultBox').style.display = 'none';
    document.getElementById('searchInput').focus(); 
    
    renderTable();
}

// 4. Render Tabel
function renderTable() {
    const tbody = document.getElementById('cartTableBody');
    
    if(cart.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Keranjang Kosong</td></tr>`;
        updateTotals(0);
        return;
    }

    let html = '';
    let totalAll = 0;

    cart.forEach((item, index) => {
        const subtotal = item.selling_price * item.qty;
        totalAll += subtotal;

        html += `
            <tr>
                <td>${index + 1}</td>
                <td>${item.name}</td>
                <td>
                    <div class="input-group input-group-sm" style="width: 100px;">
                        <button class="btn btn-outline-secondary" onclick="updateQty(${index}, -1)">-</button>
                        <input type="text" class="form-control text-center" value="${item.qty}" readonly>
                        <button class="btn btn-outline-secondary" onclick="updateQty(${index}, 1)">+</button>
                    </div>
                </td>
                <td>Rp ${subtotal.toLocaleString('id-ID')}</td>
                <td>${currentCashier}</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="removeItem(${index})"><i class="bi bi-x-lg"></i></button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
    updateTotals(totalAll);
}

// 5. Update Qty & Hapus
function updateQty(index, delta) {
    const item = cart[index];
    const newQty = item.qty + delta;

    if(newQty > 0) {
        if(newQty <= item.stock) {
            item.qty = newQty;
        } else {
            alert("Stok tidak cukup!");
        }
    }
    renderTable();
}

function removeItem(index) {
    cart.splice(index, 1);
    renderTable();
}

function clearCart() {
    cart = [];
    renderTable();
    document.getElementById('payInput').value = '';
    document.getElementById('changeDisplay').value = '0';
}

// 6. Hitung Total
function updateTotals(total) {
    document.getElementById('grandTotalDisplay').value = total.toLocaleString('id-ID');
    document.getElementById('grandTotalValue').value = total;
    calculateChange();
}

function calculateChange() {
    const total = parseInt(document.getElementById('grandTotalValue').value) || 0;
    const pay = parseInt(document.getElementById('payInput').value) || 0;
    const change = pay - total;

    if(pay > 0) {
        document.getElementById('changeDisplay').value = change.toLocaleString('id-ID');
    } else {
        document.getElementById('changeDisplay').value = '0';
    }
}

// 7. Proses Bayar
async function processPayment() {
    if(cart.length === 0) {
        alert("Keranjang kosong!");
        return;
    }

    const total = parseInt(document.getElementById('grandTotalValue').value);
    const pay = parseInt(document.getElementById('payInput').value) || 0;

    if(pay < total) {
        alert("Uang pembayaran kurang!");
        return;
    }

    // AMBIL METODE PEMBAYARAN
    const paymentMethod = document.getElementById('paymentMethod').value;

    const payload = {
        user_id: currentUserId,
        pembayaran: paymentMethod, // <--- TAMBAHAN BARU
        amount_paid: pay,
        items: cart.map(i => ({ product_id: i.id, quantity: i.qty }))
    };

    try {
        const res = await fetch('api/api_sales.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        const result = await res.json();

        if(result.status === 'success') {
            alert("Transaksi Berhasil! Kembalian: Rp " + result.data.change.toLocaleString('id-ID'));
            clearCart();
            fetchProducts();
        } else {
            alert("Gagal: " + result.message);
        }
    } catch (err) {
        console.error(err);
        alert("Error koneksi sistem.");
    }
}

// Fitur Tambahan: Auto-fill bayar jika QRIS/Transfer
    function handlePaymentMethodChange() {
        const method = document.getElementById('paymentMethod').value;
        const total = document.getElementById('grandTotalValue').value;
        const payInput = document.getElementById('payInput');

        if (method === 'qris' || method === 'transfer') {
            // Jika Non-Tunai, anggap bayarnya pas (sesuai total)
            payInput.value = total;
            payInput.readOnly = true; // Kunci input biar ga diubah manual
        } else {
            // Jika Cash, kosongkan dan biarkan kasir ketik
            payInput.value = '';
            payInput.readOnly = false;
        }
        calculateChange(); // Hitung ulang kembalian
    }