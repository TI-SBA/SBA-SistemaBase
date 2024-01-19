<?php
global $f;
date_default_timezone_set('America/Lima');
$f->library('excel');
PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/in/listado_arrendatarios_inmueble.xlsx');
$baseRow = 5;

foreach ($data as $s => $sublocal) {
    $objWorkSheet = $objPHPExcel->getSheet(0)->copy();

    $row = $baseRow;
    foreach ($sublocal as $t => $tipo) {
        foreach ($tipo as $i => $inmueble) {
            foreach ($inmueble as $c => $contrato) {
                foreach ($contrato as $a => $arrendatario) {
                    $objWorkSheet->setCellValue('A'.$row, $arrendatario['direccion'])
                                ->setCellValue('B'.$row, $arrendatario['tipo'])
                                ->setCellValue('C'.$row, $arrendatario['titular'])
                                ->setCellValue('D'.$row, $arrendatario['doctipo'])
                                ->setCellValue('E'.$row, $arrendatario['docnum']);
                    $row++;
                }
            }
        }
    }
    $objWorkSheet->setTitle($arrendatario['sublocal']);
    $objPHPExcel->addSheet($objWorkSheet);
    unset($objWorkSheet);
}
$objPHPExcel->removeSheetByIndex(0);
// Auto size columns for each worksheet
foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
    $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));

    $sheet = $objPHPExcel->getActiveSheet();
    $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(true);
    /** @var PHPExcel_Cell $cell */
    foreach ($cellIterator as $cell) {
        $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
    }
}
$objWorkSheet = $objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Listado de Arrendatario por Inmueble al '.date('Y-m-d').'.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
