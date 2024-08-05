<?php
// Load file koneksi.php
require_once ('../config/koneksi.php');

// Mulai buffering output
ob_start(); 
?>

<html>
<head>
  <title>Cetak PDF</title>
  <link rel="shortcut icon" href="../../asset/internal/img/img-local/favicon.ico">
    
   <style>
   h1{
      color: #262626;
     }
     table {
      max-width: 960px;
      margin: 10px auto;
     }

      thead th {
        font-weight: 400;
        background: #8a97a0;
        color: #FFF;
      }

      tr {
        background: #f4f7f8;
        border-bottom: 1px solid #FFF;
        margin-bottom: 5px;
      }

      tr:nth-child(even) {
        background: #e8eeef;
      }

      th, td {
        text-align: center;
        padding: 15px 20px;
        font-weight: 300;
      }
   </style>
</head>
<body>
  
<h1 align="center">LAPORAN DATA ADMINISTRATOR</h1>
<table align="center" cellspacing="0">
<thead>
<tr>
  <th>NIA</th>
  <th>NAMA</th>
  <th>TELEPON</th>
  <th>E-MAIL</th>
  <th>LEVEL</th>
</tr>
</thead>
<tbody>
<?php
$query = "SELECT * FROM admin"; 
$sql = mysqli_query($conn, $query); 
$row = mysqli_num_rows($sql); 
    
while($data = mysqli_fetch_array($sql)){ 
?>
<tr>
  <td><?php echo $data['nia'] ?></td>
  <td><?php echo $data['nama'] ?></td>
  <td><?php echo $data['telepon'] ?></td>
  <td><?php echo $data['email'] ?></td>
  <td><?php echo $data['level'] ?></td>
</tr>
<?php } ?>
</tbody>
</table>
</body>
</html>

<?php
$html = ob_get_contents(); 
ob_end_clean(); 

// Setelah memastikan tidak ada output sebelumnya, coba hilangkan warning dengan error_reporting()
error_reporting(0);

require_once("../../asset/plugin/html2pdf/html2pdf.class.php");
$pdf = new HTML2PDF('P','A4','en');
$pdf->WriteHTML($html);
$filename = "Data-Admin-(".date('d-m-Y').").pdf";
$pdf->Output("$filename");
?>
