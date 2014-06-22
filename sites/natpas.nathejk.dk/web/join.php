<?php

$query = new Nathejk_Klan;
$query->id = isset($_GET['teamId']) ? intval($_GET['teamId']) : 0;
$team = $query->findOne();
if (!$team) {
    Pasta_Http::exitWithNotFound();
}

if ($_POST) {
    $team->parentTeamId = isset($_POST['teamId']) ? intval($_POST['teamId']) : 0;
    if ($team->save()) {
        Pasta_Http::exitWithRedirect('functions/window-reload.php');
    }
}

$query = new Nathejk_Klan;
$query->typeName = $team->typeName;
$query->signupStatusTypeName = Nathejk_Team::STATUS_PAID;
$query->parentTeamId = 0;
$query->setOrderBySql('teamNumber, title, gruppe');
$teams = $query->findAll();

$PAGE = new Nathejk_Page(true);
$PAGE->assign(compact('team', 'teams'));
$PAGE->display();

?>
