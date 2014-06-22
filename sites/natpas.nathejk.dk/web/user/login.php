<?php
$PAGE = new Sbs_Page();

if (!empty($_POST)) {
    if ($USER) {
        if (!empty($_POST['goto'])) {
            Pasta_Http::exitWithRedirect($_POST['goto']);
        } else {
            Pasta_Http::exitWithRedirect('/');
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