<?php
include "config/connect.php";
include "sidebar.php";

// ============================
// 1) SALDO KAS TERKINI
// pemasukan = total tagihan yg pembayarannya disetujui
// pengeluaran = total pengajuan disetujui
// ============================

$querySaldo = mysqli_query($koneksi, "
    SELECT 
        COALESCE((
            SELECT SUM(t.t_jumlah)
            FROM pembayaran b
            JOIN tagihan t ON t.tId = b.tId
            WHERE b.bStatus = 'Disetujui'
        ), 0) AS pemasukan,
        COALESCE((
            SELECT SUM(pJumlah)
            FROM pengajuan
            WHERE pStatus = 'Disetujui'
        ), 0) AS pengeluaran
");

$saldo = mysqli_fetch_assoc($querySaldo);
$saldoKas = (float) $saldo['pemasukan'] - (float) $saldo['pengeluaran'];


// ============================
// 2) TOTAL PENGELUARAN BULAN INI
// ============================
$queryKeluar = mysqli_query($koneksi, "
    SELECT COALESCE(SUM(pJumlah), 0) AS total
    FROM pengajuan
    WHERE pStatus='Disetujui'
      AND MONTH(pTanggal) = MONTH(CURDATE())
      AND YEAR(pTanggal)  = YEAR(CURDATE())
");
$pengeluaranBulan = (float) (mysqli_fetch_assoc($queryKeluar)['total'] ?? 0);


// ============================
// 3) TOTAL PEMASUKAN BULAN INI
// (dari pembayaran disetujui + tanggal bayar bTanggal)
// ============================
$queryMasuk = mysqli_query($koneksi, "
    SELECT COALESCE(SUM(t.t_jumlah), 0) AS total
    FROM pembayaran b
    JOIN tagihan t ON t.tId = b.tId
    WHERE b.bStatus='Disetujui'
      AND MONTH(b.bTanggal) = MONTH(CURDATE())
      AND YEAR(b.bTanggal)  = YEAR(CURDATE())
");
$pemasukanBulan = (float) (mysqli_fetch_assoc($queryMasuk)['total'] ?? 0);


// ============================
// 4) PIE CHART (Sudah vs Belum)
// sudah = Disetujui
// belum = NULL atau Verifikasi
// ============================
$queryPie = mysqli_query($koneksi, "
    SELECT
        SUM(bStatus = 'Disetujui') AS sudah,
        SUM(bStatus IS NULL OR bStatus = 'Verifikasi') AS belum
    FROM pembayaran
");
$pie = mysqli_fetch_assoc($queryPie);
$pieSudah = (int) ($pie['sudah'] ?? 0);
$pieBelum = (int) ($pie['belum'] ?? 0);


// ============================
// 5) BAR CHART (Pendapatan per Bulan untuk 2024 & 2025)
// pendapatan = sum tagihan yg disetujui pada bulan tsb (pakai bTanggal)
// ============================
$y2024 = array_fill(0, 12, 0);
$y2025 = array_fill(0, 12, 0);

$queryBar = mysqli_query($koneksi, "
    SELECT 
        YEAR(b.bTanggal) AS tahun,
        MONTH(b.bTanggal) AS bulan,
        SUM(t.t_jumlah) AS total
    FROM pembayaran b
    JOIN tagihan t ON t.tId = b.tId
    WHERE b.bStatus='Disetujui'
      AND YEAR(b.bTanggal) IN (2024, 2025)
    GROUP BY YEAR(b.bTanggal), MONTH(b.bTanggal)
    ORDER BY tahun, bulan
");

while ($row = mysqli_fetch_assoc($queryBar)) {
    $bulanIdx = (int) $row['bulan'] - 1;
    $total = (float) $row['total'];
    if ((int) $row['tahun'] === 2024)
        $y2024[$bulanIdx] = $total;
    if ((int) $row['tahun'] === 2025)
        $y2025[$bulanIdx] = $total;
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
                            <h6 class="mb-3">Grafik Pendapatan (Pembayaran Disetujui)</h6>
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

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // PIE
        const pieDataSudah = <?= (int) $pieSudah ?>;
        const pieDataBelum = <?= (int) $pieBelum ?>;

        // BAR
        const data2024 = <?= json_encode($y2024) ?>;
        const data2025 = <?= json_encode($y2025) ?>;

        // BAR CHART
        new Chart(document.getElementById('barChart'), {
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

        // PIE CHART
        new Chart(document.getElementById('pieChart'), {
            type: 'pie',
            data: {
                labels: ['Sudah Membayar', 'Belum Membayar'],
                datasets: [{
                    data: [pieDataSudah, pieDataBelum],
                    backgroundColor: ['#00c6ff', '#b2f0e9']
                }]
            },
            options: { plugins: { legend: { position: 'bottom' } } }
        });
    </script>
</body>

</html> 