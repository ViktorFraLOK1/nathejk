<?php
$q = new Nathejk_CheckIn;
$q->typeName = 'qr';
$q->addWhereSql("location LIKE '%:%'");
$checkIns = $q->findAll();

$q = new Nathejk_Marker;
$markers = $q->findAll();

$PAGE = new Nathejk_Page();
$PAGE->assign('activeMap', true);
$PAGE->assign('spot', $checkIns);
$PAGE->assign('markers', $markers);
$PAGE->display();
?>
