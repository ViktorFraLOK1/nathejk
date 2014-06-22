<?php

class Nathejk_SignupControl
{
    protected $smarty;

    public function __construct()
    {
        $this->smarty = getSmarty();
        $this->smarty->setTemplateDir(__DIR__ . '/../../sites/tilmelding.nathejk.dk/markup/');

        $query = new Nathejk_Agenda;
        $agenda = $query->findOne();

        $this->smarty->assign(compact('agenda'));
    }

    public function front($site)
    {
        $query = new Nathejk_Agenda;
        $agenda = $query->findOne();
        if ($agenda->signupStartUts > time()) {
            $templateName = 'countdown.tpl';
        } else {
            $templateName = 'signup.tpl';
            $request = $site->getRequest();
            if ($typeName = $request->post('teamTypeName')) {
                $team = ($typeName == 'patrulje') ? new Nathejk_Patrulje : new Nathejk_Klan;
                $team->typeName = $typeName;
                $team->contactTitle = $request->post('contactName');
                $team->contactMail = $request->post('contactMail');
                $team->contactPhone = $request->post('contactPhone');
                $team->createdUts = time();
                $team->ip = $request->server('REMOTE_ADDR');
                if ($team->save()) {
                    $team->sendInvite();
                    $team->sendVerifyPhoneSMS();
                    $templateName = 'verify.tpl';
                    $this->smarty->assign('team', $team);
                }
            }
        }
        return $this->smarty->fetch($templateName);
    }
    
    public function liga($site)
    {
        $token = $site->getRequest()->get('token');
        $url = "http://liga.adventurespejd.dk/api/token/$token/";
        $json = file_get_contents($url);
        $data = json_decode($json);

        $query = new Nathejk_Patrulje;
        $query->ligaNumber = $data->league_id;
        if (!$team = $query->findOne()) {
            $team = new Nathejk_Patrulje;
            $team->title = $data->patrol->name;
            $team->gruppe = $data->patrol->group . ' / ' . $data->patrol->division;
            $team->korps = $data->patrol->core;
            $team->ligaNumber = $data->league_id;
            $team->ligaNumberVerified = 1;
            $team->memberCount = count($data->patrol_members);

            $team->createdUts = time();
            $team->ip = $site->getRequest()->server('REMOTE_ADDR');
            if (!$team->save()) {
                die('500: Kontakt din tropsleder');
            }
            $memberFields = array('title', 'address', 'postalCode', 'mail', 'phone', 'contactPhone', 'birthDate'); //, 'returning');
            foreach ($data->patrol_members as $m) {
                $member = $team->getMemberByIdOrNew(0);
                $member->title = $m->name;
                $member->phone = $m->phone;
                $member->birthDate = $m->birth_date;
                $member->mail = $m->email;
                $member->save();
            }
        }
        //$team->sendInvite();
        Pasta_Http::exitWithRedirect($team->frontendUrl);
    }
    
    public function verify($site)
    {
        if ($teamId = $site->getRequest()->post('teamId')) {
            $query = new Nathejk_Klan;
            $query->id = $teamId;
            if ($team = $query->findOne()) {
                if (intval($team->phoneVerifyCode) == intval($site->getRequest()->post('phoneVerifyCode'))) {
                    $team->verifiedUts = time();
                    $team->save();
                    Pasta_Http::exitWithRedirect($team->frontendUrl);
                }
                //wrong code, try again
                $this->smarty->assign(compact('team'));
                return $this->smarty->fetch('verify.tpl');
            }
        }
    }
    
    public function show($site, $teamId, $checksum)
    {
        $query = new Nathejk_Klan;
        $query->id = $teamId;
        $query->deletedUts = 0;
        $team = $query->findOne();
        if ($team && $team->checksum == $checksum) {
            if (!$team->openedUts) {
                $team->openedUts = time();
                $team->save();
            }
            $this->smarty->assign(compact('team'));
            return $this->smarty->fetch('hold.tpl');
        }
    }

    public function save($site, $teamId, $checksum)
    {
        $query = new Nathejk_Klan;
        $query->id = $teamId;
        $query->deletedUts = 0;
        $team = $query->findOne();
        if (!$team || $team->checksum != $checksum) {
            return 500;
        }
        $contactFields = array('contactTitle', 'contactAddress', 'contactPostalCode', 'contactMail', 'contactPhone');
        $teamFields = array('title', 'gruppe', 'korps', 'ligaNumber', 'contactRole', 'memberCount', 'lokNumber', 'arrivalName');
        foreach (array_merge($contactFields, $teamFields) as $field) {
            $value = $site->getRequest()->post($field);
            if ($value) {
                $team->$field = $value;
            }
        }
        $memberFields = array('title', 'address', 'postalCode', 'mail', 'phone', 'contactPhone', 'birthDate'); //, 'returning');
        $discardedIds = array();
        foreach ($site->getRequest()->post('members') as $index => $post) {
            if ($index >= $team->memberCount) {
                $discardedIds[] = $post['id'];
                continue;
            }
            $member = $team->getMemberByIdOrNew($post['id']);
            if (!$member) {
                continue;
            }
            foreach ($memberFields as $field) {
                if (isset($post[$field])) {
                    $member->$field = $post[$field];
                }
            }
            $member->returning = isset($post['returning']) ? 1 : 0;
            $member->save();
            if ($files = $site->getRequest()->file('photos')) {
                if (isset($files['tmp_name'][$member->id])) {
                    if ($photo = Nathejk_Photo::createFromUpload($files['tmp_name'][$member->id])) {
                        $photo->memberId = $member->id;
                        $photo->save();
                        $member->photoId = $photo->id;
                        $member->save();
                    }
                }
            }
        }
        $team->deleteMembers($discardedIds);
        if (!$team->finishedUts) {
            $team->finishedUts = time();
        }
        $team->lastModifyUts = time();
        $team->save();

            if ($team->typeName == 'patrulje') {
                if ($team->signupStatusTypeName == Nathejk_Team::STATUS_PAID && $team->memberCount > $team->paidMemberCount) {
                    $team->signupStatusTypeName = Nathejk_Team::STATUS_PAY;
                }
            }
        switch ($team->signupStatusTypeName) {
        case Nathejk_Team::STATUS_PAY : $template = 'pay.tpl'; break;
        case Nathejk_Team::STATUS_HOLD : $template = 'late.tpl'; break;
        default : $template = 'update.tpl';
        }

        $this->smarty->assign(compact('team'));
        return $this->smarty->fetch($template);

    }

