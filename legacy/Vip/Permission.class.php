<?php

/**
 * @package Vip
 */
class Vip_Permission extends Pasta_TableRow
{
    function getKeyColumns($table = null)
    {
        return array('actionId', 'doerId', 'parameterValue');
    }

    /**
     * @deprecated
     * @return  int
     */
    function getParameter()
    {
        return $this->getParameterValue();
    }
    function setParameter($value)
    {
        $this->setParameterValue($value);
    }

    function isAllowed()
    {
        return (bool) $this->getColumn('allowed');
    }
    function setAllowed($value)
    {
        $this->setColumn('allowed', $value ? '1' : '0');
    }
}

?>
