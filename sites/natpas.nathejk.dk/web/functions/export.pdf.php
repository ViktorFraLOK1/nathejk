<?php
require('/usr/share/php/fpdf/fpdf.php');

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
$teams = $query->findAll();

if (!count($teams)) {
    // close iframe
    die('luk vindue');
}

$fontSize = 10;

$pdf = new FPDF('P','mm','A4');

foreach ($teams as $i => $team) {
    $pdf->AddPage();
    
    $pdf->SetFont('Arial','B',16);
    $number = ($team->teamNumber) ? "{$team->teamNumber}." : "LOK {$team->lokNumber},";
    $pdf->Cell(0, 10, utf8_decode("$number $team->title"), 1, 2);

    $info = array(
        'ID' => "{$team->id} (ikke patruljenummer)", 
        'Gruppe' => $team->gruppe, 
        'Korps' => $team->korps, 
        'Bemærkninger' => $team->remark,
    );
    $pdf->SetFont('Arial','', $fontSize);
    foreach ($info as $label => $value) {
        $pdf->Cell(35, 5, utf8_decode("$label:"), 0, 0);
        $pdf->Cell(0, 5, utf8_decode($value), 0, 1);
    }
    $pdf->Ln();

    $contact = array(
        'Navn' => $team->contactTitle,
        'Adresse' => $team->contactAddress,
        'Postnummer' => $team->contactPostalCode,
        'E-mail-adresse' => $team->contactMail,
        'Telefon' => $team->contactPhone,
        'Rolle' => $team->contactRole,
    );
    $pdf->SetFont('Arial','B', $fontSize);
    $pdf->Cell(0, 6, "Kontaktperson", 0, 2);
    $pdf->SetFont('Arial','', $fontSize);
    foreach ($contact as $label => $value) {
        $pdf->Cell(35, 5, "$label:", 0, 0);
        $pdf->Cell(0, 5, utf8_decode($value), 0, 1);
    }
    $pdf->Ln();
    $pdf->Line(10,85, 200, 85);

    foreach ($team->members as $i => $member) {
        $pdf->setXY(floor($i/4)*95+10, ($i%4)*45+90);
        $pdf->SetFont('Arial','B', $fontSize);
        $pdf->Cell(0, 6, "Deltager " . ($i+1), 0, 2);

        $pdf->SetFont('Arial','', $fontSize);
        foreach ($member->info as $label => $value) {
            if ($label == 'E-mail') continue;
            $pdf->SetX(floor($i/4)*95+10);
            $pdf->Cell(25, 5, utf8_decode("$label:"), 0, 0);
            $pdf->Cell(0, 5, utf8_decode($value), 0, 1);
        }
        $pdf->Ln();
    }
}
$pdf->Output();

