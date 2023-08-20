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
use dcNamespace;
use Dotclear\Core\Process;

class Install extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        $settings = dcCore::app()->blog->settings->get(My::id());

        $settings->put('active', false, dcNamespace::NS_BOOL, 'Active', false, true);
        $settings->put('on_post', true, dcNamespace::NS_BOOL, 'Add social meta on post', false, true);
        $settings->put('on_page', false, dcNamespace::NS_BOOL, 'Add social meta on page', false, true);
        $settings->put('twitter_account', '', dcNamespace::NS_STRING, 'Twitter account', false, true);
        $settings->put('facebook', true, dcNamespace::NS_BOOL, 'Insert Facebook meta', false, true);
        $settings->put('google', true, dcNamespace::NS_BOOL, 'Insert Google meta', false, true);
        $settings->put('twitter', true, dcNamespace::NS_BOOL, 'Insert Twitter meta', false, true);
        $settings->put('photo', false, dcNamespace::NS_BOOL, 'Photoblog', false, true);
        $settings->put('description', '', dcNamespace::NS_STRING, 'Default description', false, true);
        $settings->put('image', '', dcNamespace::NS_STRING, 'Default image', false, true);

        return true;
    }
}
