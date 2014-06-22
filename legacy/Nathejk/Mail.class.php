<?php

class Nathejk_Mail extends Pasta_TableRow
{
    private $_team = null;
    private $_rcpts = array();

    public static function getVariableDescriptions()
    {
        return array(
            'id' => 'Holdets id-nummer, både klaner og patruljer har fået tildelt et fortløbende id-nummer fra samme pulje i den rækkefølge de har tilmeldt sig',
            'number' => 'Patruljens holdnummer, dette nummer er tildelt fortløbende og findes kun hos spejder- og superseniorpatruljer.',
            'contact' => 'Navnet på holdets kontaktperson',
            'mail' => 'Kontaktpersonens e-mail-adresse',
            'phone' => 'Kontaktpersonens telefonnummer',
            'role' => 'Kontaktpersonens rolle i forhold til patruljen',
            'link' => 'Linket til den side hvor kontaktpersonen kan ændre i de indtastede oplysninger, tilføje/slette deltagere.',
            'team' => 'Patruljenavn / Klannavn',
            'total' => 'Det samlede deltagergebyr for hele holdet.',
            'count' => 'Antal deltagere',
            'members' => 'Deltagernes tilmeldingsoplysninger',
        );
    }

    public function getVariableValues()
    {
        return array(
            'id' => $this->team->id,
            'number' => $this->team->teamNumber,
            'contact' => $this->team->contactTitle,
            'mail' => $this->team->contactMail,
            'phone' => $this->team->contactPhone,
            'role' => $this->team->contactRole,
            'link' => $this->team->frontendUrl,
            'team' => $this->team->title,
            'total' => $this->team->totalPrice,
            'count' => $this->team->memberCount,
            'members' => $this->team->membersInfoText,
        );
    }

    public function getTeam()
    {
        if (!$this->_team) {
            $query = new Nathejk_Klan;
            $query->id = $this->teamId;
            $this->_team = $query->findOne();
        }
        return $this->_team;
    }

    public function setRcpts(array $rcpts)
    {
        $this->rcptTo = implode(', ', $rcpts);
    }

    public function setBody($text)
    {
        $keys = array_map(create_function('$x', 'return "#" . strtoupper($x) . "#";'), array_keys($this->variableValues));
        $this->setColumn('body', str_replace($keys, $this->variableValues, $text));
    }
    
    public function isValid()
    {
        if ($this->exists) {
            $this->errorString = 'Denne e-mail kan ikke gensendes';
            return false;
        }
        if (!$this->team) {
            $this->errorString = 'Holdet som mailen skal sendes til er ikke angivet.';
            return false;
        }
        if (trim($this->subject) == '') {
            $this->errorString = 'Emnefelt er ikke udfyldt';
            return false;
        }
        if (trim($this->body) == '') {
            $this->errorString = 'Indholdsfelt er ikke udfyldt';
            return false;
        }
        return true;
    }
            


    public function send()
    {
        $this->sendUts = time();
        $this->mailFrom = "Nathejk <tilmeld@nathejk.dk>";

        require_once "/usr/share/php/Mail.php";
 
        $headers = array (
            'From'      => $this->mailFrom,
            //'To'        => $this->team->contactMail,
            'To'        => $this->rcptTo,
            'Subject'   => $this->subject,
            'Date'      => date('r'),
            'Content-Type' => "text/plain; charset=UTF8",

        );
        $smtp = Mail::factory('mail');
         
        $message = $this->body;
        $recipients = array();
        foreach (explode(',', $this->rcptTo) as $rcpt) {
            if (preg_match('/^[^<]*<(.+)>$/', trim($rcpt), $match)) {
                $recipients[] = $match[1];
            }
        }
        $mail = $smtp->send($recipients, $headers, $message);
        if ($mail !== true) {
            $this->smtpErrorMessage = $mail->getMessage();
        }
        $this->save();

        return $mail;
    }
}

?>
