<?php

if ($_SERVER['QUERY_STRING'] == 'json') {
    $members = array();
    $teams = array();

    $query = new Nathejk_Klan;
    $query->addWhereSql("typeName NOT IN ('klan', 'patrulje')");
    foreach ($query->findAll() as $team) {
        $members = array();
        foreach ($team->members as $member) {
            $members[] = array('id' => $member->id, 'title' => $member->title, 'phone' => $member->phone);
        }
            
        $teams[$team->typeName][] = array('id' => $team->id, 'title' => $team->title, 'members' => $members);
    }
    $teamTypes = array();
    $groups = array('lok' => 'Banditter', 'guide' => 'Guides', 'start' => 'Startpost', 'post1' => 'Postlinie 1', 'post2' => 'Postlinie 2', 'oplev' => 'Oplevelsespost', 'slut' => 'Mål', 'logistik' => 'Logistik', 'andet' => 'Andet');
    foreach ($groups as $typeName => $title) {
    //foreach ($teams as $typeName => $team) {
        
        $teamTypes[] = array('typeName' => $typeName, 'title' => $title, 'teams' => isset($teams[$typeName]) ? $teams[$typeName] : array());
    }

    header('Content-Type: application/json');
    print json_encode($teamTypes);
    exit;
}
if ($_POST) {
    foreach ($_POST as $m) {
        $json = json_decode($m);
        $query = new Nathejk_Klan;
        $query->id = $json->teamId ? $json->teamId : 0;
        $query->deletedUts = 0;
        $team = $query->findOne();
        if (!$team) continue;

        $member = new Nathejk_Senior;
        if (isset($json->id)) {
            $member->id = $json->id;
            $member = $member->findOne();
        } 
        $member->teamId = $json->teamId;
        $member->title = $json->name;
        $member->phone = $json->phone;
        $member->save();
    }
    Pasta_Http::exitWithRedirect('');
}

$query = new Nathejk_Senior;
$query->teamId = 0;
$query->memberId = 0;
$query->setOrderBySql('id DESC');
$member = $query->findAll();

$PAGE = new Nathejk_Page;
$PAGE->assign('activePhone', true);
$PAGE->assign(compact('member'));
$PAGE->display();

?>
