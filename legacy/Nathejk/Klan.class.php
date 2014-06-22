<?php

class Nathejk_Klan extends Nathejk_Team
{
/*
    public function __construct()
    {
        $this->typeName = 'klan';
    }
*/
    public static function getByIdAndChecksum($id, $cs)
    {
        $query = new self;
        $query->id = $id;
        $query->deletedUts= 0;
        if ($team = $query->findOne()) {
            if ($team->checksum == $cs) {
                return $team;
            }
        }
        return null;
    }
    public function getCatchCount()
    {
        $query = new Nathejk_Senior;
        $query->teamId = $this->id;
        $memberIds = $query->findColumn('id');

        $query = new Nathejk_CheckIn;
        $query->columnIn('memberId', $memberIds);
        //$query->columnIn('typeName', array('qr', 'sms', 'web'));
        return $query->countAll();
    }
    public function getAllLoks()
    {
        $q = new Nathejk_Lok;
        $q->typeName = 'lok';
        $q->setOrderBySql('title');
        return $q->findAll();
    }

    public function sendConfirm() {}
    public function sendInvite()
    {
        //$url = "http://{$_SERVER['HTTP_HOST']}/senior?id=$this->id&cs=$this->checksum";
        $message = "For at starte tilmeldingen skal du følge nedenstående link\n\n{$this->frontendUrl}\n\nMed venlig hilsen\nNathejk";
        
        return $this->sendMail('Seniortilmelding', $message);
       /* require_once "Mail.php";
 
        $to = "$this->contactTitle <$this->contactMail>";
        $headers = array (
            'From'      => "Nathejk <nhteam@nathejk.dk>",
            'To'        => $to,
            'Subject'   => "Seniortilmelding",
        );
        $url = "http://{$_SERVER['HTTP_HOST']}/senior.php?id=$this->id&cs=$this->checksum";
        $message = utf8_decode("For at gå til tilmeldingen skal du følge nedenstående link. Udfyld så mange informationer om dine klanmedlemmer som muligt - du kan altid rette/tilføje informationer senere.\n\n$url\n\nMed venlig hilsen\nNathejk");
        
        $smtp = Mail::factory('smtp', array (
            'host'      => 'mail.authsmtp.com',
            'auth'      => true,
            'username'  => 'ac56310',
            'password'  => 'ejuxqwr5ptfcjn',
        ));
        file_get_contents('http://test.klanvildmule.dk/post.php?to=' . urlencode($to) . '&subject=Seniortilmelding&message=' . urlencode($message));
     //   $mail = @$smtp->send($to, $headers, $message);
     //   return $mail;
/*
        $user = new Enter_DefaultUser;
        $user->email = $this->contactMail;
        $user->name = $this->contactTitle;

        $mail = new Pasta_Mail;
        $mail->setFrom('nhteam@nathejk.dk', 'Nathejk');
        $mail->subject = 'Seniortilmelding';
        $url = "http://nathejk.kj.dev.peytz.dk/senior.php?id=$this->id&cs=$this->checksum";
        $mail->text = utf8_decode("For at starte tilmeldingen skal du følge nedenstående link\n\n$url\n\nMed venlig hilsen\nNathejk");
        $mail->sendToUser($user);
*/
    }
    public function getFrontendUrl()
    {
        return "http://{$_SERVER['HTTP_HOST']}/senior/$this->id:$this->checksum";
    }

    public function getMemberPrice()
    {
        return 195;
    }

    public function getMinMemberCount()
    {
        return 1;
    }
    public function getMaxMemberCount()
    {
        return 5;
    }


    public function getMemberClassName()
    {
        return 'Nathejk_Senior';
    }

    public function save()
    {
        if ($this->signupStatusTypeName == Nathejk_Team::STATUS_NEW) {
            if (!$this->agenda->isOpenForSeniorSignup()) {
                $this->signupStatusTypeName = Nathejk_Team::STATUS_HOLD;
            }
        }
        return parent::save();
    }
}

?>
