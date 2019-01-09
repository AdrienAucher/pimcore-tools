<?php
/**
 * Active Publishing - All right reserved
 *
 * @license Copyright (C) 2018 Active Publishing
 * @author Active Publishing <contact@active-publishing.fr>
 */

namespace ActivePublishing\Tool;

use Pimcore\Tool as PimcoreTool;
use Pimcore\Model\User as PimcoreUser;

/**
 * Class User
 * @package ActivePublishing\Tool
 */
class User
{
    /**
     * @return bool|PimcoreUser
     */
    public static function getCurrentUser()
    {
        try {
            $currentUser = PimcoreTool\Admin::getCurrentUser();
            if ($currentUser instanceof PimcoreUser) {
                return $currentUser;
            }
        } catch (\Exception $ex) {
            ApTool::Log("activepublishing", $ex->getMessage());
        }

        return false;
    }

    /**
     * @return array
     */
    public static function getRolesFromCurrentUser()
    {
        if (($user = self::getCurrentUser()) && ($user instanceof PimcoreUser)) {
            return $user->getRoles();
        }

        return [];
    }

    /**
     * @param $roleName
     *
     * @return bool
     */
    public static function hasRoleFromCurrentUser ($roleName)
    {
        $roles = self::getRolesFromCurrentUser();

        if (!empty($roles)) {
            foreach ($roles as $roleId) {
                $role = PimcoreUser\Role::getById($roleId);
                if ($role->getName() == trim($roleName)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $name
     *
     * @return bool|PimcoreUser\AbstractUser|PimcoreUser\Folder
     */
    public static function getOrCreateUserFolder ($name)
    {
        $folder = PimcoreUser\Folder::getByName($name);
        if (!$folder) {
            $folder = new PimcoreUser\Folder();
            $folder->setName($name);
            $folder->setParentId(0);

            try {
                $folder->save();
            } catch (\Exception $ex) {
                return false;
            }
        }

        return $folder;
    }

    /**
     * @param $name
     * @param int $pid
     * @param bool $active
     * @param array $config
     *
     * @return null|PimcoreUser
     * @throws \Exception
     */
    public static function getOrCreateUser ($name, $pid = 0, $active = false, $config = [])
    {
        $user = PimcoreUser::getByName($name);

        if (!$user instanceof PimcoreUser) {
            $user = new PimcoreUser();
            $user->setName($name);
            $user->setParentId($pid);
            $user->setActive($active);
            $user->setPassword(PimcoreTool\Authentication::getPasswordHash($name, $name));

            if (!empty($config)) {
                foreach ($config as $key => $value) {
                    switch ($key) {
                        case "mail" : 
                            $user->setEmail(trim($value));
                            break;

                        case "language" : 
                            $user->setLanguage(trim($value));
                            break;

                        case "lastname" : 
                            $user->setLastname(trim($value));
                            break;

                        case "firstname" : 
                            $user->setFirstname(trim($value));
                            break;

                        default;
                    }
                }
            }

            try {
                $user->save();
            } catch (\Exception $ex) {
                APTool::log("activepublishing", $ex->getMessage());
                return null;
            }
        }

        return $user;
    }

}
