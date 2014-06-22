<?php

class Nathejk_DiplomControl
{
    protected $smarty;

    public function __construct()
    {
        $this->smarty = getSmarty();
        $this->smarty->setTemplateDir(__DIR__ . '/../../markup/');

        $query = new Nathejk_Agenda;
        $agenda = $query->findOne();

        $this->smarty->assign(compact('agenda'));
    }

    public function find()
    {
        $this->smarty->display('diplom.tpl');
    }

    public function create($site, $number)
    {
        $q = new Nathejk_Patrulje;
        $q->teamNumber = $number;
        $team = $q->findOne();

        if (!$team || !$team->startUts) {
            Pasta_Http::exitWithRedirect('/diplom');
        }
        $pdf = new Document_Pdf('P','mm','A4');
        // add a page 
        $pdf->AddPage(); 
        // set the sourcefile 
        $pdf->setSourceFile($site->getRequest()->server('DOCUMENT_ROOT') . '/files/Diplom2013.pdf'); 
        $pdf->useTemplate($pdf->importPage(1), 0, 0, 210); 

        $pdf->AddFont('Impact','','impact.php');

        $pdf->SetFont('Impact', '', 20); 
        $pdf->SetTextColor(0,0,0); 
        //$pdf->Write(0, $team->title);
        $pdf->SetXY(10, 125);
        $pdf->MultiCell(0, 10, utf8_decode("$team->armNumber $team->title"), 0, 'C');
        if ($team->photoId) {
            $pdf->Image('http://tilmelding.nathejk.dk/photo.image.php?id=' . $team->photoId, 55, 140, 100, 75, 'jpeg');
        }
        $pdf->SetXY(65, 220);
        $pdf->SetFont('Arial', '', 12);
        if ($team->finishUts) {
            $time = strftime('%R', $team->finishUts);
            $pdf->Cell(80, 5, utf8_decode("har gennemført Nathejk 2013"), 0, 2, 'C');
            $pdf->Cell(80, 5, utf8_decode("fra Gilleleje til Allerød"), 0, 2, 'C');
            $pdf->Cell(80, 5, utf8_decode("og gik i mål lørdag nat kl. $time!"), 0, 2, 'C');
        } else {
            $pdf->Cell(80, 5, utf8_decode("deltog i Nathejk 2013 fra Gilleleje til Allerød"), 0, 2, 'C');
        }

        $pdf->Output('Nathejk2013.diplom.pdf', 'I'); 
    }
}
