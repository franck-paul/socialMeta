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

use Dotclear\App;
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

        $settings = My::settings();

        $settings->put('active', false, App::blogWorkspace()::NS_BOOL, 'Active', false, true);
        $settings->put('on_post', true, App::blogWorkspace()::NS_BOOL, 'Add social meta on post', false, true);
        $settings->put('on_page', false, App::blogWorkspace()::NS_BOOL, 'Add social meta on page', false, true);
        $settings->put('on_other', false, App::blogWorkspace()::NS_BOOL, 'Add social meta on other contexts', false, true);
        $settings->put('twitter_account', '', App::blogWorkspace()::NS_STRING, 'Twitter account', false, true);
        $settings->put('mastodon_account', '', App::blogWorkspace()::NS_STRING, 'Mastodon account', false, true);
        $settings->put('facebook', true, App::blogWorkspace()::NS_BOOL, 'Insert Facebook meta', false, true);
        $settings->put('google', true, App::blogWorkspace()::NS_BOOL, 'Insert Google meta', false, true);
        $settings->put('twitter', true, App::blogWorkspace()::NS_BOOL, 'Insert Twitter meta', false, true);
        $settings->put('photo', false, App::blogWorkspace()::NS_BOOL, 'Photoblog', false, true);
        $settings->put('description', '', App::blogWorkspace()::NS_STRING, 'Default description', false, true);
        $settings->put('image', '', App::blogWorkspace()::NS_STRING, 'Default image', false, true);

        return true;
    }
}
