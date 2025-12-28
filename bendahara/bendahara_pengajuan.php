<?php include '../sidebar.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan - KASKU</title>

    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/pengajuan_bendahara.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
<div class="main-content">
<div class="container-fluid">

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center form-header">
        <button class="btn btn-tambah-pengajuan" id="btnTambahPengajuan">
            <i class="bi bi-plus-circle"></i> Tambah Pengajuan Baru
        </button>
        <i class="bi bi-search search-icon" id="searchIcon"></i>
    </div>

    <div class="card-body">

        <div id="searchInputContainer" class="mb-3" style="display:none;">
            <input type="text" class="form-control" placeholder="Cari nama..." id="searchInput">
        </div>

        <div class="table-responsive table-container">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="pengajuan-table-body"></tbody>
            </table>
        </div>

    </div>
</div>

</div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="tambahPengajuanModal">
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Tambah Pengajuan Baru</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <form id="formTambahPengajuan">
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" class="form-control" id="namaPengaju" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="tanggalPengajuan" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <textarea class="form-control" id="keteranganPengajuan" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Jumlah (Rp)</label>
                <input type="number" class="form-control" id="jumlahPengajuan" required>
            </div>
        </form>
    </div>

    <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <!-- FIX PENTING -->
        <button type="button" class="btn btn-primary" id="btnSimpanPengajuan">
            Simpan
        </button>
    </div>
</div>
</div>
</div>

<!-- MODAL DETAIL -->
<div class="modal fade" id="detailPengajuanModal">
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Detail Pengajuan</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <div class="mb-2">
            <label class="form-label">Nama</label>
            <input type="text" class="form-control" id="detailNama" readonly>
        </div>
        <div class="mb-2">
            <label class="form-label">Tanggal</label>
            <input type="text" class="form-control" id="detailTanggal" readonly>
        </div>
        <div class="mb-2">
            <label class="form-label">Keterangan</label>
            <textarea class="form-control" id="detailKeterangan" readonly></textarea>
        </div>
        <div class="mb-2">
            <label class="form-label">Jumlah</label>
            <input type="text" class="form-control" id="detailJumlah" readonly>
        </div>
        <div class="mb-2">
            <label class="form-label">Status</label>
            <input type="text" class="form-control" id="detailStatus" readonly>
        </div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
    </div>
</div>
</div>
</div>

<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
// ================= LOAD DATA =================
function loadPengajuan() {
    fetch("../config/get_pengajuan.php")
        .then(res => res.json())
        .then(data => {
            const body = document.getElementById("pengajuan-table-body");
            body.innerHTML = "";
            data.forEach((row, i) => {
                body.innerHTML += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${row.pDetail}</td>
                    <td>${row.pTanggal}</td>
                    <td>${row.pKeterangan}</td>
                    <td>Rp ${parseInt(row.pJumlah).toLocaleString()}</td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm btn-status"
                                data-id="${row.pId}">
                            ${row.pStatus}
                        </button>
                    </td>
                </tr>`;
            });
        })
        .catch(err => console.error("Load error:", err));
}
loadPengajuan();

// ================= TAMBAH MODAL =================
document.getElementById('btnTambahPengajuan')
    .addEventListener('click', () => {
        new bootstrap.Modal(
            document.getElementById('tambahPengajuanModal')
        ).show();
    });

// ================= SIMPAN =================
document.getElementById('btnSimpanPengajuan')
    .addEventListener('click', async () => {

        const nama = namaPengaju.value.trim();
        const tanggal = tanggalPengajuan.value;
        const ket = keteranganPengajuan.value.trim();
        const jumlah = jumlahPengajuan.value;

        if (!nama || !tanggal || !ket || !jumlah) {
            alert("Semua field wajib diisi");
            return;
        }

        const fd = new FormData();
        fd.append("pDetail", nama);
        fd.append("pTanggal", tanggal);
        fd.append("pKeterangan", ket);
        fd.append("pJumlah", jumlah);
        fd.append("pStatus", "Diproses");

        try {
            const res = await fetch("../config/insert_pengajuan.php", {
                method: "POST",
                body: fd
            });

            const json = await res.json();


            if (json.success === true) {
                loadPengajuan();
                bootstrap.Modal.getInstance(
                    document.getElementById('tambahPengajuanModal')
                ).hide();
                document.getElementById('formTambahPengajuan').reset();
            } else {
                alert(json.message || "Gagal menyimpan");
            }

        } catch (err) {
            alert("Error backend, cek Console");
            console.error(err);
        }
    });

// ================= DETAIL =================
document.addEventListener('click', function(e){
    const btn = e.target.closest('.btn-status');
    if (!btn) return;

    const id = btn.dataset.id;

    fetch("../config/get_detail_pengajuan.php?pId=" + id)
        .then(res => res.json())
        .then(d => {
            detailNama.value = d.pDetail;
            detailTanggal.value = d.pTanggal;
            detailKeterangan.value = d.pKeterangan;
            detailJumlah.value = "Rp " + parseInt(d.pJumlah).toLocaleString();
            detailStatus.value = d.pStatus;
            new bootstrap.Modal(
                document.getElementById('detailPengajuanModal')
            ).show();
        });
});
</script>

</body>
</html>
