<?php
$PAGE = new Nathejk_Page(true);

$error = '';
$redirectUrl = '';

if (!empty($_POST)) {
    $teams = isset($_POST['team']) ? $_POST['team'] : array();
    foreach ($teams as $teamId => $post) {
        $query = new Nathejk_Klan;
        $query->id = $teamId;
        $team = $query->findOne();
        if ($team) {
            $memberCount = $post['paid'] / $team->memberPrice;
            $uts = strtotime(implode('-', array_reverse(explode('/', $post['date']))) . ' 00:00:00');
            $team->addPayment($post['paid'], $uts, $memberCount);
        }
    }
    die('done, reload parent');
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
