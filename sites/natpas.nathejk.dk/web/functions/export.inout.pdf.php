<?php
//require('/usr/share/php/fpdf/fpdf.php');

$teams = array();
if (!empty($_GET['id'])) {
    foreach ($_GET['id'] as $pane) {
        foreach ($pane as $id) {
            $query = new Nathejk_Klan;
            $query->id = $id;
            $teams[] = $query->findOne();
        }
    }
}
$query = new Nathejk_Klan;
$query->columnIn('id', explode(',', $_GET['ids']));
$query->setOrderBySql('teamNumber, title');
$teams = $query->findAll();

if (!count($teams)) {
    // close iframe
    die('luk vindue');
}

$fontSize = 10;

$pdf = new Document_Pdf('P','mm','A4');

    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0, 10, 'Nathejk ' . date('Y'), 0, 2);
    $pdf->SetFont('Arial','',12);
foreach ($teams as $i => $team) {
    
    $number = ($team->teamNumber) ? "{$team->teamNumber}." : "{$team->lokNumber},";
    
    $pdf->SetFillColor($i%2 ? 230 : 240);
    $pdf->Cell(0, 7, utf8_decode("$number $team->title ({$team->gruppe})"), 0, 0, 'L', true);
    $pdf->SetX(-30);
    $pdf->Cell(10, 7, ' ' , 1, 0);
    $pdf->Cell(10, 7, ' ' , 1, 0);
    $pdf->Ln();

    //$pdf->Ln();
}
$pdf->Output();

