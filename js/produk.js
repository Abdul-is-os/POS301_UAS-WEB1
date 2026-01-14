// js/produk.js

let allProducts = [];

document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
    
    // Fitur Search tabel
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        const keyword = e.target.value.toLowerCase();
        const filtered = allProducts.filter(p => p.name.toLowerCase().includes(keyword));
        renderTable(filtered);
    });
});

// 1. GET DATA (READ)
async function loadProducts() {
    try {
        const res = await fetch('api/api_produk.php');
        const json = await res.json();
        
        if (json.status === 'success') {
            allProducts = json.data;
            renderTable(allProducts);
        } else {
            console.error(json.message);
        }
    } catch (error) {
        console.error("Gagal load produk", error);
    }
}

// 2. RENDER TABEL
function renderTable(data) {
    const tbody = document.getElementById('productTableBody');
    tbody.innerHTML = '';

    if(data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center">Data tidak ditemukan</td></tr>';
        return;
    }

    data.forEach((item, index) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${index + 1}</td>
            <td class="fw-bold">${item.name}</td>
            <td>${item.category || '-'}</td>
            <td>
                <span class="badge ${item.stock < 10 ? 'bg-danger' : 'bg-success'}">
                    ${item.stock}
                </span>
            </td>
            <td>Rp ${parseInt(item.cost_price).toLocaleString('id-ID')}</td>
            <td>Rp ${parseInt(item.selling_price).toLocaleString('id-ID')}</td>
            <td>${item.unit}</td>
            <td>
                <button class="btn btn-sm btn-info text-white me-1"><i class="bi bi-eye"></i></button>
                <button class="btn btn-sm btn-warning text-white me-1"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// 3. INSERT DATA (CREATE)
async function saveProduct() {
    // Ambil value dari form modal
    const name = document.getElementById('pName').value;
    const category = document.getElementById('pCategory').value;
    const brand = document.getElementById('pBrand').value;
    const cost = document.getElementById('pCost').value;
    const price = document.getElementById('pPrice').value;
    const stock = document.getElementById('pStock').value;
    const unit = document.getElementById('pUnit').value;

    // Validasi sederhana
    if(!name || !cost || !price) {
        alert("Nama, Harga Beli, dan Harga Jual wajib diisi!");
        return;
    }

    const payload = {
        name: name,
        category: category,
        brand: brand,
        cost_price: parseFloat(cost),
        selling_price: parseFloat(price),
        stock: parseInt(stock),
        unit: unit
    };

    try {
        const res = await fetch('api/api_produk.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        const result = await res.json();

        if(result.status === 'success') {
            alert("Produk berhasil ditambahkan!");
            
            // Tutup Modal secara manual
            const modalEl = document.getElementById('addProductModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            // Reset Form
            document.getElementById('addProductForm').reset();
            
            // Reload Tabel
            loadProducts();
        } else {
            alert("Gagal: " + result.message);
        }

    } catch (error) {
        console.error(error);
        alert("Terjadi kesalahan sistem.");
    }
}