<?php

class Nathejk_MailTemplate extends Pasta_TableRow
{
    public function getEditable()
    {
        return $this->optgroup == '';
    }
}

?>
