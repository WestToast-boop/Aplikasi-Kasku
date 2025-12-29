<?php
include '../sidebar.php';

?>
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
                                    <th>Jenis Pengajuan</th>
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
                            <label class="form-label">Pengaju</label>
                            <input type="text" class="form-control"
                                value="<?= htmlspecialchars($_SESSION['username'] ?? ''); ?>" readonly>
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

                        <div class="mb-3">
                            <label class="form-label">Jenis Pengajuan</label>
                            <select name="jenis_pengajuan" class="form-control" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="Pemasukan">Pemasukan (Uang Masuk)</option>
                                <option value="Pengeluaran">Pengeluaran (Uang Keluar)</option>
                            </select>
                        </div>

                        <!-- kalau kamu butuh field detail pengajuan, aktifkan ini -->
                        <!--
                    <div class="mb-3">
                        <label class="form-label">Detail (opsional)</label>
                        <textarea class="form-control" id="detailPengajuan" rows="2"></textarea>
                    </div>
                    -->
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpanPengajuan">Simpan</button>
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
                        <label class="form-label">Jenis</label>
                        <input type="text" class="form-control" id="detailJenis" readonly>
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
                            <td>${row.nama_pengaju ?? '-'}</td>
                            <td>${row.pTanggal ?? '-'}</td>
                            <td>${row.pKeterangan ?? '-'}</td>
                            <td>Rp ${parseInt(row.pJumlah ?? 0).toLocaleString()}</td>
                            <td>${row.jenis_pengajuan ?? '-'}</td>
                            <td>
                                <button class="btn btn-outline-primary btn-sm btn-status"
                                        data-id="${row.pId}">
                                    ${row.pStatus ?? '-'}
                                </button>
                            </td>
                        </tr>
                    `;
                    });
                })
                .catch(err => console.error("Load error:", err));
        }
        loadPengajuan();

        // ================= TAMBAH MODAL =================
        document.getElementById('btnTambahPengajuan')
            .addEventListener('click', () => {
                new bootstrap.Modal(document.getElementById('tambahPengajuanModal')).show();
            });

        // ================= SIMPAN =================
        document.getElementById('btnSimpanPengajuan')
            .addEventListener('click', async () => {

                const tanggal = document.getElementById('tanggalPengajuan').value;
                const ket = document.getElementById('keteranganPengajuan').value.trim();
                const jumlah = document.getElementById('jumlahPengajuan').value;

                const jenis = document.querySelector('#formTambahPengajuan select[name="jenis_pengajuan"]').value;

                if (!tanggal || !ket || !jumlah || !jenis) {
                    alert("Semua field wajib diisi");
                    return;
                }

                const fd = new FormData();
                // pDetail sekarang bukan nama, jadi kosong saja (atau isi dari field detail kalau kamu pakai)
                fd.append("pDetail", "");
                fd.append("pTanggal", tanggal);
                fd.append("pKeterangan", ket);
                fd.append("pJumlah", jumlah);
                fd.append("jenis_pengajuan", jenis);

                try {
                    const res = await fetch("../config/insert_pengajuan.php", {
                        method: "POST",
                        body: fd
                    });

                    const text = await res.text();
                    let json;

                    try {
                        json = JSON.parse(text);
                    } catch (e) {
                        console.error("Response bukan JSON:", text);
                        alert("Backend error, cek console");
                        return;
                    }

                    if (json.success === true) {
                        loadPengajuan();
                        bootstrap.Modal.getInstance(document.getElementById('tambahPengajuanModal')).hide();
                        document.getElementById('formTambahPengajuan').reset();
                    } else {
                        alert(json.message || "Gagal menyimpan");
                    }

                } catch (err) {
                    console.error(err);
                    alert("Fetch error");
                }
            });

        // ================= DETAIL =================
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-status');
            if (!btn) return;

            const id = btn.dataset.id;

            fetch("../config/get_detail_pengajuan.php?pId=" + encodeURIComponent(id))
                .then(res => res.json())
                .then(d => {
                    if (d.success === false) {
                        alert(d.message || "Data tidak ditemukan");
                        return;
                    }

                    document.getElementById('detailNama').value = d.nama_pengaju ?? '-';
                    document.getElementById('detailTanggal').value = d.pTanggal ?? '-';
                    document.getElementById('detailKeterangan').value = d.pKeterangan ?? '-';
                    document.getElementById('detailJumlah').value = "Rp " + parseInt(d.pJumlah ?? 0).toLocaleString();
                    document.getElementById('detailJenis').value = d.jenis_pengajuan ?? '-';
                    document.getElementById('detailStatus').value = d.pStatus ?? '-';

                    new bootstrap.Modal(document.getElementById('detailPengajuanModal')).show();
                });
        });
    </script>

</body>

</html>