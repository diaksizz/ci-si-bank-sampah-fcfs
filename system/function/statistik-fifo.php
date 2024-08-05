<?php
include("../system/config/koneksi.php");

// Handle form submission to update the queue
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
    $date_now = $date->format('Y-m-d H:i:s');

    // Update the entry in the database
    $query = "UPDATE setor SET updated_at='$date_now' WHERE id_setor='$id'";
    mysqli_query($conn, $query);

    // Redirect to the same page to avoid form resubmission
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Fetch the queue data
$query = mysqli_query($conn, "SELECT * FROM setor ORDER BY tanggal_setor ASC");

$total_waiting_time = 0;
$total_entries = 0;
$previous_updated_at = null;
$first_entry = null;
$current_time = time();
$first_unprocessed_entry = null;
$i = 1;

while ($row = mysqli_fetch_assoc($query)) {
    if ($first_entry == null) {
        $first_entry = $row;
    }

    if ($first_unprocessed_entry == null && empty($row['updated_at'])) {
        $first_unprocessed_entry = $row;
    }

    // Set previous_updated_at to current entry's updated_at for next iteration
    $previous_updated_at = strtotime($row['tanggal_setor']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="../datatables/css/jquery.dataTables.css">
    <style>
        label {
            font-family: Montserrat;    
            font-size: 18px;
            display: block;
            color: #262627;
        }
        .card-container {
            display: flex;
            gap: 20px;
        }

        .card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 300px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-header {
            font-size: 24px;
            font-weight: bold;
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        .card-body {
            padding: 20px;
            flex-grow: 1;
        }

        .card-footer {
            padding: 20px;
            border-top: 1px solid #ddd;
            text-align: right;
        }

        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            height: 100%;
        }

        .btn:hover {
            background-color: #0056b3;
        }
        #total-queue{

        }
    </style>
</head>
<body>
    <h2 style="font-size: 30px; color: #262626;">Transaksi Setor Sampah</h2>
    <div class="card-container">
        <div class="card">
            <div class="card-header">Antrian Saat ini</div>
                <div class="card-body">
                    <div id="first-queue">
                        <?php if ($first_unprocessed_entry): ?>
                            <h3>Antrian dengan Id : <?= $first_unprocessed_entry['nin'] ?></h3>
                            <h5>Dengan waktu kedatangan: <br><?= $first_unprocessed_entry['tanggal_setor'] ?></h5>
                        <?php else: ?>
                            Tidak ada antrian.
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <?php if ($first_unprocessed_entry): ?>
                        <form method="post" action="">
                            <input type="hidden" name="id" value="<?= $first_unprocessed_entry['id_setor'] ?>">
                            <button type="submit" class="btn">Selesai</button>
                        </form>
                    <?php endif; ?>
                </div>
        </div>
        <div class="card">
            <div class="card-body" style="display:block; text-align:center">
            <h1 style="font-size: 30px; color: #262626;">
                Total Antrian
            </h1>
            <h1 style="font-size: 30px; color: #262626;"><?= $total_entries ?></h1>
            </div>
        </div>
    </div>
    <br>
    <hr>
    <br>
    <table id="example" class="display" cellspacing="0" width="100%" border="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>NIN</th>
                <th>Jenis Sampah</th>
                <th>Berat</th>
                <th>Harga</th>
                <th>Total</th>
                <th>NIA</th>
                <th>Waktu Tiba</th>
                <th>Proses</th>
                <th>Mulai Eksekusi</th>
                <th>Waktu Selesai</th>
                <th>Waktu Tunggu</th>
                <th>Selisih Waktu</th>
                <th>TA (Turn Around)</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>ID</th>
                <th>Tanggal</th>
                <th>NIN</th>
                <th>Jenis Sampah</th>
                <th>Berat</th>
                <th>Harga</th>
                <th>Total</th>
                <th>NIA</th>
                <th>Waktu Tiba</th>
                <th>Proses</th>
                <th>Mulai Eksekusi</th>
                <th>Waktu Selesai</th>
                <th>Waktu Tunggu</th>
                <th>Selisih Waktu</th>
                <th>TA (Turn Around)</th>
            </tr>   
        </tfoot>
        <tbody>
        <?php
            // Reset query result pointer and iterate again to display table rows
            mysqli_data_seek($query, 0);
            $previous_finish_time = null;
            $total_waiting_time = 0;
            $total_entries = 0;

            while ($row = mysqli_fetch_assoc($query)) {
                // Menghitung waktu tiba
                $arrival_time = strtotime($row['tanggal_setor']);

                // Menghitung waktu proses berdasarkan selisih antara tanggal_setor dan updated_at
                if (!empty($row['updated_at'])) {
                    $updated_at_time = strtotime($row['updated_at']);
                    $proses = ($updated_at_time - $arrival_time) / 60; // Convert to minutes
                } else {
                    $proses = 0;
                    $updated_at_time = null;
                }

                // Menentukan waktu mulai
                if ($previous_finish_time) {
                    $start_time = $previous_finish_time;
                } else {
                    $start_time = $arrival_time;
                }

                
                // Menentukan waktu selesai
                $finish_time = $start_time + ($proses * 60); // waktu selesai dalam detik
                
                // Menetapkan waktu menunggu
                $waiting_time = ($start_time - $arrival_time) / 60; // Convert to minutes
                $waiting_time = max(0, $waiting_time); // pastikan waktu menunggu tidak negatif
                
                // Menghitung turn around time
                $turn_around_time = $waiting_time + $proses;
                
                // Menentukan waktu eksekusi
                $waktu_eksekusi = date('H:i', $start_time);
                
                // Menentukan waktu tiba dan waktu selesai
                $waktu_tiba = date('H:i', $arrival_time);
                $waktu_selesai = $updated_at_time ? date('H:i', $finish_time) : 'N/A';
                
                $tunggu = ($finish_time - $start_time) / 60;

                // Menghitung selisih waktu dengan entri sebelumnya
                if ($previous_finish_time) {
                    $selisih_waktu = ($arrival_time - $previous_finish_time) / 60; // Convert to minutes
                } else {
                    $selisih_waktu = 0; // Default for the first entry
                }

                // Set previous_finish_time untuk entri berikutnya
                $previous_finish_time = $finish_time;

                // Akumulasi total waktu menunggu dan jumlah entri
                $total_waiting_time += $waiting_time;
                $total_entries++;
        ?>
        <tr align="center">
            <td><?php echo $i++ ?></td>
            <td><?php echo $row['tanggal_setor'] ?></td>
            <td><?php echo $row['nin'] ?></td>
            <td><?php echo $row['jenis_sampah'] ?></td>
            <td><?php echo number_format($row['berat']) . " Kg" ?></td>
            <td><?php echo "Rp. " . number_format($row['harga'], 2, ",", ".") ?></td>
            <td><?php echo "Rp. " . number_format($row['total'], 2, ",", ".") ?></td>
            <td><?php echo $row['nia'] ?></td>
            <td><?php echo $waktu_tiba ?></td>
            <td><?php echo round($proses, 0) . " menit" ?></td>
            <td><?php echo $waktu_eksekusi ?></td>
            <td><?php echo $waktu_selesai ?></td>
            <td><?php echo round($tunggu, 0) . " menit" ?></td>
            <td><?php echo round($selisih_waktu, 0) . " menit" ?></td>
            <td><?php echo round($turn_around_time, 0) . " menit" ?></td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
    <br>
    <br>
    <div id="average-waiting-time" style="text-align: center; font-size: 18px; font-family: Montserrat; color: #262627;">
        Rata-rata Waktu Menunggu: <?php echo round($total_entries > 0 ? $total_waiting_time / $total_entries : 0, 1); ?> menit
    </div>

    <script type="text/javascript" src="../datatables/js/jquery.min.js"></script>
    <script type="text/javascript" src="../datatables/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        });
    </script>
</body>
</html>
