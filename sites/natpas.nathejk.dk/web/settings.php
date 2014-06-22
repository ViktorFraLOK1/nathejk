<?php

$query = new Nathejk_Agenda;
if (!$agenda = $query->findOne()) {
    $agenda = new Nathejk_Agenda;
}

if ($_POST) {
    $columnNames = array('seniorIntro', 'spejderIntro', 'maxSeniorMemberCount');
    foreach ($columnNames as $columnName) {
        if (isset($_POST[$columnName])) {
            $agenda->$columnName = $_POST[$columnName];
        }
    }
    $columnNames = array('signupSeniorOpen', 'signupSpejderOpen');
    foreach ($columnNames as $columnName) {
        $agenda->$columnName = isset($_POST[$columnName]) ? 1 : 0;
    }
    $agenda->signupStartUts = strtotime($_POST['signupStartDateTime']);
    $agenda->save();

    Pasta_Http::exitWithRedirect('');
}

$query = new Nathejk_Marker;
$markers = $query->findAll();

$PAGE = new Nathejk_Page;
$PAGE->assign(compact('markers', 'agenda'));
$PAGE->display();

?>
