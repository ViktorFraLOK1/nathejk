<?php

// Get the number of panes, should be either 1 or 2
$panes = isset($_REQUEST['panes']) && is_numeric($_REQUEST['panes']) ? (int) $_REQUEST['panes'] : 1;
$panes = isset($_POST['addPane']) ? $panes + 1 : $panes;
$panes = isset($_POST['removePane']) ? $panes - 1 : $panes;
$panes = max(1, min(2, $panes));

$PAGE = new Nathejk_Page();

$typeName = isset($_GET['typeName']) ? $_GET['typeName'] : '';
$inboxPane = new Nathejk_Pane_Team($typeName);
$inboxPane->initialize();

$searchPane = null;

if ($panes == 2) {
//    $searchPane = new Sbs_Pane_Search('s');
//    $searchPane->initialize();
}

$PAGE->assign('inboxPane', $inboxPane);
$PAGE->assign('searchPane', $searchPane);

// Setup columns views for select boxes in header
$columnsViewNames = Nathejk_DisplayColumns::getViewNames();
$columnsViewNames = array_combine($columnsViewNames, $columnsViewNames);
$PAGE->assign('columnViewNames', $columnsViewNames);

$PAGE->assign('active' . ucfirst($typeName), true);
$PAGE->assign('panes', $panes);

$PAGE->display();

?>
