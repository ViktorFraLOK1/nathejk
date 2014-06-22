<?php
$team = $Agenda->getTeamById(isset($_GET['id']) ? intval($_GET['id']) : 0);
if (!$team) {
    Pasta_Http::exitWithNotFound();
}

$PAGE = new Nathejk_Page();
if (isset($_POST['checkIn'])) {
    $checkIn = new Nathejk_CheckIn;
    $checkIn->remark = $_POST['checkInRemark'];
    $checkIn->createdUts = strtotime($_POST['checkInDateTime']);
    $checkIn->location = $_POST['checkInPosition'];
    $checkIn->teamId = $team->id;
    $checkIn->typeName = $USER->username;
    if (!$checkIn->save()) {
        $PAGE->errorString = $checkIn->errorString;
    }    
} elseif ($_POST) {
    $columnNames = array('title', 'gruppe', 'korps', 'memberCount', 'contactTitle', 'contactAddress', 'contactPostalCode', 'contactMail', 'contactPhone', 'contactRole', 'remark', 'signupStatusTypeName', 'ligaNumber', 'lokNumber');
    foreach ($columnNames as $columnName) {
        if (isset($_POST[$columnName])) {
            $team->$columnName = $_POST[$columnName];
        }
    }
    $team->checkedAtStart = isset($_POST['checkedAtStart']) ? 1 : 0;
    if (in_array($team->typeName, array('super', 'klan'))) {
        $team->typeName = isset($_POST['typeName']) ? 'super' : 'klan';
    }
    if ($team->save()) {
        Pasta_Http::exitWithRedirect('');
    }
}



$PAGE->assign('activeTeam', true);
$PAGE->assign('team', $team);
$PAGE->assign('seniorCountOptions', array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5));
$PAGE->assign('lokNumbers', array(0 => '-', 1 => 'LOK 1', 2 => 'LOK 2', 3 => 'LOK 3', 4 => 'LOK 4', 5 => 'LOK 5'));

$PAGE->display();

?>
