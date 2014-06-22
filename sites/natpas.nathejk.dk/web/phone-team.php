<?php

if ($_POST) {
    foreach ($_POST as $json) {
        $post = json_decode($json);
        $team = new Nathejk_Klan;
        if (isset($post->id)) {
            $team->id = $post->id;
            $team = $team->findOne();
        }
        $team->typeName = $post->type;
        $team->title = $post->name;
        $team->save();
    }
    Pasta_Http::exitWithRedirect('functions/window-close.php');
}

$PAGE = new Nathejk_Page(true);
$PAGE->display();
//hello phone

