<?php

$memberIds = array();
$q = isset($_GET['q']) ? $_GET['q'] : '';

if (!empty($q)) {
    $query = new Nathejk_Klan;
    $query->columnLike('title', "%$q%");
    $teamIds = $query->findColumn('id');
    $query = new Nathejk_Senior;
    $query->columnIn('teamId', $teamIds);
    $memberIds = array_merge($memberIds, $query->findColumn('id'));

    $query = new Nathejk_Senior;
    $query->columnLike('title', "%$q%");
    $memberIds = array_merge($memberIds, $query->findColumn('id'));
    
    $query = new Nathejk_Senior;
    $query->columnLike('mail', "%$q%");
    $memberIds = array_merge($memberIds, $query->findColumn('id'));
    
    $query = new Nathejk_Senior;
    $query->columnLike('phone', "%$q%");
    $memberIds = array_merge($memberIds, $query->findColumn('id'));
}

$query = new Nathejk_Senior;
$query->columnIn('id', $memberIds);
$members = $query->findAll();

$PAGE = new Nathejk_Page;
$PAGE->assign('activeSearch', true);
$PAGE->assign(compact('members'));
$PAGE->display();

?>
