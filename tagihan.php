<?php
session_start();
include "config/connect.php";

if (!isset($_SESSION['userId'])) {
    die("Session userId tidak ditemukan. Pastikan login menyimpan session userId.");
}

$user = $_SESSION['userId'];

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM tagihan WHERE userId = '$user' ORDER BY t_tanggal DESC"
);

include "sidebar.php";
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/tagihan.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="main-content">
        <div class="container mt-4">
            <div class="table-container">
                <h4 class="mb-3">Tagihan</h4>
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
                            <tr>
                                <td><?= date('d-m-Y', strtotime($row['t_tanggal'])) ?></td>
                                <td><?= $row['t_keterangan'] ?></td>
                                <td>Rp <?= number_format($row['t_jumlah'], 0, ',', '.') ?></td>
                                <td>
                                    <?php if ($row['t_status'] == 'Belum Bayar'): ?>
                                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalPembayaran"
                                            data-id="<?= $row['tId'] ?>">Belum Bayar</button>

                                    <?php elseif ($row['t_status'] == 'Sudah Bayar'): ?>
                                        <button class="btn btn-success" data-bs-toggle="modal"
                                            data-bs-target="#modalDetailPembayaran" data-id="<?= $row['tId'] ?>">Sudah
                                            Bayar</button>

                                    <?php else: ?>
                                        <button class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#modalDetailPembayaran"
                                            data-id="<?= $row['tId'] ?>">Diproses</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    <!-- Modal Pembayaran -->
    <div class="modal fade" id="modalPembayaran" tabindex="-1" aria-labelledby="modalPembayaranLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content p-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title mx-auto" id="modalPembayaranLabel">Pembayaran Keamanan</h5>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <label for="rekening" class="form-label">Nomor Rekening</label>
                        <div class="info-box">1504071527</div>
                    </div>
                    <div class="d-flex justify-content-around align-items-center flex-wrap">
                        <div>
                            <label class="form-label">Foto Pembayaran</label>
                            <div class="upload-box">
                                <img src="https://cdn-icons-png.flaticon.com/512/685/685655.png"
                                    alt="Insert Picture"><br>
                                <small>Insert Picture</small>
                            </div>
                        </div>
                        <img src="../img/qr.png" width="200" alt="QR Code">
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Pembayaran -->
    <div class="modal fade" id="modalDetailPembayaran" tabindex="-1" aria-labelledby="modalDetailLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content p-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title mx-auto" id="modalDetailLabel">Detail Pembayaran</h5>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <div class="info-box">Eko Pragos</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Pembayaran</label>
                            <div class="info-box">12-25-2025</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Pembayaran</label>
                            <div class="upload-box">
                                <img src="https://cdn-icons-png.flaticon.com/512/685/685655.png" alt="Insert Picture">
                                <small>Insert Picture</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll("[data-bs-target='#modalPembayaran']").forEach(btn => {
            btn.addEventListener("click", () => {
                let id = btn.getAttribute("data-id");

                fetch("get_tagihan.php?id=" + id)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById("modalPembayaranLabel").innerText =
                            "Pembayaran " + data.t_keterangan;

                        document.querySelector(".info-box").innerText = data.no_rek;
                    });
            });
        });
    </script>

</body>

</html>