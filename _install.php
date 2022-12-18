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
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

if (!dcCore::app()->newVersion(basename(__DIR__), dcCore::app()->plugins->moduleInfo(basename(__DIR__), 'version'))) {
    return;
}

try {
    dcCore::app()->blog->settings->addNamespace('socialMeta');
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
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

return false;
