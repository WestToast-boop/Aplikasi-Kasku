<?php
include "config/connect.php";
include "sidebar.php";
// ============================
// 1. SALDO KAS TERKINI
// ============================
$querySaldo = mysqli_query($koneksi, "
    SELECT 
        SUM(CASE WHEN bStatus='Verifikasi' THEN bJumlah ELSE 0 END) AS pemasukan,
        (SELECT SUM(pJumlah) FROM pengajuan WHERE pStatus='Disetujui') AS pengeluaran
    FROM pembayaran
");

$saldo = mysqli_fetch_assoc($querySaldo);
$saldoKas = ($saldo['pemasukan'] ?? 0) - ($saldo['pengeluaran'] ?? 0);

// ============================
// 2. TOTAL PENGELUARAN BULAN INI
// ============================
$queryKeluar = mysqli_query($koneksi, "
    SELECT SUM(pJumlah) AS total
    FROM pengajuan
    WHERE pStatus='Disetujui'
    AND MONTH(pTanggal) = MONTH(CURRENT_DATE())
    AND YEAR(pTanggal) = YEAR(CURRENT_DATE())
");

$pengeluaranBulan = mysqli_fetch_assoc($queryKeluar)['total'] ?? 0;

// ============================
// 3. TOTAL PEMASUKAN BULAN INI
// ============================
$queryMasuk = mysqli_query($koneksi, "
    SELECT SUM(bJumlah) AS total
    FROM pembayaran
    WHERE bStatus='Verifikasi'
    AND MONTH(bTanggal) = MONTH(CURRENT_DATE())
    AND YEAR(bTanggal) = YEAR(CURRENT_DATE())
");

$pemasukanBulan = mysqli_fetch_assoc($queryMasuk)['total'] ?? 0;

// ============================
// 4. DATA PIE CHART (Sudah/Belum Membayar)
// ============================
$queryPie = mysqli_query($koneksi, "
    SELECT 
        SUM(CASE WHEN t_status='Sudah Bayar' THEN 1 ELSE 0 END) AS sudah,
        SUM(CASE WHEN t_status='Belum Bayar' THEN 1 ELSE 0 END) AS belum
    FROM tagihan
");

$pie = mysqli_fetch_assoc($queryPie);

// ============================
// 5. DATA BAR CHART (Pendapatan per Bulan)
// ============================

$queryBar = mysqli_query($koneksi, "
    SELECT 
        MONTH(bTanggal) AS bulan,
        SUM(CASE WHEN YEAR(bTanggal)=2024 THEN bJumlah ELSE 0 END) AS y2024,
        SUM(CASE WHEN YEAR(bTanggal)=2025 THEN bJumlah ELSE 0 END) AS y2025
    FROM pembayaran
    WHERE bStatus='Verifikasi'
    GROUP BY MONTH(bTanggal)
    ORDER BY bulan
");

$barData = [];
while ($row = mysqli_fetch_assoc($queryBar)) {
    $barData[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KASKU Dashboard</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card card-info h-100">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted">Saldo Kas Terkini</h6>
                            <h4 class="fw-bold">Rp <?= number_format($saldoKas, 0, ',', '.') ?></h4>
                            <i class="bi bi-wallet2 text-info fs-2"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-info h-100">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted">Total Pengeluaran Bulan Ini</h6>
                            <h4 class="fw-bold">Rp <?= number_format($pengeluaranBulan, 0, ',', '.') ?></h4>
                            <p class="small text-muted mb-1">Untuk perbaikan jalan</p>
                            <i class="bi bi-receipt text-primary fs-2"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-info h-100">
                        <div class="card-body text-center">
                            <h6 class="card-title text-muted">Total Pemasukan Bulan Ini</h6>
                            <h4 class="fw-bold">Rp <?= number_format($pemasukanBulan, 0, ',', '.') ?></h4>
                            <i class="bi bi-graph-up text-success fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3">Grafik Pendapatan</h6>
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3">Persentase Pembayaran</h6>
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // PIE DATA
        const pieDataSudah = <?= $pie['sudah'] ?>;
        const pieDataBelum = <?= $pie['belum'] ?>;

        // BAR DATA
        const data2024 = <?= json_encode(array_values($y2024)) ?>;
        const data2025 = <?= json_encode(array_values($y2025)) ?>;

        // ------- BAR CHART -------
        const ctxBar = document.getElementById('barChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [
                    { label: '2024', data: data2024, backgroundColor: '#80D0C7' },
                    { label: '2025', data: data2025, backgroundColor: '#00aaff' }
                ]
            },
            options: { responsive: true }
        });

        // ------- PIE CHART -------
        const ctxPie = document.getElementById('pieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Sudah Membayar', 'Belum Membayar'],
                datasets: [{
                    data: [pieDataSudah, pieDataBelum],
                    backgroundColor: ['#00c6ff', '#b2f0e9']
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>
</body>

</html>