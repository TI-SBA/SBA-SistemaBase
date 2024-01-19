<?php
$data = $_GET['data'];
$text = base64_decode($data);
require_once('../libraries/tcpdf_min/tcpdf_barcodes_2d.php');
$pdf = new TCPDF2DBarcode($text,'PDF417');
$pdf->getBarcodePNG();

?>