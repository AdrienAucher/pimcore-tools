<?php
/**
 * Active Publishing - All right reserved
 *
 * @license Copyright (C) 2018 Active Publishing
 * @author Active Publishing <contact@active-publishing.fr>
 */

namespace ActivePublishing\Tool;

use Pimcore\Version;


/**
 * Class InstallerTool
 * @package ActivePublishing\Tool
 */
class Installer
{

    CONST UPDATE_SCRIPTS_DIR = 'Resources/update-scripts';

    /**
     * @param $bundleId
     *
     * @return string
     */
    public static function getUpdateScriptsPath ($bundleId)
    {
        return ApTool::getBundlePath($bundleId) . self::UPDATE_SCRIPTS_DIR;
    }

    /**
     * @param $checkVersion
     * @return bool
     */
    public static function allowInstallFromPimcoreVersion ($checkVersion)
    {
        $pVersion = str_replace("v", "", Version::getVersion());
        $partVersion = explode(".", $pVersion);
        $partMinVersionBundle = explode(".", $checkVersion);

        if (intval($partVersion[0]) < intval($partMinVersionBundle[0])) { // Major version
            return false;
        } else {
            if (intval($partVersion[1]) < intval($partMinVersionBundle[1])) { // Medium version
                return false;
            } else {
                if (intval($partVersion[2]) < intval($partMinVersionBundle[2])) { // Minor version
                    return false;
                }
            }
        }

        return true;
    }

}