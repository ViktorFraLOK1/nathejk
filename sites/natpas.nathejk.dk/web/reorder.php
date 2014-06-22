<?php

$q = new Nathejk_Patrulje;
$q->deletedUts = 0;
$q->setOrderBySql('id');
$teams = $q->findAll();
$n = 1;
foreach ($teams as $team) {
    print "$n. {$team->title}<br>\n";
    $team->teamNumber = $n++;
//    $team->save();
}

