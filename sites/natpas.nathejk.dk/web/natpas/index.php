<?php
$query = new Nathejk_Patrulje;
$query->id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$team = $query->findOne();
if (!$team) {
    Pasta_Http::exitWithNotFound();
}


$PAGE = new Nathejk_Page(true);
//$PAGE->headerTemplatePath = 'empty.tpl';
//$PAGE->footerTemplatePath = 'empty.tpl';

$PAGE->assign('team', $team);

$PAGE->display();

?>
