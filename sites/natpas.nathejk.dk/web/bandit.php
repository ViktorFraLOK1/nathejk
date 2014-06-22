<?php

$query = new Nathejk_Photo;
$query->addWhereSql('memberId > 0');
foreach ($query->findAll() as $photo) {
    $q = new Nathejk_Senior;
    $q->id = $photo->memberId;
    $member = $q->findOne();
    print '<h1>' . $member->title . ' (<a href="' . $member->team->urlId . '">' . $member->team->title . '</a>)</h1>';
    print '<img src="' . $photo->url . '">';
    print '<hr>';
}
