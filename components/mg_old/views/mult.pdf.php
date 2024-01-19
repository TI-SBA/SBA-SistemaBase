<?php
global $f;
$f->library('pdf');
$f->library('fpdf_alpha');
/* Caveat: I'm not a PHP programmer, so this may or may
 * not be the most idiomatic code...
 *
 * FPDF is a free PHP library for creating PDFs:
 * http://www.fpdf.org/
 */
class PDF1 extends PDF_ImageAlpha {
    const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;
    // tweak these values (in pixels)
    const MAX_WIDTH = 800;
    const MAX_HEIGHT = 500;
    function pixelsToMM($val) {
        return $val * self::MM_IN_INCH / self::DPI;
    }
    function resizeToFit($imgFilename) {
        list($width, $height) = getimagesize($imgFilename);
        $widthScale = self::MAX_WIDTH / $width;
        $heightScale = self::MAX_HEIGHT / $height;
        $scale = min($widthScale, $heightScale);
        return array(
            round($this->pixelsToMM($scale * $width)),
            round($this->pixelsToMM($scale * $height))
        );
    }
    function centreImage($img) {
        list($width, $height) = $this->resizeToFit($img);
        // you will probably want to swap the width/height
        // around depending on the page's orientation
        $this->Image(
            $img, (self::A4_HEIGHT - $width) / 2,
            (self::A4_WIDTH - $height) / 2,
            $width,
            $height
        );
    }
}
// usage:
$pdf = new PDF1();
foreach ($data as $img) {
	$pdf->AddPage("P");
	$pdf->centreImage("tmp/".$img);
}
//$pdf->Output();
$pdf->Output('tmp/file.pdf','F');
?>