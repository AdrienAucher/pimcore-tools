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

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Objectbrick;
use ActivePublishing\Tool\ApTool;

/**
 * Class Definition
 * @package ActivePublishing\Tool\Installer
 */
class Definition
{
    CONST INSTALL_CLASSES_DIR = 'Resources/install/classes';
    CONST INSTALL_FIELDCOLLECTIONS_DIR = 'Resources/install/fieldcollections';
    CONST INSTALL_OBJECTBRICK_DIR = 'Resources/install/objectbrick';

    /**
     * @param $bundleId
     * @throws \Exception
     */
    public static function installClasses($bundleId)
    {
        if (file_exists(ApTool::getBundlePath($bundleId) . self::INSTALL_CLASSES_DIR)) {
            $rscandir = rscandir(ApTool::getBundlePath($bundleId) . self::INSTALL_CLASSES_DIR);
            if (!empty($rscandir)) {
                foreach ($rscandir as $path) {
                    $filename = basename($path);
                    $content = file_get_contents($path);

                    $parts = [];
                    if (1 === preg_match('/^class_(.*)_export\.json$/', $filename, $parts)) {
                        $name = $parts[1];
                        $definition = ClassDefinition::getByName($name);

                        if (!$definition) {
                            $definition = new ClassDefinition();
                            $definition->setName($name);
                        }

                        ClassDefinition\Service::importClassDefinitionFromJson($definition, $content, true);
                    }
                }
            }
        }
    }

    /**
     * @param $bundleId
     * @throws \Exception
     */
    public static function installFieldcollections($bundleId)
    {
        if (file_exists(ApTool::getBundlePath($bundleId) . self::INSTALL_FIELDCOLLECTIONS_DIR)) {
            $rscandir = rscandir(ApTool::getBundlePath($bundleId) . self::INSTALL_FIELDCOLLECTIONS_DIR);

            if (!empty($rscandir)) {
                foreach ($rscandir as $path) {
                    $filename = basename($path);
                    $content = file_get_contents($path);

                    $parts = [];
                    if (1 === preg_match('/^fieldcollection_(.*)_export\.json$/', $filename, $parts)) {
                        $name = $parts[1];

                        try {
                            $definition = Fieldcollection\Definition::getByKey($name);
                        } catch (\Exception $ex) {
                            $definition = new Fieldcollection\Definition();
                            $definition->setKey($name);
                        }

                        ClassDefinition\Service::importFieldCollectionFromJson($definition, $content, true);
                    }
                }
            }
        }
    }

    /**
     * @param $bundleId
     * @throws \Exception
     */
    public static function installObjectBrick($bundleId)
    {
        if (file_exists(ApTool::getBundlePath($bundleId) . self::INSTALL_OBJECTBRICK_DIR)) {
            $rscandir = rscandir(ApTool::getBundlePath($bundleId) . self::INSTALL_OBJECTBRICK_DIR);
            if (!empty($rscandir)) {
                foreach ($rscandir as $path) {
                    $filename = basename($path);
                    $content = file_get_contents($path);

                    $parts = [];
                    if (1 === preg_match('/^fieldcollection_(.*)_export\.json$/', $filename, $parts)) {
                        $name = $parts[1];

                        try {
                            $definition = Objectbrick\Definition::getByKey($name);
                        } catch (\Exception $ex) {
                            $definition = new Objectbrick\Definition();
                            $definition->setKey($name);
                        }

                        ClassDefinition\Service::importObjectBrickFromJson($definition, $content, true);
                    }
                }
            }
        }
    }

}
