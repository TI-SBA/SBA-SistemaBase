<?php

require('fpdf_alpha.php');

$pdf=new PDF_ImageAlpha();

$pdf->AddPage();

$pdf->SetFont('Arial','',16);
$pdf->MultiCell(0,8, str_repeat('Hello World! ', 180));

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// A) provide image + separate 8-bit mask (best quality!)

//first embed mask image (w, h, x and y will be ignored, the image will be scaled to the target image's size)
$maskImg = $pdf->Image('alpha.png', 0,0,0,0, '', '', true); 

//embed image, masked with previously embedded mask
$pdf->Image('img.png',5,10,100,0,'','', false, $maskImg);

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// B) same as A), but using a JPG file (+ mask defined as PNG)

// Notice: although B) uses the same mask as A), we have to embed a copy of the original mask, 
// in other words: each mask can only be used once (TODO: this propably could be avoided)

//first embed mask image (w, h, x and y will be ignored, the image will be scaled to the target image's size)
$maskImg2 = $pdf->Image('alpha2.png', 0,0,0,0, '', '', true);

//embed image, masked with previously embedded mask
$pdf->Image('img.jpg',105,10,100,0,'','', false, $maskImg2);

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// C) use alpha channel from PNG (alpha channel converted to 7-bit by GD, lower quality)
$pdf->ImagePngWithAlpha('image_with_alpha.png',55,100,100,0);

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// D) same as C), but using Image()-method that recognizes the alpha channel
$pdf->Image('image_with_alpha.png',55,190,100,0,'PNG');

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// send PDF to browser
$pdf->Output();

?>