<?php include '../sidebar.php'
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Warga</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/data_warga.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="main-content">
        <div class="container mt-4">
            <div class="table-container">
                <h4 class="mb-3">Data Warga</h4>
                <input type="text" id="searchInput" class="form-control mb-3" placeholder="🔍 Cari Warga...">

                <table class="table align-middle text-center">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Status</th>
                            <th>Aksi</th>   
                        </tr>
                    </thead>
                    <tbody id="wargaTable">
                        <!-- Data diisi lewat JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit Status -->
    <div class="modal fade" id="modalEditStatus" tabindex="-1" aria-labelledby="modalEditStatusLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content p-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title mx-auto" id="modalEditStatusLabel">Edit Status Warga</h5>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <div class="info-box" id="modalNama">-</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select id="statusSelect" class="form-select text-center">
                            <option value="Warga">Warga</option>
                            <option value="Bendahara">Bendahara</option>
                            <option value="Ketua RT">Ketua RT</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-success" id="saveStatusBtn">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        let wargaData = [];

        // Load data dari database
        async function loadWarga() {
            const res = await fetch("../config/get_warga.php");
            wargaData = await res.json();
            renderTable(wargaData);
        }

        document.addEventListener("DOMContentLoaded", loadWarga);

        const wargaTable = document.getElementById("wargaTable");
        const searchInput = document.getElementById("searchInput");

        const modalNama = document.getElementById("modalNama");
        const statusSelect = document.getElementById("statusSelect");
        const saveStatusBtn = document.getElementById("saveStatusBtn");

        let editUserId = null;

        // Render tabel
        function renderTable(data) {
            wargaTable.innerHTML = "";

            if (data.length === 0) {
                wargaTable.innerHTML =
                    `<tr><td colspan="3" class="text-muted fst-italic">Tidak ada warga ditemukan</td></tr>`;
                return;
            }

            data.forEach(w => {
                wargaTable.innerHTML += `
            <tr>
                <td>${w.username}</td>
                <td>${w.role}</td>
                <td>
                    <button class="btn btn-primary btn-sm" onclick="editWarga(${w.userId}, '${w.username}', '${w.role}')">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                </td>
            </tr>
        `;
            });
        }

        // Buka modal
        function editWarga(id, nama, role) {
            editUserId = id;
            modalNama.textContent = nama;
            statusSelect.value = role;

            const modal = new bootstrap.Modal(document.getElementById("modalEditStatus"));
            modal.show();
        }

        // Simpan status baru
        saveStatusBtn.addEventListener("click", async () => {
            const newStatus = statusSelect.value;
            const nama = modalNama.textContent;

            const yakin = confirm(`Apakah Anda yakin ingin menjadikan "${nama}" sebagai "${newStatus}"?`);
            if (!yakin) return;

            const formData = new FormData();
            formData.append("userId", editUserId);
            formData.append("role", newStatus);

            const res = await fetch("../config/update_role.php", {
                method: "POST",
                body: formData
            });

            const text = await res.text();

            if (text === "OK") {
                alert("Status berhasil diperbarui!");
                loadWarga();
            } else {
                alert("Gagal memperbarui status!");
            }

            bootstrap.Modal.getInstance(document.getElementById("modalEditStatus")).hide();
        });

        // Search warga
        searchInput.addEventListener("input", e => {
            const query = e.target.value.toLowerCase();

            const filtered = wargaData.filter(w =>
                w.username.toLowerCase().includes(query)
            );

            renderTable(filtered);
        });
    </script>
</body>

</html>