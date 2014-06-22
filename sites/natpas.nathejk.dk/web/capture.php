<?php
//$capturePane = new Nathejk_Pane_Capture('capture');
//$capturePane->initialize();

$teamType = '';
if (isset($_GET['teamId'])) {
    $query = new Nathejk_Klan;
    $query->id = intval($_GET['teamId']);
    if ($team = $query->findOne()) {
        $teamType = $team->typeName;
    }
}

$query = new Nathejk_CheckIn;
$query->deletedUts = 0;
switch ($teamType) {
    case 'klan' :
    case 'super' :
        $q = new Nathejk_Senior;
        $q->teamId = $team->id;
        $query->columnIn('memberId', $q->findColumn('id'));
        break;

    case 'patrulje' :
        $query->teamId = intval($_GET['teamId']);
        break;

    case 'lok' :
        $query->columnIn('memberId', $team->memberIds);
        break;
}
$query->addWhereSql('typeName != "qr-fail"');
$query->setOrderBySql('createdUts DESC');
$captures = $query->findAll();

$PAGE = new Nathejk_Page();
$PAGE->assign('activeCapture', true);
//$PAGE->assign('capturePane', $capturePane);
$PAGE->assign('captures', $captures);
$PAGE->display();
?>
