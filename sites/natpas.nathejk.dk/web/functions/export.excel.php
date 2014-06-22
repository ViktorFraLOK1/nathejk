<?php
require_once 'Spreadsheet/Excel/Writer.php';

function ts2excelTime($ts) {
    return ($ts + 2209161600 + date('Z', $ts)) / 86400;
}


$workbook = new Spreadsheet_Excel_Writer();
$worksheet = $workbook->addWorksheet('Surveys');

$formatDate = $workbook->addFormat();
$formatDate->setNumFormat('YYYY-MM-DD hh:mm:ss');
$formatHeading = $workbook->addFormat();
$formatHeading->setBold();

$columnTitles = array('Id' => 4, 'Title' => 25, 'Status' => 10, 'Open from' => 17, 'Open until' => 17, 'Answers' => 8, 'Excel data' => 10);
$columnIndex = 0;
foreach ($columnTitles as $title => $width) {
    $worksheet->setColumn(0, $columnIndex, $width);
    $worksheet->write(0, $columnIndex++, $title, $formatHeading);
}

$surveys = Opinion_Survey::getAllByCustomerOrderByColumnAndDirection($CUSTOMER, 'id', '');

$rowIndex = 1;
foreach ($surveys as $survey) {
    if ($survey->userMayView($USER)) {
        $worksheet->write($rowIndex, 0, $survey->getId());
        $worksheet->write($rowIndex, 1, $survey->getTitle());
        $worksheet->write($rowIndex, 2, $survey->getStatusId() . ' (' . $survey->getStatusTitle() . ')');
        $worksheet->write($rowIndex, 3, ts2excelTime($survey->getOpenFromUts()), $formatDate);
        $worksheet->write($rowIndex, 4, ts2excelTime($survey->getOpenUntilUts()), $formatDate);
        $worksheet->write($rowIndex, 5, $survey->getAnswerCount());
        $worksheet->write($rowIndex, 6, $PRODUCT->getBackendUrl() . 'survey/output.excel.php?surveyId=' . $survey->getId());
        $rowIndex++;
    }
}

//$workbook->send("opinion-surveys.xls");
//$workbook->close();
?>
