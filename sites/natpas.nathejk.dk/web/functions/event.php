<?php

$checkInId = isset($_GET['checkInId']) ? $_GET['checkInId'] : 0;
$query = new Nathejk_CheckIn;
$query->id = $checkInId;
$checkIn = $query->findOne();
if (!$checkIn) {
    Pasta_Http::exitWithRedirect('window-close.php');
}
if ($_POST) {
    $columns = array('memberId', 'teamId', 'location', 'remark');
    foreach ($columns as $column) {
        $checkIn->$column = $_POST[$column];
    }
    $checkIn->createdUts = strtotime($_POST['inDateTime']);
    $checkIn->outUts = strtotime($_POST['outDateTime']);
    if ($checkIn->save()) {
        Pasta_Http::exitWithRedirect('window-reload.php');
    }
}
$PAGE = new Nathejk_Page(true);
$PAGE->assign('checkIn', $checkIn);
$PAGE->display();

