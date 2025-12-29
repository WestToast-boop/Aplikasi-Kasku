<?php include '../sidebar.php'; ?>
<!DOCTYPE html>
<html lang="id">

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

                <!-- Tombol tambah + search (JANGAN taruh di dalam tbody) -->
                <div class="d-flex gap-2 flex-wrap mb-3">
                    <button class="btn btn-success" id="btnTambahWarga">
                        <i class="bi bi-plus-circle"></i> Tambah Warga
                    </button>

                    <input type="text" id="searchInput" class="form-control" style="max-width: 320px;"
                        placeholder="🔍 Cari Warga...">
                </div>

                <table class="table align-middle text-center">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="wargaTable">
                        <!-- Diisi lewat JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- =======================
     MODAL EDIT ROLE
======================= -->
    <div class="modal fade" id="modalEditStatus" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content p-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title mx-auto">Edit Role Warga</h5>
                </div>

                <div class="modal-body text-center">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <div class="info-box" id="modalNama">-</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select id="statusSelect" class="form-select text-center">
                            <option value="warga">warga</option>
                            <option value="bendahara">bendahara</option>
                            <option value="ketua">ketua</option>
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

    <!-- =======================
     MODAL TAMBAH WARGA
======================= -->
    <div class="modal fade" id="modalTambahWarga" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content p-4">
                <div class="modal-header border-0">
                    <h5 class="modal-title mx-auto">Tambah Warga</h5>
                </div>

                <div class="modal-body">
                    <form id="formTambahWarga">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" id="tambahUsername" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="tambahAlamat" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="tambahPassword" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select text-center" id="tambahRole">
                                <option value="warga">warga</option>
                                <option value="bendahara">bendahara</option>
                                <option value="ketua">ketua</option>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-success" id="btnSimpanWarga">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        let wargaData = [];
        let editUserId = null;

        const wargaTable = document.getElementById("wargaTable");
        const searchInput = document.getElementById("searchInput");

        const modalNama = document.getElementById("modalNama");
        const statusSelect = document.getElementById("statusSelect");
        const saveStatusBtn = document.getElementById("saveStatusBtn");

        function escapeHtml(s) {
            return String(s ?? '')
                .replaceAll("&", "&amp;")
                .replaceAll("<", "&lt;")
                .replaceAll(">", "&gt;")
                .replaceAll('"', "&quot;")
                .replaceAll("'", "&#039;");
        }

        async function loadWarga() {
            try {
                const res = await fetch("../config/get_warga.php");
                const data = await res.json();
                wargaData = Array.isArray(data) ? data : [];
                renderTable(wargaData);
            } catch (err) {
                console.error("Gagal load warga:", err);
                wargaTable.innerHTML = `<tr><td colspan="3" class="text-danger">Gagal mengambil data</td></tr>`;
            }
        }

        function renderTable(data) {
            wargaTable.innerHTML = "";

            if (!data || data.length === 0) {
                wargaTable.innerHTML = `<tr><td colspan="3" class="text-muted fst-italic">Tidak ada warga ditemukan</td></tr>`;
                return;
            }

            data.forEach(w => {
                wargaTable.innerHTML += `
        <tr>
          <td>${escapeHtml(w.username)}</td>
          <td>${escapeHtml(w.role)}</td>
          <td>
            <button class="btn btn-primary btn-sm"
              onclick="editWarga(${Number(w.userId)}, '${escapeHtml(w.username)}', '${escapeHtml(w.role)}')">
              <i class="bi bi-pencil"></i> Edit
            </button>
          </td>
        </tr>
      `;
            });
        }

        // expose function to global (dipakai onclick)
        window.editWarga = function (id, nama, role) {
            editUserId = id;
            modalNama.textContent = nama;
            statusSelect.value = role;

            bootstrap.Modal.getOrCreateInstance(document.getElementById("modalEditStatus")).show();
        }

        // Simpan role baru
        saveStatusBtn.addEventListener("click", async () => {
            const newRole = statusSelect.value;
            const nama = modalNama.textContent;

            if (!editUserId) return;

            const yakin = confirm(`Apakah Anda yakin ingin menjadikan "${nama}" sebagai "${newRole}"?`);
            if (!yakin) return;

            const fd = new FormData();
            fd.append("userId", editUserId);
            fd.append("role", newRole);

            try {
                const res = await fetch("../config/update_role.php", {
                    method: "POST",
                    body: fd
                });
                const json = await res.json();

                if (json.success) {
                    alert("Role berhasil diperbarui!");
                    bootstrap.Modal.getInstance(document.getElementById("modalEditStatus")).hide();
                    loadWarga();
                } else {
                    alert(json.message || "Gagal memperbarui role!");
                }
            } catch (err) {
                console.error(err);
                alert("Fetch error");
            }
        });

        // Search
        searchInput.addEventListener("input", e => {
            const q = e.target.value.toLowerCase();
            const filtered = wargaData.filter(w => (w.username ?? '').toLowerCase().includes(q));
            renderTable(filtered);
        });

        // Buka modal tambah warga
        document.getElementById("btnTambahWarga").addEventListener("click", () => {
            bootstrap.Modal.getOrCreateInstance(document.getElementById("modalTambahWarga")).show();
        });

        // Simpan warga baru
        document.getElementById("btnSimpanWarga").addEventListener("click", async () => {
            const username = document.getElementById("tambahUsername").value.trim();
            const alamat = document.getElementById("tambahAlamat").value.trim();
            const password = document.getElementById("tambahPassword").value;
            const role = document.getElementById("tambahRole").value;

            if (!username || !alamat || !password || !role) {
                alert("Semua field wajib diisi");
                return;
            }

            const fd = new FormData();
            fd.append("username", username);
            fd.append("alamat", alamat);
            fd.append("password", password);
            fd.append("role", role);

            try {
                const res = await fetch("../config/insert_warga.php", {
                    method: "POST",
                    body: fd
                });
                const json = await res.json();

                if (json.success) {
                    alert("Warga berhasil ditambahkan!");
                    document.getElementById("formTambahWarga").reset();
                    bootstrap.Modal.getInstance(document.getElementById("modalTambahWarga")).hide();
                    loadWarga();
                } else {
                    alert(json.message || "Gagal menambahkan warga!");
                }
            } catch (err) {
                console.error(err);
                alert("Fetch error");
            }
        });

        document.addEventListener("DOMContentLoaded", loadWarga);
    </script>

</body>

</html>