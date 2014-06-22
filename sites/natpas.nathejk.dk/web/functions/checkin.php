<?php
$PAGE = new Nathejk_Page(true);

if (!isset($_COOKIE['post'])) {
    die('du skal logge ind som brugeren "post" for at kunne checke patruljer ind/ud');
}
$q = new Nathejk_Senior;
$q->id = $_COOKIE['post'];
$postuser = $q->findOne();

if (!$postuser) {
    die('du skal logge ind som brugeren "post" for at kunne checke patruljer ind/ud');
}


$error = '';
$redirectUrl = '';

/*array(1) { 
    ["photo"]=> array(5) { 
        ["name"]=> array(1) { 
            [123]=> string(28) "8bitpacmanghosts-480818.jpeg" 
        } 
        ["type"]=> array(1) { 
            [123]=> string(10) "image/jpeg" 
        } 
        ["tmp_name"]=> array(1) { 
            [123]=> string(14) "/tmp/phpKjU2TR" 
        } 
        ["error"]=> array(1) { 
            [123]=> int(0) 
        } 
        ["size"]=> array(1) { 
            [123]=> int(129164) 
        } 
    } 
}*/

if (!empty($_POST)) {
    $date = date('Y-m-d', $_POST['formGenerateUts']);

    $teams = isset($_POST['team']) ? $_POST['team'] : array();
    foreach ($teams as $teamId => $post) {

        $query = new Nathejk_Klan;
        $query->id = $teamId;
        $team = $query->findOne();
        if ($team) {
            $checkIn = new Nathejk_CheckIn;
            $checkIn->teamId = $team->id;
            $checkIn->memberId = $postuser->id;//getCheckInByTypeName($USER->username);
            $checkIn->load(false);
            $checkIn->typeName = $postuser->title;
            if (!empty($post['inDate']) && !empty($post['inTime'])) {
                $checkIn->createdUts = strtotime("{$post['inDate']} {$post['inTime']}");
            }
            if (!empty($post['outDate']) && !empty($post['outTime'])) {
                $checkIn->outUts = strtotime("{$post['outDate']} {$post['outTime']}");
            }
            $checkIn->remark = $post['remark'];
            $checkIn->save();

            if (isset($_FILES['photo']['tmp_name'][$team->id])) {
                $tmpName = $_FILES['photo']['tmp_name'][$team->id];
                if (is_uploaded_file($tmpName)) {
                    $content = file_get_contents($tmpName);
                    if (imagecreatefromstring($content)) {
                        $photo = new Nathejk_Photo;
                        $photo->source = $content;
                        if ($photo->save()) {
                            $team->photoId = $photo->id;
                            $team->save();
                        }
                    }
                }
            }
        }
    }
    Pasta_Http::exitWithRedirect('window-close.php');
}

if ($error) {
    $PAGE->assign('message', $error);
} elseif ($redirectUrl) {
    $PAGE->assign('redirectUrl', $redirectUrl);
}

$teams = array();
if (!empty($_GET['id'])) {
    foreach ($_GET['id'] as $teamIds) {
        $query = new Nathejk_Klan;
        $query->columnIn('id', $teamIds);
        $query->typeName = 'patrulje';
        $teams = $query->findAll();
    }
}

            $checkIn = new Nathejk_CheckIn;
            $checkIn->memberId = $postuser->id;//getCheckInByTypeName($USER->username);
            $PAGE->assign('checkIns', Pasta_Conversion::array2assoc($checkIn->findAll(), 'teamId'));
$PAGE->assign('days', array('2011-09-16' => 'fre', '2011-09-17' => 'lør', '2011-09-18' => 'søn'));
$PAGE->assign('teams', $teams);
$PAGE->display();
