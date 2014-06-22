<?php

// Klaner
$query = new Nathejk_Klan;
$query->deletedUts = 0;
$query->columnIn('typeName', array('klan', 'super'));
$allTeamIds = $query->findColumn('id');

$query->signupStatusTypeName = Nathejk_Team::STATUS_PAID;
$klanCount = $query->countAll();

$query = new Nathejk_Senior;
$query->deletedUts = 0;
$query->columnIn('teamId', $allTeamIds);
$seniorCount = $query->countAll();
//$seniorCount = $query->getPearDb()->getOne("SELECT SUM(paidMemberCount) FROM team WHERE typeName='klan'");
//$klanCount = count($activeTeamIds);
$pendingKlanCount = count($allTeamIds) - $klanCount;


// Patruljer
$query = new Nathejk_Patrulje;
$query->deletedUts = 0;
$query->typeName = 'patrulje';
$allTeamIds = $query->findColumn('id');

$q = new Nathejk_Spejder;
$q->columnIn('teamId', $allTeamIds);
$q->deletedUts = 0;
$spejderCount = $q->countAll();
$q->discontinuedUts = 0;
$q->addWhereSql('pausedUts > 0');
$pausedSpejderCount = $q->countAll();
$q = new Nathejk_Spejder;
$q->columnIn('teamId', $allTeamIds);
$q->deletedUts = 0;
$q->discontinuedUts = 0;
$q->pausedUts = 0;
$activeSpejderCount = $q->countAll();


$q = new Nathejk_Spejder;
$q->columnIn('teamId', $allTeamIds);
$q->deletedUts = 0;
$q->addWhereSql('discontinuedUts > 0');
$stoppedSpejderCount = $q->countAll();


$query->signupStatusTypeName = Nathejk_Team::STATUS_PAID;
$activeTeamIds = $query->findColumn('id');

//$:w$query = new Nathejk_Spejder;
//$spejderCount = $query->getPearDb()->getOne("SELECT SUM(paidMemberCount) FROM team WHERE typeName='patrulje'");
//$query->deletedUts = 0;
//$query->columnIn('teamId', $allTeamIds);
//$spejderCount = $query->countAll();
$patruljeCount = count($activeTeamIds);
$pendingPatruljeCount = count($allTeamIds) - $patruljeCount;

// Bingo
/*$query = new Nathejk_Patrulje;
$query->deletedUts = 0;
$query->typeName = 'patrulje';
$query->signupStatusTypeName = Nathejk_Team::STATUS_PAID;
$allTeamIds = $query->findColumn('id');
$query = new Nathejk_Klan;
$query->deletedUts = 0;
$query->columnIn('typeName', array('klan', 'lok'));
//$query->signupStatusTypeName = Nathejk_Team::STATUS_PAID;
$allKlanIds = $query->findColumn('id');
$query = new Nathejk_Senior;
$query->deletedUts = 0;
$query->columnIn('teamId', $allKlanIds);
$allSeniorIds = $query->findColumn('id');
$query = new Nathejk_CheckIn;
$query->columnIn('memberId', $allSeniorIds);
$caughtTeamIds = $query->findColumn('teamId');
$query = new Nathejk_Patrulje;
$query->columnIn('id', array_diff($allTeamIds, $caughtTeamIds));
$bingoTeams = array();
foreach ($query->findAll() as $p) {
    if ($p->activeMemberCount >= 3) {
        $bingoTeams[] = $p;
    }
}
*/

$PAGE = new Nathejk_Page();
$PAGE->assign('activeIndex', true);
$PAGE->assign(compact('bingoTeams', 'seniorCount', 'klanCount', 'pendingKlanCount', 'spejderCount', 'patruljeCount', 'pendingPatruljeCount'));
$PAGE->assign(compact('activeSpejderCount', 'pausedSpejderCount', 'stoppedSpejderCount'));
$PAGE->display();
?>
