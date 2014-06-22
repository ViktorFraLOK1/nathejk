<?php

$PAGE = new Pasta_Page();

if (!empty($_POST)) {
    $email = !empty($_POST['email']) ? $_POST['email'] : false;
    if ($email) {
        $u = Enter_DefaultUser::getByEmail($email);
        if ($u) {
            $u->sendLostPasswordMail();
            Pasta_Http::exitWithRedirect('login.php?lostpass');
        }
    }
    $PAGE->assign('systemMessageHeader', 'Email not found');
    $PAGE->assign('systemMessage', 'Please enter a valid email.');
}
$PAGE->display();
?>
