<?php
$conn = mysqli_connect('localhost', 'root', '', 'bsk09');

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
