<?php
include "../config/connect.php";
session_start();

$user = $_SESSION['userId'];
$role = $_SESSION['role'];

$query = mysqli_query($koneksi, "SELECT * FROM pengajuan ORDER BY pTanggal DESC");
include '../sidebar.php'
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/pengajuan.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="main-content">
        <!-- Table -->
        <div class="container mt-4">
            <div class="table-container">
                <h4 class="mb-3">Pengajuan</h4>
                <table class="table align-middle text-center">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($query)): ?>

                            <?php
                            // Tentukan warna tombol
                            $status = $row['pStatus'];

                            if ($status == "Disetujui") {
                                $btnClass = "btn-success";
                            } elseif ($status == "Ditolak") {
                                $btnClass = "btn-danger";
                            } else {
                                $btnClass = "btn-primary";
                            }
                            ?>

                            <tr>
                                <td><?= date('d-m-Y', strtotime($row['pTanggal'])) ?></td>
                                <td><?= $row['pKeterangan'] ?></td>
                                <td>Rp <?= number_format($row['pJumlah'], 0, ',', '.') ?></td>

                                <td>
                                    <button class="status-btn btn <?= $btnClass ?>" data-bs-toggle="modal"
                                        data-bs-target="#modalDetail" data-id="<?= $row['pId'] ?>"
                                        data-keterangan="<?= $row['pKeterangan'] ?>" data-detail="<?= $row['pDetail'] ?>"
                                        data-status="<?= $row['pStatus'] ?>" data-alasan="<?= $row['pAlasan'] ?>">
                                        <?= $status ?>
                                    </button>
                                </td>
                            </tr>

                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content p-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100 text-center fw-bold fs-4" id="modalDetailLabel">Detail</h5>
                </div>

                <input type="hidden" id="modalId" name="pId" form="formStatus">

                <div class="mb-3 text-center">
                    <label class="form-label fw-semibold">Keterangan</label>
                    <div class="info-box-blue mx-auto" id="modalKet"></div>
                </div>

                <div class="mt-4">
                    <label class="form-label fw-semibold">List Barang</label>
                    <ul class="list-box" id="modalDetailList"></ul>
                </div>

                <!-- Alasan jika sudah diputus -->
                <div id="alasanBox" class="mt-3" style="display:none;">
                    <label class="fw-semibold">Alasan Keputusan</label>
                    <div class="p-2 border rounded" id="alasanText"></div>
                </div>

                <!-- Form submit -->
                <form id="formStatus" method="POST" action="update_status.php"></form>

                <!-- Tombol aksi: hanya tampil jika status = Diproses -->
                <div id="formActions" class="modal-footer border-0 justify-content-center gap-3">
                    <textarea name="pAlasan" class="form-control w-75" form="formStatus"
                        placeholder="Alasan (opsional)"></textarea>

                    <button type="submit" name="aksi" value="Tolak" form="formStatus" class="btn btn-danger px-4">
                        Tolak
                    </button>

                    <button type="submit" name="aksi" value="Setuju" form="formStatus" class="btn btn-success px-4">
                        Setuju
                    </button>
                </div>
            </div>
        </div>

        <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {

                const modal = document.getElementById('modalDetail');

                modal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;

                    const id = button.getAttribute('data-id');
                    const ket = button.getAttribute('data-keterangan');
                    const detail = button.getAttribute('data-detail');
                    const status = button.getAttribute('data-status');
                    const alasan = button.getAttribute('data-alasan');

                    // Isi modal
                    document.getElementById('modalId').value = id;
                    document.getElementById('modalKet').innerHTML = ket;
                    document.getElementById('modalDetailList').innerHTML = detail;

                    // Jika sudah disetujui / ditolak → disable button aksi dan tampilkan alasan
                    if (status === "Disetujui" || status === "Ditolak") {
                        document.getElementById('formActions').style.display = 'none';
                        document.getElementById('alasanBox').style.display = 'block';
                        document.getElementById('alasanText').innerHTML = alasan ? alasan : "-";
                    } else {
                        document.getElementById('formActions').style.display = 'flex';
                        document.getElementById('alasanBox').style.display = 'none';
                    }
                });
            });
        </script>
</body>

</html>