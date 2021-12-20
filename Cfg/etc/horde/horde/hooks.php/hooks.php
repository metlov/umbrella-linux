<?php
class Horde_Hooks
{
    public function prefs_init($pref, $value, $username, $scope_ob)
    {
        switch ($pref) {
        case 'from_addr':
            if (is_null($username)) {
                return $value;
            }

            $factory = $GLOBALS['injector']->getInstance('Horde_Core_Factory_Ldap');
            $ldap = $factory->create('horde', $factory->getConfig('ldap'));
            try {
                $result = $ldap->search(
                    $GLOBALS['conf']['ldap']['user']['basedn'],
                    Horde_Ldap_Filter::create('uid', 'equals', $username),
                    array('attributes' => array('mail'))
                );
                if ($result->count()) {
                    $entry = $result->shiftEntry();
                    return $entry->getValue('mail', 'single');
                }
            } catch (Horde_Ldap_Exception $e) {
            }

            return $value;

        case 'fullname':
            if (is_null($username)) {
                return $value;
            }

            $factory = $GLOBALS['injector']->getInstance('Horde_Core_Factory_Ldap');
            $ldap = $factory->create('horde', $factory->getConfig('ldap'));
            try {
                $result = $ldap->search(
                    $GLOBALS['conf']['ldap']['user']['basedn'],
                    Horde_Ldap_Filter::create('uid', 'equals', $username),
                    array('attributes' => array('cn', 'sn', 'givenName'))
                );
                if ($result->count()) {
                    $entry = $result->shiftEntry();
                    $name = '';
                    if ($entry->getValue('givenName', 'single')) {
                        $name = $entry->getValue('givenName', 'single');
                    }
                    if ($entry->getValue('sn', 'single')) {
                        $name = $name . ' ' . $entry->getValue('sn', 'single');
                    }
                    if ($name == '') {
                        $name = $entry->getValue('cn', 'single');
                    }
                    if ($name == '') {
                        return $username;
                    }
                    return $name;
                }
            } catch (Horde_Ldap_Exception $e) {
            }

            return $username;
        }
    }
}
