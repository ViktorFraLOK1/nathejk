<?php

class Nathejk_Slut extends Nathejk_Member
{
    public function isBandit()
    {
        return false;
    }

    public function scan(Nathejk_Team $team, $location)
    {
        if (!$team->finishUts) {
            $team->finishUts = time();
            $team->save();
        }
        return parent::scan($team, $location);
    }
}

