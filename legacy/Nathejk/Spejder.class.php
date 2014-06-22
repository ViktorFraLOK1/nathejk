<?php

class Nathejk_Spejder extends Nathejk_Member
{
    public function getInfoText()
    {
        return 
            "Navn: " . $this->title . "\n" .
            "Fødselsdato: " . $this->birthDate . "\n" .
            "Egen telefon på Nathejk: " . $this->phone . "\n" .
            "Telefon til forældre under Nathejk: " . $this->contactPhone . "\n" .
            "Bemærkninger: " . $this->remark . "\n";
    }

    public function getInfo()
    {
        return array_merge(parent::getInfo(), array(
            'Navn' => "{$this->title} (" . ($this->returning ? 'ja' : 'nej') . ")",
            'Eget tlf.' => $this->spejderTelefon,
            'Kontakt tlf.' => $this->phone,
            'Fødselsdag' => $this->birthDate,
            'Bemærkninger' => $this->remark,
        ));
    }

    public function getAge()
    {
        $age = strtotime('2013-09-20') - strtotime($this->birthDate);
        return date('Y', $age) - 1970;
    }
}

?>
