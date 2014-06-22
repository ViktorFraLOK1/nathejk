<?php
$PAGE = new Nathejk_Page();

if (!empty($_POST)) {
    if ($USER) {
        if ($USER->username == 'post' && !isset($_POST['post'])) {
            $q = new Nathejk_Post;
            $q->typeName = 'post';
            $q->deletedUts = 0;
            $postIds = $q->findColumn('id');
            $q = new Nathejk_Senior;
            $q->columnIn('teamId', $postIds);
            $q->deletedUts = 0;
            
            $PAGE->assign('postUsers', Pasta_Conversion::array2assoc($q->findAll(), 'id', 'title'));

        } else {
            if (isset($_POST['post'])) {
                setcookie('post', $_POST['post']);
            }
            if (!empty($_POST['goto'])) {
                Pasta_Http::exitWithRedirect($_POST['goto']);
            } else {
                Pasta_Http::exitWithRedirect('/');
            }
        }
    } else {
        $PAGE->assign('systemMessageHeader', 'Login failed');
        $PAGE->assign('systemMessage', 'Try using the lost password function below.');
    }
}

if (isset($_GET['lostpass'])) {
    $PAGE->assign('systemMessageHeader', 'Email sent');
    $PAGE->assign('systemMessage', 'An email with you username and new password has been sent to you mailbox.');
}
$PAGE->display();
?>
