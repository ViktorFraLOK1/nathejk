<?php

class Nathejk_ScanControl
{
    protected $smarty;

    public function __construct()
    {
        $this->smarty = getSmarty();
        $this->smarty->setTemplateDir(__DIR__ . '/../../markup/');

        $query = new Nathejk_Agenda;
        $agenda = $query->findOne();

        $this->smarty->assign(compact('agenda'));
    }

    public function getLoggedInUser()
    {
        $cookie = isset($_COOKIE['nh']) ? $_COOKIE['nh'] : ':';
        list($phone, $checksum) = explode(':', $cookie);

        $member = null;
        if (!empty($phone)) {
            $query = new Nathejk_Senior;
            $query->phone = $phone;
            $query->deletedUts = 0;
            $member = $query->findOne();
        }
        if (!$member && !empty($_POST['phone'])) {
            $query = new Nathejk_Senior;
            $query->phone = $_POST['phone'];
            $query->deletedUts = 0;
            $members = $query->findAll();
            if (count($members) == 1) {
                $member = array_shift($members);
            }
        }
        if (!$member) {
            $this->smarty->display('scan/login.tpl');
        } else {
            $this->smarty->assign('member', $member);
            setcookie('nh', $member->phone . ':' . md5('kaal' . $member->id), time() + 60*60*24*3, '/');
        }
        return $member;
    }

    public function status()
    {
        $this->smarty->display('scan/status.tpl');
    }

    public function contact($site, $teamId, $checksum)
    {
        if (!$user = $this->getLoggedInUser()) {
            return;
        }

        $query = new Nathejk_Patrulje;
        $query->id = $teamId;
        $query->deletedUts = 0;
        $team = $query->findOne();
        
        if (!$team) {
            $this->smarty->display('scan/error.tpl');
            return;
        }
        $team = $team->parentTeamId ? $team->parentTeam : $team;

        $this->smarty->assign('team', $team);

        $loc = $site->getRequest()->get('location');
        if (empty($loc)) {
            $this->smarty->display('scan/coordinates.tpl');
        } else {
            $user->scan($team, $loc);
            $this->smarty->display('scan/contact.tpl');
        }
    }
}
