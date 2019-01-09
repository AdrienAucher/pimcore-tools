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

use Pimcore\File;

/**
 * Class Config
 * @package ActivePublishing\Tool
 */
class Config
{
    /**
     * @param $bundleId
     *
     * @return string
     */
    public static function getConfigPath ($bundleId)
    {
        $name = str_replace("bundle", "", mb_strtolower($bundleId));
        return PIMCORE_CONFIGURATION_DIRECTORY . '/' . $name . '.php';
    }

    /**
     * @param $bundleId
     * @param array $newConfig
     */
    public static function setConfig($bundleId, $newConfig = [])
    {
        $oldConfig = self::getConfig($bundleId);
        if (!$oldConfig) {
            File::putPhpFile(self::getConfigPath($bundleId), to_php_data_file_format($newConfig));

        } else {
            $config = array_merge($oldConfig, $newConfig);

            if (array_key_exists("config", $oldConfig)) {
                $config['config'] = $oldConfig['config'];
            } else if (array_key_exists("config", $newConfig)) {
                $config['config'] = $newConfig['config'];
            }

            File::putPhpFile(self::getConfigPath($bundleId), to_php_data_file_format($config));
        }
    }

    /**
     * @param $bundleId
     *
     * @return bool|mixed
     */
    public static function getConfig($bundleId)
    {
        $config = ApTool::includeIfExists(self::getConfigPath($bundleId));
        if(is_array($config) && !empty($config)){
            return $config;
        }

        return false;
    }

}
