<?php

class Nathejk_Start extends Nathejk_Member
{
    public function isBandit()
    {
        return false;
    }

    public function scan(Nathejk_Team $team, $location)
    {
        if (!$team->startUts) {
            $team->startUts = time();
            $team->save();
        }
        return parent::scan($team, $location);
    }
}

