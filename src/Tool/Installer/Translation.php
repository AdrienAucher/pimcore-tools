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

use Pimcore\Model\Translation\Admin as TranslationAdmin;
use Pimcore\Model\Translation\Website as TranslationWebsite;
use Pimcore\Tool\Admin as ToolAdmin;
use ActivePublishing\Tool\ApTool;
use ActivePublishing\Tool\User;

/**
 * Class Translation
 * @package ActivePublishing\Tool\Installer
 */
class Translation
{
    CONST INSTALL_ADMIN_TS_FILE = 'Resources/install/export_ admin_translations.csv';
    CONST INSTALL_WEB_TS_FILE = 'Resources/install/export_ website_translations.csv';

    /**
     * @param $bundleId
     *
     * @throws \Exception
     */
    public static function installAdminTranslations($bundleId)
    {
        if (file_exists(ApTool::getBundlePath($bundleId) . self::INSTALL_ADMIN_TS_FILE)) {
            $file = ApTool::getBundlePath($bundleId) . self::INSTALL_ADMIN_TS_FILE;
            TranslationAdmin::importTranslationsFromFile($file, true, ToolAdmin::getLanguages());
        }
    }

    /**
     * @param $bundleId
     *
     * @throws \Exception
     */
    public static function installWebsiteTranslations($bundleId)
    {
        if (file_exists(ApTool::getBundlePath($bundleId) . self::INSTALL_WEB_TS_FILE)) {
            $file = ApTool::getBundlePath($bundleId) . self::INSTALL_WEB_TS_FILE;
            $languages = User::getCurrentUser()->getAllowedLanguagesForEditingWebsiteTranslations();

            TranslationWebsite::importTranslationsFromFile($file, true, $languages);
        }
    }

}