<?php

class Nathejk_Senior extends Nathejk_Member
{
    public function getInfoText()
    {
        return 
            "Navn: " . $this->title . "\n" .
            "Fødselsår: " . intval($this->birthDate) . "\n" .
            "Telefon: " . $this->phone . "\n" .
            "E-mail-adresse: " . $this->mail . "\n";
    }
    
    public function getInfo()
    {
        return array_merge(parent::getInfo(), array(
            'E-mail' => $this->mail,
            'Eget tlf.' => $this->phone,
            'Fødselsår' => intval($this->birthDate),
            'Bemærkninger' => $this->remark,
        ));
    }
    public function isBandit()
    {
        return true;
    }
}

?>
