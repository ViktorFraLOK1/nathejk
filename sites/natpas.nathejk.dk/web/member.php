<?php

$query = new Nathejk_Senior;
$query->id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$member = $query->findOne();
if (!$member) {
    $query = new Nathejk_Klan;
    $query->id = isset($_GET['teamId']) ? $_GET['teamId'] : 0;
    $team = $query->findOne();
    if (!$team) {
        Pasta_Http::exitWithNotFound();
    }
    $memberClassName = $team->memberClassName;
    $member = new $memberClassName;
    $member->teamId = $team->id;
}

if ($_POST) {
    $columnNames = array('title', 'address', 'postalCode', 'spejderTelefon', 'phone', 'mail', 'remark', 'status'); 
    foreach ($columnNames as $columnName) {
        if (isset($_POST[$columnName])) {
            $member->$columnName = $_POST[$columnName];
        }
    }
    $member->birthDate = implode('-', array_reverse(explode('/', $_POST['birthDate'])));
    $member->returning = isset($_POST['returning']) ? 1 : 0;
/*
    if (isset($_POST['paused'])) {
        $member->pausedUts = time();
    }
    if (isset($_POST['discontinued'])) {
        $member->discontinuedUts = time();
    }
    if (isset($_POST['restart'])) {
        $member->pausedUts = 0;
        $member->discontinuedUts = 0;
    }
*/
    if ($member->save()) {
        Pasta_Http::exitWithRedirect('functions/window-reload.php');
    }
}

$PAGE = new Nathejk_Page(true);
$PAGE->assign('member', $member);
$PAGE->display();

?>
