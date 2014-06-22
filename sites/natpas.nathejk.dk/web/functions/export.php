<?php
require_once 'Spreadsheet/Excel/Writer.php';

function ts2excelTime($ts) {
    return ($ts + 2209161600 + date('Z', $ts)) / 86400;
}

$PAGE = new Nathejk_Page(true);

$error = '';
$redirectUrl = '';

$teams = array();
if (!empty($_GET['id'])) {
    foreach ($_GET['id'] as $teamIds) {
        $query = new Nathejk_Klan;
        $query->columnIn('id', $teamIds);
        $teams = $query->findAll();
    }
}
if (!count($teams)) {
    // close iframe
    die('luk vindue');
}

if (!empty($_POST['exportFormat'])) {
    switch ($_POST['exportFormat']) {
    case 'pdf' : 
        Pasta_Http::exitWithRedirect('export.pdf.php?ids=' . implode(',', $teamIds));

    case 'inout' : 
        Pasta_Http::exitWithRedirect('export.inout.pdf.php?ids=' . implode(',', $teamIds));

    case 'natpas' : 
        Pasta_Http::exitWithRedirect('export.natpas.pdf.php?ids=' . implode(',', $teamIds));

    case 'excel' :
        $workbook = new Spreadsheet_Excel_Writer();

        $formatDate = $workbook->addFormat();
        $formatDate->setNumFormat('YYYY-MM-DD hh:mm:ss');
        $formatHeading = $workbook->addFormat();
        $formatHeading->setBold();

        $worksheet = $workbook->addWorksheet('Hold');
        $columnIndex = 0;
        foreach ($teams[0]->columnNames as $name) {
            if (in_array($name, array('paid'))) {
                continue;
            }
            $rowIndex = 0;
            $worksheet->write($rowIndex++, $columnIndex, $name, $formatHeading);
            foreach ($teams as $team) {
                if (substr($name, -3) == 'Uts') {
                    $worksheet->setColumn($rowIndex, $columnIndex, 17);
                    if ($team->$name > 0) {
                        $worksheet->write($rowIndex, $columnIndex, ts2excelTime($team->$name), $formatDate);
                    }
                } else {
                    $worksheet->write($rowIndex, $columnIndex, utf8_decode($team->$name));
                }
                $rowIndex++;
            }
            $columnIndex++;
        }
        
        $worksheet = $workbook->addWorksheet('Deltagere');
        $columns = array('Hold id', 'Holdnavn', 'Kontaktperson', 'Kontakt e-mail', 'Deltager id', 'Banditnummer', 'Deltager navn', 'Telefon', 'DOB', 'E-mail-adresse');
        $rowIndex = 0;
        foreach ($columns as $columnIndex => $column) {
            $worksheet->write($rowIndex, $columnIndex, $column, $formatHeading);
        }
        $rowIndex++;
        foreach ($teams as $team) {
            foreach ($team->members as $member) {
                $columnIndex = 0;
                $worksheet->write($rowIndex, $columnIndex++, utf8_decode($team->id));
                $worksheet->write($rowIndex, $columnIndex++, utf8_decode($team->title));
                $worksheet->write($rowIndex, $columnIndex++, utf8_decode($team->contactTitle));
                $worksheet->write($rowIndex, $columnIndex++, utf8_decode($team->contactMail));
                $worksheet->write($rowIndex, $columnIndex++, utf8_decode($member->id));
                $worksheet->write($rowIndex, $columnIndex++, utf8_decode($member->number));
                $worksheet->write($rowIndex, $columnIndex++, utf8_decode($member->title));
                $worksheet->write($rowIndex, $columnIndex++, utf8_decode($member->phone));
                $worksheet->write($rowIndex, $columnIndex++, utf8_decode($member->birthDate));
                $worksheet->write($rowIndex, $columnIndex++, utf8_decode($member->mail));
                $rowIndex++;
            }
        }

        $workbook->send("nathejk-" . date('Ymd-Hi') . ".xls");
        $workbook->close();
    }
}

if ($error) {
    $PAGE->assign('message', $error);
} elseif ($redirectUrl) {
    $PAGE->assign('redirectUrl', $redirectUrl);
}


$PAGE->assign('teams', $teams);
$PAGE->display();
