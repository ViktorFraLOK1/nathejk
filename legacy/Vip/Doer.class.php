<?php

/**
 * @package Vip
 */
abstract class Vip_Doer extends Pasta_TableRow
{
    abstract function getPermissionParentId();

    /**
     * @return  bool
     */
    public function save()
    {
        if (!$this->getId()) {
            $this->setColumn('id', $this->getNextId('doer'));
        }
        return parent::save();
    }

    function getPermissionParent()
    {
        $id = $this->getPermissionParentId();
        return $id ? Vip_Doer::getById($id) : null;
    }

    /**
     * @param   mixed  an action name, or an action id
     * @param   int    a parameter value
     * @return  bool
     */
    function hasPermission($actionIdOrName, $parameter = 0)
    {
        // Cache former resolved permission questions
        static $permissions;
        // Check cached permission questions
        if (isset($permissions[$this->id][$actionIdOrName][$parameter])) {
            return $permissions[$this->id][$actionIdOrName][$parameter];
        }

        // By default, nobody has permission to anything. This can be
        // overridden by specifying the default value with doerId=0
        $allow = false;

        // For users, this is ($userId, $customerId, 0)
        // For customers, this is ($customerId, 0, 0)
        $doerIds = array(
            $this->getId(),
            $this->getPermissionParentId(),
            0
        );
        if (is_numeric($actionIdOrName)) {
            $actionId = intval($actionIdOrName);
        } else {
            $action = Vip_Action::getByName($actionIdOrName);
            if (!$action) {
                trigger_error("Action '$actionIdOrName' not found");
                return false;
            }
            $actionId = $action->getId();
        }

        $permission = new Vip_Permission();
        $db = $permission->getPearDb();
        $whereSql = "actionId = '$actionId'
            AND doerId IN (" . implode(', ', $doerIds) . ")
            AND parameterValue IN (" . $db->quote($parameter) . ", 0)";
        $permission->addWhereSql($whereSql);
        $orderBySql = "FIELD(doerId, " . implode(', ', $doerIds) . "),
            parameterValue DESC";
        $permission->setOrderBySql($orderBySql);
        $permission->load(false);

        $permissions[$this->id][$actionIdOrName][$parameter] = $permission->exists() && $permission->isAllowed();
        return $permissions[$this->id][$actionIdOrName][$parameter];
    }

    /**
     * @param   mixed  an action name, or an action id
     * @return  array  array of parameters
     */
    function getPermittedParameterValues($actionIdOrName)
    {
        $action = Vip_Action::getByIdOrName($actionIdOrName);
        //TODO could probably be optimized to only use one SQL statement
        $values = array();
        foreach ($action->getParameterValues() as $value) {
            if ($this->hasPermission($action->getId(), $value)) {
                $values[] = $value;
            }
        }
        return $values;
    }

    /**
     * @param   mixed  an action name, or an action id
     * @return  array  array of parameter objects
     */
    function getPermittedParameterValueNamePairs($actionIdOrName)
    {
        $action = Vip_Action::getByIdOrName($actionIdOrName);
        //TODO could probably be optimized to use only one SQL statement
        $names = array();
        foreach ($action->getParameterValueNamePairs() as $value => $name) {
            if ($this->hasPermission($action->getId(), $value)) {
                $names[$value] = $name;
            }
        }
        return $names;
    }

    /**
     * @param   mixed  an action name, or an action id
     * @return  array  array of parameter objects
     */
    function getPermittedParameterObjects($actionIdOrName)
    {
        $action = Vip_Action::getByIdOrName($actionIdOrName);
        $methodName = $action->getParameterValueMethod();
        //TODO could probably be optimized to use only one SQL statement
        $objects = array();
        foreach ($action->getParameterObjects() as $object) {
            if ($this->hasPermission($action->getId(), $object->$methodName())) {
                $objects[] = $object;
            }
        }
        return $objects;
    }

    static function getById($id)
    {
        $doer = Vip_User::getById($id);
        if (!$doer) {
            $doer = Vip_Customer::getById($id);
        }
        return $doer;
    }
}

?>
