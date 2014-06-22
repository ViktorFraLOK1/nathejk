<?php
$team = null;
if (isset($_GET['teamId'])) {
    $query = new Nathejk_Post;
    $query->id = intval($_GET['teamId']);
    $team = $query->findOne();
}
if (!$team) {
    $team = new Nathejk_Post;
    $team->typeName = 'post';
}

if ($_POST) {
    $fields = array('title', 'gruppe', 'remark');
    foreach ($fields as $field) {
        $team->$field = $_POST[$field];
    }
    if (isset($_POST['member'])) {
    foreach ($_POST['member'] as $id => $post) {
        $member = $team->getMemberById($id);
        $member->title = $post['title'];
        $member->phone = $post['phone'];
        $member->save();
    }
    }
    //$team->openedUts = $_POST['title'];
    if (isset($_POST['delete'])) {
    } else if (isset($_POST['new'])) {
        $memberClassName = $team->memberClassName;
        $member = new $memberClassName;
        $member->teamId = $team->id;
        $member->save();
    } else if ($team->save()) {
        Pasta_Http::exitWithRedirect('functions/window-reload.php');
    }
}

$PAGE = new Nathejk_Page(true);
$PAGE->assign('activeCapture', true);
$PAGE->assign('team', $team);
$PAGE->assign('hours', range(0, 23));
$PAGE->assign('minutes', range(0, 59));
$PAGE->display();

?>
