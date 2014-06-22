<?php

$pats = new Nathejk_Patrulje;
$pats->signupStatusTypeName = 'PAID';
$pats->typeName = 'patrulje';
$pats->deletedUts = 0;
foreach ($pats->findAll() as $p) {
    $ms = new Nathejk_Spejder;
    $ms->deletedUts = 0;
    $ms->teamId = $p->id;
        print "<h1>$p->title ($p->memberCount)</h1>";
    foreach ($ms->findAll() as $i => $m) {
        print $m->title . ($i >= $p->memberCount ? '<span style="color:red">slet</span>' : '-') . "<br>\n";
    //    if ($i >= $p->memberCount) $m->delete();
    }
}
