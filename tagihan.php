<?php
session_start();
include "config/connect.php";

if (!isset($_SESSION['userId'])) {
    die("Session userId tidak ditemukan. Pastikan login menyimpan session userId.");
}

$user = (int)$_SESSION['userId'];

/**
 * Ambil tagihan + status pembayaran user ini + bukti (bFoto)
 */
$sql = "
    SELECT 
        tagihan.tId,
        tagihan.t_tanggal,
        tagihan.t_keterangan,
        tagihan.t_jumlah,
        tagihan.photo,
        tagihan.no_rek,
        pembayaran.bStatus,
        pembayaran.bFoto
    FROM tagihan
    LEFT JOIN pembayaran 
        ON pembayaran.tId = tagihan.tId
       AND pembayaran.userId = $user
    ORDER BY tagihan.t_tanggal DESC
";
$query = mysqli_query($koneksi, $sql);

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
                    <?php
                    $tId = (int)$row['tId'];
                    $bStatus = $row['bStatus'];          // NULL / Verifikasi / Disetujui / Ditolak
                    $bFoto = $row['bFoto'] ?? '';
                    ?>
                    <tr>
                        <td><?= date('d-m-Y', strtotime($row['t_tanggal'])) ?></td>
                        <td><?= htmlspecialchars($row['t_keterangan']) ?></td>
                        <td>Rp <?= number_format((float)$row['t_jumlah'], 0, ',', '.') ?></td>
                        <td>
                            <?php if ($bStatus === NULL): ?>
                                <button class="btn btn-primary btn-bayar"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalPembayaran"
                                        data-id="<?= $tId ?>">
                                    Bayar
                                </button>

                            <?php elseif ($bStatus === 'Ditolak'): ?>
                                <button class="btn btn-danger btn-bayar"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalPembayaran"
                                        data-id="<?= $tId ?>">
                                    Ditolak (Bayar Ulang)
                                </button>

                            <?php elseif ($bStatus === 'Verifikasi'): ?>
                                <button class="btn btn-warning btn-detail"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDetailPembayaran"
                                        data-id="<?= $tId ?>"
                                        data-bfoto="<?= htmlspecialchars($bFoto) ?>">
                                    Menunggu Verifikasi
                                </button>

                            <?php elseif ($bStatus === 'Disetujui'): ?>
                                <button class="btn btn-success btn-detail"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDetailPembayaran"
                                        data-id="<?= $tId ?>"
                                        data-bfoto="<?= htmlspecialchars($bFoto) ?>">
                                    Sudah Bayar
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<!-- =========================
     MODAL BAYAR (UPLOAD AKTIF)
========================= -->
<div class="modal fade" id="modalPembayaran" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content p-4">

            <form action="config/submit_pembayaran.php"
                  method="POST"
                  enctype="multipart/form-data"
                  id="formPembayaran">

                <div class="modal-header border-0">
                    <h5 class="modal-title mx-auto" id="modalPembayaranLabel">Pembayaran</h5>
                </div>

                <div class="modal-body text-center">

                    <!-- HIDDEN tId -->
                    <input type="hidden" name="tId" id="pay_tId">

                    <div class="mb-3">
                        <label class="form-label">Nomor Rekening</label>
                        <div class="info-box" id="pay_rekening">-</div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label class="form-label">Upload Bukti Pembayaran</label>
                            <input type="file"
                                   name="bukti"
                                   class="form-control"
                                   accept="image/*"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">QR Code (klik untuk zoom)</label><br>
                            <img id="pay_qr"
                                 src="https://via.placeholder.com/200?text=QR"
                                 class="img-fluid mt-2"
                                 style="max-height:200px; cursor: zoom-in;"
                                 alt="QR Code">
                        </div>
                    </div>

                </div>

                <div class="modal-footer justify-content-center">
                    <button type="submit" class="btn btn-primary">Kirim Pembayaran</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- =========================
     MODAL DETAIL (READ-ONLY)
     - tampilkan bukti yang sudah diupload
