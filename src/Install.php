<?php
/**
 * @brief socialMeta, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\socialMeta;

use dcCore;
use dcNsProcess;

class Install extends dcNsProcess
{
    public static function init(): bool
    {
        $module = basename(dirname(__DIR__));
        $check  = dcCore::app()->newVersion($module, dcCore::app()->plugins->moduleInfo($module, 'version'));

        self::$init = defined('DC_CONTEXT_ADMIN') && $check;

        return self::$init;
    }

    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        dcCore::app()->blog->settings->socialMeta->put('active', false, 'boolean', 'Active', false, true);
        dcCore::app()->blog->settings->socialMeta->put('on_post', true, 'boolean', 'Add social meta on post', false, true);
        dcCore::app()->blog->settings->socialMeta->put('on_page', false, 'boolean', 'Add social meta on page', false, true);
        dcCore::app()->blog->settings->socialMeta->put('twitter_account', '', 'string', 'Twitter account', false, true);
        dcCore::app()->blog->settings->socialMeta->put('facebook', true, 'boolean', 'Insert Facebook meta', false, true);
        dcCore::app()->blog->settings->socialMeta->put('google', true, 'boolean', 'Insert Google meta', false, true);
        dcCore::app()->blog->settings->socialMeta->put('twitter', true, 'boolean', 'Insert Twitter meta', false, true);
        dcCore::app()->blog->settings->socialMeta->put('photo', false, 'boolean', 'Photoblog', false, true);
        dcCore::app()->blog->settings->socialMeta->put('description', '', 'string', 'Default description', false, true);
        dcCore::app()->blog->settings->socialMeta->put('image', '', 'string', 'Default image', false, true);

        return true;
    }
}
