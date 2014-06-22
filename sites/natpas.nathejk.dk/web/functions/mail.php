<?php
$PAGE = new Nathejk_Page(true);

$error = '';
$redirectUrl = '';



$query = new Nathejk_MailTemplate;
$query->id = isset($_POST['mailTemplateId']) ? intval($_POST['mailTemplateId']) : 0;
$mailTemplate = $query->findOne();
if (isset($_POST['save'])) {
    if (!$mailTemplate) {
        $mailTemplate = new Nathejk_MailTemplate;
    }
    if ($mailTemplate->title == '') {
        $mailTemplate->title = trim($_POST['subject']);
    }
    $mailTemplate->subject = trim($_POST['subject']);
    $mailTemplate->body = trim($_POST['body']);

    if ($mailTemplate->editable) {
        if ($mailTemplate->body == '') {
            $mailTemplate->delete();
        } else {
            $mailTemplate->save();
        }
    }
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

if (isset($_POST['send']) && trim($_POST['subject']) != '' && trim($_POST['body']) != '') {
    ignore_user_abort(true);
    foreach ($teams as $team) {
        $m = $team->sendMail(trim($_POST['subject']), trim($_POST['body']), isset($_POST['sendToAll']));
    }
    Pasta_Http::exitWithRedirect('window-close.php');
}

if ($error) {
    $PAGE->assign('message', $error);
} elseif ($redirectUrl) {
    $PAGE->assign('redirectUrl', $redirectUrl);
}

$groupedMailTemplates = array('- ingen -');
$query = new Nathejk_MailTemplate;
$query->setOrderBySql('optgroup, sortOrder');
foreach ($query->findAll() as $template) {
    $optgroup = $template->optgroup ? $template->optgroup : 'Manuelt oprettet';
    $groupedMailTemplates[$optgroup][$template->id] = $template->title;
}

$PAGE->assign('mailTemplates', $groupedMailTemplates);
$PAGE->assign('mailTemplate', $mailTemplate);
$PAGE->assign('variableDescriptions', Nathejk_Mail::getVariableDescriptions());

$PAGE->assign('teams', $teams);
$PAGE->display();
