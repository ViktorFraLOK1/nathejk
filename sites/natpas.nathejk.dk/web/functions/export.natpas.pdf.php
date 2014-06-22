<?php
#require('/usr/share/php/fpdf/fpdf.php');

$teamIds = isset($_GET['ids']) ? explode(',', $_GET['ids']) : array();

$query = new Nathejk_Klan;
$query->columnIn('id', $teamIds);
$teams = $query->findAll();

if (!count($teams)) {
    Pasta_Http::exitWithNotFound('No teams found by specified ids');
}

$fontSize = 10;

$pdf = new Document_Pdf('L','mm','A4');
// A4, 210 mm, 297 mm
foreach ($teams as $i => $team) {
    $pdf->AddPage();
    $pdf->Image('../natpas/background.jpg', 10, 10, 129, 190);
    $pdf->Image('../natpas/background.jpg', 159, 10, 129, 190);

    //$pdf->Image('http://tilmelding.nathejk.dk/qr.image.php?teamId=' . $team->id, 7, 7, 25, 25, 'png');
    $pdf->Image('http://tilmelding.nathejk.dk/qr.image.php?teamId=' . $team->id, 7, 178, 25, 25, 'png');
    $pdf->Image('http://tilmelding.nathejk.dk/qr.image.php?teamId=' . $team->id, 94, 5, 50, 50, 'png');
    $pdf->Image('http://tilmelding.nathejk.dk/qr.image.php?teamId=' . $team->id, 117, 178, 25, 25, 'png');
    if ($team->photoId) {
        $pdf->Image('http://tilmelding.nathejk.dk/photo.image.php?id=' . $team->photoId, 25, 100, 100, 75, 'jpeg');
    }

    $pdf->SetXY(10, 20);
    $pdf->SetFont('Arial', 'B', 40);
    $pdf->Cell(84, 10, utf8_decode("{$team->armNumber}"), 0, 2, 'C');
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->SetXY(10, 30);
    $pdf->MultiCell(84, 10, utf8_decode("$team->title"), 0, 'C');
    
    $pdf->SetY(60);
    foreach ($team->members as $i => $member) {
        $pdf->SetFont('Arial','', 12);
        $pdf->SetX(25);
        $pdf->Cell(100, 5, utf8_decode($member->title), 0, 0);
        $pdf->SetX(25);
        $pdf->Cell(100, 5, $member->age, 0, 2, 'R');
    }
}
$pdf->Output();

