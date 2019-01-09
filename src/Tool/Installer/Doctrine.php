<?php
/**
 * Active Publishing - All right reserved
 *   Full copyright and license information is available in
 *   LICENSE.md which is distributed with this source code.
 * @copyright Copyright (c) Active Publishing (https://activepublishing.fr)
 * @license Creative Common CC BY NC-ND 4.0
 * @author Active Publishing <contact@active-publishing.fr>
 */

namespace ActivePublishing\Tool\Installer;

use Pimcore\Db;
use ActivePublishing\Tool\ApTool;

/**
 * Class Doctrine
 * @package ActivePublishing\Tool\Installer
 */
class Doctrine
{
    CONST INSTALL_TABLES_FILE = 'Resources/install/tables.php';

    /**
     * TODO Migrate to Doctrine
     *
     * @throws \Exception
     */
    public static function installBundleTables($bundleId)
    {
        $sQuery = "CREATE TABLE IF NOT EXISTS `%s`(%s) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

        try{
            $tablesConf = include (ApTool::getBundlePath($bundleId) . self::INSTALL_TABLES_FILE);

            foreach ($tablesConf as $tableName => $colonnes){
                $data = array();

                foreach ($colonnes as $attributs => $definition) {
                    $data[] = "`$attributs` $definition";
                }

                $strData = implode(",", $data);
                $stmt = Db::getConnection()->prepare(sprintf($sQuery, $tableName, $strData));
                $stmt->execute();
            }
        } catch(\Exception $ex) {
            $message = 'INSTALLER : An error occures when creating ActiveWireframe tables.';
            ApTool::log($bundleId, $message . ' : ' . $ex->getMessage());
            throw new \Exception($message);
        }
    }

}