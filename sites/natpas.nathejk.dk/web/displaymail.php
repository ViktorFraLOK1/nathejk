<?php

$query = new Nathejk_Mail;
$query->id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$PAGE = new Nathejk_Page(true);
$PAGE->assign('mail', $query->findOne());
$PAGE->display();

?>