    protected function getTeam($teamId, $checksum)
    {
        $query = new Nathejk_Klan;
        $query->id = $teamId;
        $query->deletedUts = 0;
        $team = $query->findOne();
        if (!$team || $team->checksum != $checksum) {
            return null;
        }
        return $team;
    }
    public function upload($site, $teamId, $checksum, $memberId)
    {
        if (!$team = $this->getTeam($teamId, $checksum)) {
            return 500;
        }
        $member = $team->getMemberById($memberId);
        $this->smarty->assign(compact('member'));
        return $this->smarty->fetch('upload.tpl');
    }

    public function photo($site, $teamId, $checksum, $memberId)
    {
        if (!$team = $this->getTeam($teamId, $checksum)) {
            return 500;
        }
        if (!$member = $team->getMemberById($memberId)) {
            return 500;
        }
        if ($photo = $member->getPhoto()) {
            imagejpeg(imagecreatefromstring($photo->source));
            return null;
        }
        Pasta_Http::exitWithRedirect('/img/ghost.jpg');
    }

    public function callback($site)
    {
        $body = json_decode(file_get_contents('php://input'), true);
        if ($body['signature'] == hash_hmac('sha256', $body['timestamp'].$body['token'], 'ORh4MhK72bxAkhLw')) {  
            include_once("ContextIO/class.contextio.php");

            $contextIO = new ContextIO('etzt1mj4', 'ORh4MhK72bxAkhLw');
            if (!$contextIO->listAccounts()) {
                return;
            }
            $account = array_shift($contextIO->listAccounts()->getData());
            $accountId = $account['id'];

            $msgId = $body['message_data']['email_message_id'];
            $args = array('email_message_id' => $msgId, 'include_body' => 1);
            $mail = $contextIO->getMessage($accountId, $args)->getData();
            $body = $mail['body'][0]['content'];

            if (preg_match('/\[(\d+)\].*Antal: (\d) s/i', str_replace("\n", ' ', $body), $match)) {
                list(, $teamId, $memberCount) = $match;
                $query = new Nathejk_Klan;
                $query->id = $teamId;
                $team = $query->findOne();
                $team->addPayment($memberCount * $team->memberPrice, time(), $memberCount);
            }
            $log = new Nathejk_Httplog;
            $log->uts = time();
            $log->request = $body;
            $log->save();
        }
    }

     /*   if ($_POST && false) {
            $contactFields = array('contactTitle', 'contactAddress', 'contactPostalCode', 'contactMail', 'contactPhone');
            $teamFields = array('title', 'gruppe', 'korps', 'ligaNumber', 'contactRole', 'memberCount', 'lokNumber', 'arrivalName');
            foreach (array_merge($contactFields, $teamFields) as $field) {
                if (isset($_POST[$field])) {
                    $team->$field = $_POST[$field];
                }
            }
            $team->advSpejdNightCount = isset($_POST['advSpejdNight']) ? intval($_POST['advSpejdNightCount']) : 0;
            $memberFields = array('title', 'address', 'postalCode', 'mail', 'phone', 'contactPhone', 'birthDate'); //, 'returning');
            foreach ($_POST['members'] as $index => $post) {
                if ($index >= $team->memberCount) continue;
                $member = $team->getMemberById($post['id']);
                if (!$member) $member = new $team->memberClassName;
                foreach ($memberFields as $field) {
                    if (!isset($post[$field])) {
                        continue;
                    }
                    $member->$field = $post[$field];
                }
                $member->returning = isset($post['returning']) ? 1 : 0;
                if ($index == 0 && isset($_POST['contactAsMember'])) {
                    foreach ($contactFields as $i => $field) {
                        $memberField = $memberFields[$i];
                        $member->$memberField = $_POST[$field];
                    }
                }
                $team->addMember($member);
            }
            if (!$team->finishedUts) {
                $team->finishedUts = time();
            }
            $team->save();

            if ($team->typeName == 'patrulje') {
                if ($team->signupStatusTypeName == Nathejk_Team::STATUS_PAID && $team->memberCount != $team->paidMemberCount) {
                    $team->signupStatusTypeName = Nathejk_Team::STATUS_PAY;
                }
            }
            switch ($team->signupStatusTypeName) {
            case Nathejk_Team::STATUS_PAY : $template = 'pay.html'; break;
            case Nathejk_Team::STATUS_HOLD : $template = 'late.html'; break;
            default : $template = 'update.html';
            }

        }
    }*/

}
