<?php
/**
 * Active Publishing - All right reserved
 *   Full copyright and license information is available in
 *   LICENSE.md which is distributed with this source code.
 * @copyright Copyright (c) Active Publishing (https://activepublishing.fr)
 * @license Creative Common CC BY NC-ND 4.0
 * @author Active Publishing <contact@active-publishing.fr>
 */

namespace ActivePublishing\Tool;

use Pimcore\Extension\Bundle\PimcoreBundleManager;
use Pimcore\Model;
use Pimcore\Model\Translation;
use Pimcore\Log\Simple as Logger;
use Pimcore\Version;

/**
 * Class ApTool
 * @package ActivePublishing\Tool
 */
class ApTool
{
    /**
     * @param $bundleId
     *
     * @return mixed
     */
    public static function getBundlePath($bundleId)
    {
        return  \Pimcore::getContainer()->get("kernel")->locateResource("@" . $bundleId);
    }

    /**
     * @deprecated replace by bundleIsActive() function
     *
     * @param $InstallerNamespace
     * 
     * @return bool
     */
    public static function bundleIsInstalled ($InstallerNamespace)
    {
        if (\Pimcore::getContainer()->has($InstallerNamespace)) {
            return \Pimcore::getContainer()->get($InstallerNamespace)->isInstalled();
        }

        return false;
    }

    /**
     * @param string $id
     * @param bool $onlyInstalled
     * @return bool
     */
    public static function bundleIsActive (string $id, bool $onlyInstalled = true)
    {
        try {
            $bundleManager = \Pimcore::getContainer()->get("pimcore.extension.bundle_manager");
            if ($bundleManager instanceof PimcoreBundleManager) {
                $bundle = $bundleManager->getActiveBundle($id, $onlyInstalled);
                return true;
            }
        } catch (\Exception $ex) {
            ApTool::log("activepublishing", $ex->getTraceAsString());
        }

        return false;
    }

    /**
     * @param $key
     * @param $parent
     * @param $type
     *
     * @return mixed|string
     */
    public static function getUniqueKey($key, $parent, $type)
    {
        if ($parent instanceof Model\Document || $parent instanceof Model\Asset || $parent instanceof Model\Object) {
            $key = Model\Element\Service::getValidKey($key, $type);
            $i = 1;

            while (Model\Element\Service::pathExists($parent->getFullPath() . DIRECTORY_SEPARATOR . $key)) {
                $key = $key . "_" . $i;
                $i++;
            }
        }

        return $key;
    }

    /**
     * @param $key
     *
     * @return null|string
     */
    public static function ts($key)
    {
        try {
            return Translation\Admin::getByKeyLocalized($key, true, true);
        } catch (\Exception $ex) {
            self::log("activepublishing", $ex->getMessage());
        }

        return null;
    }

    /**
     * @param $bundleId
     * @param $message
     */
    public static function log($bundleId, $message)
    {
        Logger::log($bundleId, $message);
    }

    /**
     * @param $file
     *
     * @return bool|mixed
     */
    public static function includeIfExists($file)
    {
        if (file_exists($file)) {
            return include $file;
        }
        
        return false;
    }

    /**
     * @param $num
     *
     * @return float
     */
    public function tofloat($num)
    {
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

        if (!$sep) {
            return floatval(preg_replace("/[^0-9]/", "", $num));
        }

        return floatval(
            preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
        );
    }

    /**
     * @return string
     */
    public static function getPimcoreStaticPath ()
    {
        $pVersion = str_replace("v", "", Version::getVersion());
        $partVersion = explode(".", $pVersion);

        if (intval($partVersion[0]) == 5) { // Major version
            if (intval($partVersion[1]) <= 3) { // Medium version
                return "/pimcore/static6";
            } elseif (intval($partVersion[1]) >= 4) {
                return "/bundles/pimcoreadmin";
            }
        }

        return "/pimcore/static6";
    }

}