<?php
session_start();

$user = $_SESSION['userId'] ?? null;
$role = $_SESSION['role'] ?? null;

include '../sidebar.php';

if (!$user) {
    header("Location: ../login.php");
    exit;
}
if ($role && $role !== 'ketua') {
    echo "Akses ditolak";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

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
                    <tbody id="pengajuan-body"></tbody>
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

                <div id="alasanBox" class="mt-3" style="display:none;">
                    <label class="fw-semibold">Alasan Keputusan</label>
                    <div class="p-2 border rounded" id="alasanText"></div>
                </div>

                <form id="formStatus" method="POST" action="../config/update_status.php"></form>

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
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function btnClassByStatus(status) {
            if (status === "Disetujui") return "btn-success";
            if (status === "Ditolak") return "btn-danger";
            return "btn-primary";
        }

        async function loadPengajuanKetua() {
            const res = await fetch("../config/get_pengajuan.php");
            const data = await res.json();

            const tbody = document.getElementById("pengajuan-body");
            tbody.innerHTML = "";

            data.forEach(row => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
        <td>${(row.pTanggal ?? "-").split("-").reverse().join("-")}</td>
        <td>${row.pKeterangan ?? "-"}</td>
        <td>Rp ${parseInt(row.pJumlah ?? 0).toLocaleString("id-ID")}</td>
        <td>
          <button class="status-btn btn ${btnClassByStatus(row.pStatus)}"
            data-bs-toggle="modal"
            data-bs-target="#modalDetail"
            data-id="${row.pId}"
            data-keterangan="${(row.pKeterangan ?? "").replace(/"/g, '&quot;')}"
            data-detail="${(row.pDetail ?? "").replace(/"/g, '&quot;')}"
            data-status="${(row.pStatus ?? "").replace(/"/g, '&quot;')}"
            data-alasan="${(row.pAlasan ?? "").replace(/"/g, '&quot;')}"
          >${row.pStatus ?? "-"}</button>
        </td>
      `;
                tbody.appendChild(tr);
            });
        }

        document.addEventListener("DOMContentLoaded", () => {
            loadPengajuanKetua();

            const modal = document.getElementById('modalDetail');
            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;

                const id = button.getAttribute('data-id');
                const ket = button.getAttribute('data-keterangan');
                const detail = button.getAttribute('data-detail');
                const status = button.getAttribute('data-status');
                const alasan = button.getAttribute('data-alasan');

                document.getElementById('modalId').value = id;
                document.getElementById('modalKet').textContent = ket || "-";

                // tampilkan detail sebagai list
                const ul = document.getElementById('modalDetailList');
                ul.innerHTML = "";
                if (!detail || detail.trim() === "") {
                    ul.innerHTML = "<li>-</li>";
                } else if (detail.includes("<li") || detail.includes("</li>")) {
                    ul.innerHTML = detail;
                } else {
                    detail.split(/\r?\n|,/).map(s => s.trim()).filter(Boolean).forEach(it => {
                        const li = document.createElement("li");
                        li.textContent = it;
                        ul.appendChild(li);
                    });
                }

                if (status === "Disetujui" || status === "Ditolak") {
                    document.getElementById('formActions').style.display = 'none';
                    document.getElementById('alasanBox').style.display = 'block';
                    document.getElementById('alasanText').textContent = (alasan && alasan.trim() !== "") ? alasan : "-";
                } else {
                    document.getElementById('formActions').style.display = 'flex';
                    document.getElementById('alasanBox').style.display = 'none';
                }
            });
        });
    </script>

</body>

</html>