<?php
$PAGE = new Nathejk_Page(true);

$error = '';
$redirectUrl = '';

if (!empty($_POST)) {
    $ids = isset($_POST['ids']) ? $_POST['ids'] : array();
    foreach ($ids as $id) {
        $query = new Nathejk_Klan;
        $query->id = $id;
        $team = $query->findOne();
        if ($team) {
            if ($team->delete()) {
                $redirectUrl = '../';
            } else {
                $error = $team->errorString;
                break;
            }
        }
    }
}

if ($error) {
    $PAGE->assign('message', $error);
} elseif ($redirectUrl) {
    $PAGE->assign('redirectUrl', $redirectUrl);
}

$teams = array();
if (!empty($_GET['id'])) {
    foreach ($_GET['id'] as $pane) {
        foreach ($pane as $id) {
            $query = new Nathejk_Klan;
            $query->id = $id;
            $teams[] = $query->findOne();
        }
    }
}

$PAGE->assign('teams', $teams);
$PAGE->display();