========================= -->
<div class="modal fade" id="modalDetailPembayaran" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content p-4">

            <div class="modal-header border-0">
                <h5 class="modal-title mx-auto" id="modalDetailLabel">Detail Pembayaran</h5>
            </div>

            <div class="modal-body text-center">

                <div class="mb-3">
                    <label class="form-label">Nomor Rekening</label>
                    <div class="info-box" id="detail_rekening">-</div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <label class="form-label">Bukti Pembayaran (klik untuk zoom)</label><br>
                        <img id="detail_bukti"
                             src="https://via.placeholder.com/200?text=Bukti"
                             class="img-fluid mt-2"
                             style="max-height:250px; cursor: zoom-in;"
                             alt="Bukti Pembayaran">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">QR Code (klik untuk zoom)</label><br>
                        <img id="detail_qr"
                             src="https://via.placeholder.com/200?text=QR"
                             class="img-fluid mt-2"
                             style="max-height:250px; cursor: zoom-in;"
                             alt="QR Code">
                    </div>
                </div>

            </div>

            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>

<!-- =========================
     MODAL PREVIEW FULLSCREEN
========================= -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content bg-dark">

            <div class="modal-header border-0">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <img id="previewFullImage"
                     src=""
                     class="img-fluid"
                     style="max-height: 90vh;"
                     alt="Preview">
            </div>

        </div>
    </div>
</div>

<script src="bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

  // helper path: kalau data sudah berisi folder, jangan dobel "uploads/"
  function resolveUploadPath(filename) {
    if (!filename) return "";
    if (filename.includes('/') || filename.includes('\\')) return filename.replace('\\','/');
    return "uploads/" + filename;
  }

  function openFullscreen(src) {
    if (!src) return;
    document.getElementById('previewFullImage').src = src;
    new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
  }

  // ==============================
  // MODAL BAYAR (NULL / DITOLAK)
  // ==============================
  const modalBayar = document.getElementById('modalPembayaran');
  modalBayar.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    if (!button) return;

    const tId = button.getAttribute('data-id');
    if (!tId) return;

    // set hidden dulu (biar ga kosong walau fetch lambat)
    document.getElementById('pay_tId').value = tId;

    fetch("config/get_tagihan.php?id=" + encodeURIComponent(tId))
      .then(res => res.json())
      .then(data => {
        document.getElementById("modalPembayaranLabel").innerText =
          "Pembayaran " + (data.t_keterangan || "");

        document.getElementById("pay_rekening").innerText =
          data.no_rek || "-";

        const qr = document.getElementById("pay_qr");
        const qrSrc = data.photo ? resolveUploadPath(data.photo) : "";
        qr.src = qrSrc || "https://via.placeholder.com/200?text=QR+Tidak+Ada";
        qr.onclick = () => openFullscreen(qr.src);
      })
      .catch(err => console.error("Gagal load tagihan:", err));
  });

  // ==============================
  // MODAL DETAIL (VERIFIKASI / DISETUJUI)
  // ==============================
  const modalDetail = document.getElementById('modalDetailPembayaran');
  modalDetail.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    if (!button) return;

    const tId = button.getAttribute('data-id');
    const bFoto = button.getAttribute('data-bfoto') || "";

    fetch("config/get_tagihan.php?id=" + encodeURIComponent(tId))
      .then(res => res.json())
      .then(data => {
        document.getElementById("modalDetailLabel").innerText =
          "Detail Pembayaran " + (data.t_keterangan || "");

        document.getElementById("detail_rekening").innerText =
          data.no_rek || "-";

        const qr = document.getElementById("detail_qr");
        const qrSrc = data.photo ? resolveUploadPath(data.photo) : "";
        qr.src = qrSrc || "https://via.placeholder.com/200?text=QR+Tidak+Ada";
        qr.onclick = () => openFullscreen(qr.src);

        const bukti = document.getElementById("detail_bukti");
        const buktiSrc = bFoto ? resolveUploadPath(bFoto) : "";
        bukti.src = buktiSrc || "https://via.placeholder.com/200?text=Bukti+Tidak+Ada";
        bukti.onclick = () => openFullscreen(bukti.src);
      })
      .catch(err => console.error("Gagal load tagihan (detail):", err));
  });

});
</script>

</body>
</html>
