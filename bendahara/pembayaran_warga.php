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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
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
            </div>
            <?php
            include "../config/connect.php";

            $query = "
                SELECT 
                    t.tId,
                    t.t_tanggal,
                    t.t_keterangan,
                    t.t_jumlah,
                    u.username,
                    p.bStatus,
                    p.bFoto,
                    p.bId
                FROM tagihan t
                JOIN pembayaran p ON p.tId = t.tId
                JOIN user u ON u.userId = p.userId
                ORDER BY t.tId DESC, u.username ASC
            ";

            $result = mysqli_query($koneksi, $query);

            // ===============================
            // 🔹 GROUP DATA PER IURAN (TAGIHAN)
            // ===============================
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $groupedData = [];

            foreach ($rows as $row) {
                $tId = $row['tId'];

                if (!isset($groupedData[$tId])) {
                    $groupedData[$tId] = [
                        't_tanggal' => $row['t_tanggal'],
                        't_keterangan' => $row['t_keterangan'],
                        't_jumlah' => $row['t_jumlah'],
                        'items' => []
                    ];
                }

                $groupedData[$tId]['items'][] = $row;
            }
            ?>

            <!-- ===============================
                 TAMPILAN DIKELOMPOKKAN PER IURAN + BISA DILIPAT
            =============================== -->
            <?php foreach ($groupedData as $tId => $group): ?>
                <div class="table-container mt-3 border rounded">

                    <!-- Header Iuran (klik untuk lipat/buka) -->
                    <button type="button"
                        class="w-100 text-start p-3 bg-primary text-white border-0 d-flex justify-content-between align-items-center"
                        data-bs-toggle="collapse" data-bs-target="#iuran-<?= (int) $tId ?>" aria-expanded="false"
                        aria-controls="iuran-<?= (int) $tId ?>" style="border-radius: .375rem .375rem 0 0;">
                        <div>
                            <strong><?= htmlspecialchars($group['t_keterangan']) ?></strong><br>
                            <small>
                                Tanggal: <?= date('d-m-Y', strtotime($group['t_tanggal'])) ?> |
                                Nominal: Rp <?= number_format($group['t_jumlah'], 0, ',', '.') ?>
                            </small>
                        </div>
                        <i class="bi bi-chevron-down"></i>
                    </button>

                    <!-- Isi Iuran (default terlipat) -->
                    <div id="iuran-<?= (int) $tId ?>" class="collapse">
                        <div class="p-3">

                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle mb-0">
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

                                    <!-- NOTE:
                                         id="payment-table-body" kamu biarkan saja (tidak mengganggu JS yang ada),
                                         walaupun secara HTML seharusnya unik. Ini untuk menjaga kompatibilitas.
                                    -->
                                    <tbody id="payment-table-body">
                                        <?php
                                        $no = 1;
                                        foreach ($group['items'] as $row) {
                                            $foto = $row['bFoto'] ?? '';
                                            ?>
                                            <tr data-foto="<?= htmlspecialchars($foto) ?>">
                                                <td><?= $no++ ?></td>
                                                <td><?= htmlspecialchars($row['username']) ?></td>
                                                <td><?= date('d-m-Y', strtotime($row['t_tanggal'])) ?></td>
                                                <td><?= htmlspecialchars($row['t_keterangan']) ?></td>
                                                <td>Rp <?= number_format($row['t_jumlah'], 0, ',', '.') ?></td>
                                                <td>
                                                    <?php
                                                    if ($row['bStatus'] === NULL) {
                                                        echo '<span class="badge bg-secondary">Belum Bayar</span>';
                                                    } elseif ($row['bStatus'] === 'Verifikasi') {
                                                        echo '<button class="btn btn-warning btn-verifikasi" data-id="' . (int) $row['bId'] . '">
                                                                Verifikasi
                                                              </button>';
                                                    } elseif ($row['bStatus'] === 'Disetujui') {
                                                        echo '<span class="badge bg-success">Sudah Bayar</span>';
                                                    } elseif ($row['bStatus'] === 'Ditolak') {
                                                        echo '<span class="badge bg-danger">Ditolak</span>';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>
            <?php endforeach; ?>

        </div>
    </div>

    <!-- =========================
         MODAL TAMBAH TAGIHAN
    ========================== -->
    <div class="modal fade" id="tambahTagihanModal" tabindex="-1" aria-labelledby="tambahTagihanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="tambahTagihanModalLabel">Tambah Tagihan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="formTambahTagihan" enctype="multipart/form-data" action="../config/insert_iuran.php"
                        method="POST">

                        <div class="mb-3">
                            <label class="form-label">Izin Pengajuan (Ketua)</label>
                            <select id="pId" name="pId" class="form-control" required>
                                <option value="">-- Pilih Pengajuan --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tanggalTagihan" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggalTagihan" name="t_tanggal" required>
                        </div>

                        <div class="mb-3">
                            <label for="keteranganTagihan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keteranganTagihan" name="t_keterangan" rows="3"
                                required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="jumlahTagihan" class="form-label">Jumlah (Rp)</label>
                            <input type="number" class="form-control" id="jumlahTagihan" name="t_jumlah" required>
                        </div>

                        <div class="mb-3">
                            <label for="noRekening" class="form-label">Nomor Rekening</label>
                            <input type="text" class="form-control" id="noRekening" name="no_rek" required>
                        </div>

                        <div class="mb-3">
                            <label for="atasNama" class="form-label">Atas Nama</label>
                            <input type="text" class="form-control" id="atasNama" name="atas_nama" required>
                        </div>

                        <div class="mb-3">
                            <label for="fotoQR" class="form-label">Foto QR</label>
                            <input type="file" class="form-control" name="foto_qr" id="fotoQR" accept="image/*"
                                required>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" id="btnSimpan" class="btn btn-danger" disabled>
                                🚫 Simpan Iuran
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- =========================
         MODAL VERIFIKASI
    ========================== -->
    <div class="modal fade" id="verifikasiModal" tabindex="-1" aria-labelledby="verifikasiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
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
                        <label class="form-label">Foto Pembayaran (klik untuk fullscreen)</label>
                        <div class="border rounded p-2 text-center"
                            style="height: 220px; display:flex; align-items:center; justify-content:center;">
                            <img id="modalFoto" src="https://via.placeholder.com/300?text=No+Image"
                                class="img-fluid img-thumbnail" style="max-height: 200px; cursor: zoom-in;"
                                alt="Foto Pembayaran">
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

    <!-- =========================
         MODAL PREVIEW FULLSCREEN
    ========================== -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="previewFullImage" src="" class="img-fluid" style="max-height: 90vh;" alt="Preview Full">
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('click', function (e) {

            if (e.target.closest('#btnTambahTagihan')) {
                const modalEl = document.getElementById('tambahTagihanModal');
                if (!modalEl) return;
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
                return;
            }

            const btnVerif = e.target.closest('.btn-verifikasi');
            if (btnVerif) {

                const row = btnVerif.closest('tr');
                const bId = btnVerif.getAttribute('data-id');

                document.getElementById('modalNama').value =
                    row.cells[1].textContent.trim();
                document.getElementById('modalTanggal').value =
                    row.cells[2].textContent.trim();

                const fotoName = row.dataset.foto || "";
                const fotoPath = fotoName
                    ? ("../uploads/" + fotoName)
                    : "https://via.placeholder.com/300?text=No+Image";

                const modalFoto = document.getElementById('modalFoto');
                modalFoto.src = fotoPath;
                modalFoto.dataset.fullsrc = fotoPath;

                const modal = document.getElementById('verifikasiModal');
                modal.dataset.paymentId = bId;

                bootstrap.Modal.getOrCreateInstance(modal).show();
                return;
            }

            if (e.target && e.target.id === 'modalFoto') {
                const fotoPath = e.target.dataset.fullsrc || e.target.src;

                document.getElementById('previewFullImage').src = fotoPath;

                const previewEl = document.getElementById('imagePreviewModal');
                bootstrap.Modal.getOrCreateInstance(previewEl, { backdrop: false }).show();
                return;
            }

            if (e.target.id === 'btnSetuju') {
                const modal = document.getElementById('verifikasiModal');
                const bId = modal.dataset.paymentId;

                fetch('../config/update_status_iuran.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `bId=${encodeURIComponent(bId)}&status=Disetujui`
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else alert(data.msg || 'Gagal update');
                    })
                    .catch(err => alert('Fetch error: ' + err));

                return;
            }

            if (e.target.classList.contains('btn-danger') && e.target.closest('#verifikasiModal')) {
                const modal = document.getElementById('verifikasiModal');
                const bId = modal.dataset.paymentId;

                fetch('../config/update_status_iuran.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `bId=${encodeURIComponent(bId)}&status=Ditolak`
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) location.reload();
                        else alert(data.msg || 'Gagal update');
                    })
                    .catch(err => alert('Fetch error: ' + err));

                return;
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const selectPengajuan = document.getElementById('pId');
            const btnSimpan = document.getElementById('btnSimpan');

            if (!selectPengajuan) return;

            fetch('../config/get_pengajuan_modal.php')
                .then(res => res.json())
                .then(data => {

                    selectPengajuan.innerHTML =
                        '<option value="">-- Pilih Pengajuan --</option>';

                    if (!Array.isArray(data) || data.length === 0) {
                        const opt = document.createElement('option');
                        opt.textContent = 'Tidak ada pengajuan';
                        opt.disabled = true;
                        selectPengajuan.appendChild(opt);
                        return;
                    }

                    data.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.pId;

                        // ❌ BLOK jika bukan pemasukan
                        if (p.jenis_pengajuan === 'Pengeluaran') {
                            opt.textContent =
                                `#${p.pId} | ${p.pKeterangan} (Pengeluaran - Tidak bisa digunakan`;
                            opt.disabled = true;
                            opt.style.color = 'red';
                            opt.style.fontWeight = 'bold';

                        }
                        // ❌ BLOK jika belum disetujui
                        else if (p.pStatus !== 'Disetujui') {
                            opt.textContent =
                                `#${p.pId} | ${p.pKeterangan} (Belum Disetujui)`;
                            opt.disabled = true;
                            opt.style.color = '#6c757d';
                        }
                        // ❌ BLOK jika sudah dipakai
                        else if (p.digunakan === 'Ya') {
                            opt.textContent =
                                `#${p.pId} | ${p.pKeterangan} (Sudah Digunakan)`;
                            opt.disabled = true;
                            opt.style.color = '#6c757d';
                        }
                        // ✅ BOLEH dipakai
                        else {
                            opt.textContent =
                                `#${p.pId} | ${p.pKeterangan}`;
                            opt.style.color = '#198754'; // hijau
                            opt.style.fontWeight = '500';
                        }

                        selectPengajuan.appendChild(opt);
                    });

                })
                .catch(err => console.error('Gagal load pengajuan:', err));

            selectPengajuan.addEventListener('change', function () {
                const selected = this.options[this.selectedIndex];

                if (!this.value || selected.disabled) {
                    btnSimpan.disabled = true;
                    btnSimpan.className = 'btn btn-danger';
                    btnSimpan.innerHTML = '🚫 Simpan Iuran';
                } else {
                    btnSimpan.disabled = false;
                    btnSimpan.className = 'btn btn-success';
                    btnSimpan.innerHTML = '✔ Simpan Iuran';
                }
            });

        });
    </script>

</body>

</html>