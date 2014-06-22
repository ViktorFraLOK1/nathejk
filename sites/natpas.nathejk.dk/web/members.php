<?php

$query = new Nathejk_Klan;
$query->id = isset($_GET['teamId']) ? intval($_GET['teamId']) : 0;
$team = $query->findOne();
if (!$team) {
    Pasta_Http::exitWithNotFound();
}

$memberClassName = $team->memberClassName;
if ($_POST) {
    $columnNames = array('number', 'address', 'postalCode', 'spejderTelefon', 'phone', 'mail');
    foreach ($_POST['member'] as $memberId => $post) {
        $query = new $memberClassName;
        $query->id = $memberId;
        $member = $query->findOne();

        foreach ($columnNames as $columnName) {
            if (isset($post[$columnName])) {
                $member->$columnName = $post[$columnName];
            }
        }
        $member->save();
    }
    Pasta_Http::exitWithRedirect('functions/window-reload.php');
}

$PAGE = new Nathejk_Page(true);
$PAGE->assign('team', $team);
$PAGE->display();

?>
