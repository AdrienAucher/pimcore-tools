<?php

/**
 * Active Publishing - All right reserved
 *   Full copyright and license information is available in
 *   LICENSE.md which is distributed with this source code.
 *
 * @copyright Copyright (c) Active Publishing (https://activepublishing.fr)
 * @license Creative Common CC BY NC-ND 4.0
 * @author Active Publishing <contact@active-publishing.fr>
 */

declare(strict_types=1);

namespace ActivePublishing\GeneratorBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\BundleGenerator as BaseBundleGenerator;
use Sensio\Bundle\GeneratorBundle\Manipulator\RoutingManipulator;
use Sensio\Bundle\GeneratorBundle\Model\Bundle;

class BundleGenerator extends BaseBundleGenerator
{
    public function generateBundle(Bundle $bundle)
    {
        parent::generateBundle($bundle);

        $dir = $bundle->getTargetDirectory();

        $parameters = [
            'namespace' => $bundle->getNamespace(),
            'bundle' => $bundle->getName(),
            'format' => $bundle->getConfigurationFormat(),
            'bundle_basename' => $bundle->getBasename(),
            'extension_alias' => $bundle->getExtensionAlias(),
            'pimcore_version' => \Pimcore\Version::getVersion()
        ];

        $routingFilename = $bundle->getRoutingConfigurationFilename() ?: 'routing.yml';
        $routingTarget = $dir . '/Resources/config/pimcore/' . $routingFilename;

        // create routing file
        self::mkdir(dirname($routingTarget));
        self::dump($routingTarget, '');

        $routing = new RoutingManipulator($routingTarget);
        $routing->addResource($bundle->getName(), 'annotation');

        $this->renderFile(
            'specificBundle/services.yml.twig',
            $dir.'/Resources/config/services.yml',
            $parameters
        );

        $this->renderFile(
            'specificBundle/Bundle.php.twig',
            $dir.'/'.$bundle->getName().'.php',
            $parameters
        );

        $this->renderFile(
            'js/pimcore/startup.js.twig',
            $dir . '/Resources/public/js/pimcore/startup.js',
            $parameters
        );

        $this->renderFile(
            'controller/WireframeController.example.php.twig',
            $dir . '/Controller/' . $bundle->getBasename() . '.example.php',
            $parameters
        );

        self::removeDefaultViewsDir($dir . "/Resources/views");

        $this->renderFile(
            'areas/view.example.php.twig',
            $dir . '/Resources/views/Areas/template_name/view.example.html.php',
            $parameters
        );

        $this->renderFile(
            'areas/areabrick.example.php.twig',
            $dir . '/Document/Areabrick/template_name.php',
            $parameters
        );

        $this->renderFile(
            'areas/templateRender.example.php.twig',
            $dir . '/Resources/views/' . $bundle->getNamespace() . '/template_name.html.php',
            $parameters
        );

        $this->renderFile(
            'service/installer.example.php.twig',
            $dir . '/Service/Installer.php',
            $parameters
        );
    }

    private function removeDefaultViewsDir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") self::removeDefaultViewsDir($dir . "/" . $object); else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}