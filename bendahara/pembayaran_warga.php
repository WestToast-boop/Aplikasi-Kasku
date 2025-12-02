<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Warga - KASKU</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/pembayaran.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css  " rel="stylesheet">
</head>

<body>
    <!-- Sidebar -->
    <?php include '../sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-wrapper">
            <div class="btn-group">
                <button class="btn btn-primary btn-sm btn-add" id="btnTambahTagihan">
                    <i class="bi bi-plus-circle"></i> Tambah Tagihan Baru
                </button>
                <button class="btn btn-outline-secondary btn-sm btn-search" id="btnCariWarga">
                    <i class="bi bi-search"></i> Cari Warga
                </button>
                <button class="btn btn-outline-secondary btn-sm btn-import" id="btnImportData">
                    <i class="bi bi-download"></i> Import Data Pembayaran
                </button>
            </div>

            <!-- Search Input (hidden by default) -->
            <div id="searchInputContainer" class="mb-3" style="display: none;">
                <input type="text" class="form-control" placeholder="Cari nama warga..." id="searchInput">
            </div>

            <div class="table-container mt-3 p-3">
                <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="payment-table-body">
                        <tr>
                            <td>1</td>
                            <td>Rizky</td>
                            <td>01-08-2025</td>
                            <td>Iuran 17 agustus</td>
                            <td>RP 10.000</td>
                            <td><span class="badge bg-success status-badge status-sudah-bayar">Sudah Bayar</span></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>khayra</td>
                            <td>01-08-2025</td>
                            <td>Iuran 17 agustus</td>
                            <td>RP 10.000</td>
                            <td><button class="btn-verifikasi" data-id="2">Verifikasi</button></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>khayra</td>
                            <td>01-08-2025</td>
                            <td>Iuran 17 agustus</td>
                            <td>RP 10.000</td>
                            <td><button class="btn-verifikasi" data-id="3">Verifikasi</button></td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Tagihan Baru -->
    <div class="modal fade" id="tambahTagihanModal" tabindex="-1" aria-labelledby="tambahTagihanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahTagihanModalLabel">Tambah Tagihan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formTambahTagihan">
                        <div class="mb-3">
                            <label for="namaTagihan" class="form-label">Nama Warga</label>
                            <input type="text" class="form-control" id="namaTagihan" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggalTagihan" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggalTagihan" required>
                        </div>
                        <div class="mb-3">
                            <label for="keteranganTagihan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keteranganTagihan" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="jumlahTagihan" class="form-label">Jumlah (Rp)</label>
                            <input type="number" class="form-control" id="jumlahTagihan" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpanTagihan">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import Data Pembayaran -->
    <div class="modal fade" id="importDataModal" tabindex="-1" aria-labelledby="importDataModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importDataModalLabel">Import Data Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Silakan unggah file Excel/CSV yang berisi data pembayaran.</p>
                    <input type="file" class="form-control mb-3" accept=".xlsx,.xls,.csv" id="fileImport">
                    <div class="alert alert-info">
                        Format file: Nama, Tanggal, Keterangan, Jumlah
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="btnImportFile">Import Sekarang</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Verifikasi -->
    <div class="modal fade" id="verifikasiModal" tabindex="-1" aria-labelledby="verifikasiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verifikasiModalLabel">Detail Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modalNama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="modalNama" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modalTanggal" class="form-label">Tanggal Pembayaran</label>
                        <input type="text" class="form-control" id="modalTanggal" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modalFoto" class="form-label">Foto Pembayaran</label>
                        <div class="border rounded p-2 text-center"
                            style="height: 150px; display: flex; align-items: center; justify-content: center;">
                            <img src="https://via.placeholder.com/100?text=No+Image" alt="Foto Pembayaran"
                                id="modalFoto" class="img-fluid" style="max-height: 120px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tolak</button>
                    <button type="button" class="btn btn-success" id="btnSetuju">Setuju</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail (Setelah Verifikasi) -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="detailNama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="detailNama" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="detailTanggal" class="form-label">Tanggal Pembayaran</label>
                        <input type="text" class="form-control" id="detailTanggal" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="detailStatus" class="form-label">Status Pembayaran</label>
                        <input type="text" class="form-control" id="detailStatus" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="detailWaktu" class="form-label">Waktu Status Berubah</label>
                        <input type="text" class="form-control" id="detailWaktu" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="detailFoto" class="form-label">Foto Pembayaran</label>
                        <div class="border rounded p-2 text-center"
                            style="height: 150px; display: flex; align-items: center; justify-content: center;">
                            <img src="  https://via.placeholder.com/100?text=No+Image" alt="Foto Pembayaran"
                                id="detailFoto" class="img-fluid" style="max-height: 120px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        // === TOMBOL "TAMBAH TAGIHAN BARU" ===
        document.getElementById('btnTambahTagihan').addEventListener('click', function () {
            const modal = new bootstrap.Modal(document.getElementById('tambahTagihanModal'));
            modal.show();
        });

        // === SIMPAN TAGIHAN ===
        document.getElementById('btnSimpanTagihan').addEventListener('click', function () {
            const nama = document.getElementById('namaTagihan').value.trim();
            const tanggal = document.getElementById('tanggalTagihan').value;
            const keterangan = document.getElementById('keteranganTagihan').value.trim();
            const jumlah = document.getElementById('jumlahTagihan').value;

            if (!nama || !tanggal || !keterangan || !jumlah) {
                alert("Semua field harus diisi!");
                return;
            }

            const tableBody = document.getElementById('payment-table-body');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${tableBody.children.length + 1}</td>
                <td>${nama}</td>
                <td>${tanggal}</td>
                <td>${keterangan}</td>
                <td>RP ${parseInt(jumlah).toLocaleString()}</td>
                <td><button class="btn-verifikasi" data-id="${tableBody.children.length + 1}">Verifikasi</button></td>
            `;
            tableBody.appendChild(newRow);

            bootstrap.Modal.getInstance(document.getElementById('tambahTagihanModal')).hide();
            document.getElementById('formTambahTagihan').reset();
            alert(`Tagihan baru untuk ${nama} berhasil ditambahkan!`);
        });

        // === TOMBOL "CARI WARGA" ===
        document.getElementById('btnCariWarga').addEventListener('click', function () {
            const container = document.getElementById('searchInputContainer');
            container.style.display = container.style.display === 'none' ? 'block' : 'none';
            if (container.style.display === 'block') {
                document.getElementById('searchInput').focus();
            }
        });

        // === FUNGSI SEARCH BERDASARKAN NAMA ===
        document.getElementById('searchInput').addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#payment-table-body tr');

            rows.forEach(row => {
                const namaCell = row.cells[1];
                const nama = namaCell.textContent.toLowerCase();

                if (nama.includes(keyword)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // === TOMBOL "IMPORT DATA PEMBAYARAN" ===
        document.getElementById('btnImportData').addEventListener('click', function () {
            const modal = new bootstrap.Modal(document.getElementById('importDataModal'));
            modal.show();
        });

        // === SIMULASI IMPORT ===
        document.getElementById('btnImportFile').addEventListener('click', function () {
            const fileInput = document.getElementById('fileImport');
            if (!fileInput.files.length) {
                alert("Silakan pilih file terlebih dahulu!");
                return;
            }

            alert("✅ File berhasil diimport! Data pembayaran telah ditambahkan.");
            bootstrap.Modal.getInstance(document.getElementById('importDataModal')).hide();
            fileInput.value = '';
        });

        // === EVENT DELEGATION: TOMBOL "VERIFIKASI" DINAMIS ===
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-verifikasi')) {
                const button = e.target;
                const row = button.closest('tr');
                const nama = row.cells[1].textContent.trim();
                const tanggal = row.cells[2].textContent.trim();
                const paymentId = button.getAttribute('data-id');

                document.getElementById('modalNama').value = nama;
                document.getElementById('modalTanggal').value = tanggal;
                document.getElementById('modalFoto').src = "  https://via.placeholder.com/100?text=Foto+" + paymentId;

                const modal = document.getElementById('verifikasiModal');
                modal.dataset.paymentId = paymentId;

                const verifikasiModal = new bootstrap.Modal(modal);
                verifikasiModal.show();
            }
        });

        // === TOMBOL "SETUJU" ===
        document.getElementById('btnSetuju').addEventListener('click', function () {
            const modal = document.getElementById('verifikasiModal');
            const paymentId = modal.dataset.paymentId;

            const row = document.querySelector(`.btn-verifikasi[data-id="${paymentId}"]`).closest('tr');
            const statusCell = row.querySelector('td:last-child');

            const newButton = document.createElement('button');
            newButton.className = 'btn-sudah-bayar';
            newButton.textContent = 'Sudah Bayar';
            newButton.setAttribute('data-id', paymentId);

            statusCell.innerHTML = '';
            statusCell.appendChild(newButton);

            bootstrap.Modal.getInstance(modal).hide();
            alert(`Pembayaran ID ${paymentId} berhasil diverifikasi!`);
        });

        // === TOMBOL "TOLAK" ===
        document.querySelector('.modal-footer .btn-danger').addEventListener('click', function () {
            console.log('Pembayaran ditolak.');
        });

        // === EVENT DELEGATION: TOMBOL "SUDAH BAYAR" DINAMIS ===
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-sudah-bayar')) {
                const button = e.target;
                const paymentId = button.getAttribute('data-id');
                const row = button.closest('tr');
                const nama = row.cells[1].textContent.trim();
                const tanggal = row.cells[2].textContent.trim();

                document.getElementById('detailNama').value = nama;
                document.getElementById('detailTanggal').value = tanggal;
                document.getElementById('detailStatus').value = "Sudah Bayar";
                document.getElementById('detailWaktu').value = "12-25-2025 14:30";
                document.getElementById('detailFoto').src = "  https://via.placeholder.com/100?text=Foto+" + paymentId;

                const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
                detailModal.show();
            }
        });
    </script>
</body>

</html>