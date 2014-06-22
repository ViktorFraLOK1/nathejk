<?php
die('done');
$q = new Nathejk_Patrulje;
$q->typeName = 'patrulje';
$q->signupStatusTypeName = Nathejk_Team::STATUS_PAID;
$q->setOrderBySql('(SELECT uts FROM payment WHERE teamId=team.id ORDER BY uts LIMIT 1)');
foreach ($q->findAll() as $i => $team) {
    print "$team->id: $team->title ({$i})<br>\n";
    $team->teamNumber = $i + 1;
    $team->save();
}

