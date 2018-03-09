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

if (!defined('DC_CONTEXT_ADMIN')) {return;}

$new_version = $core->plugins->moduleInfo('socialMeta', 'version');
$old_version = $core->getVersion('socialMeta');

if (version_compare($old_version, $new_version, '>=')) {
    return;
}

try
{
    $core->blog->settings->addNamespace('socialMeta');
    $core->blog->settings->socialMeta->put('active', false, 'boolean', 'Active', false, true);
    $core->blog->settings->socialMeta->put('on_post', true, 'boolean', 'Add social meta on post', false, true);
    $core->blog->settings->socialMeta->put('on_page', false, 'boolean', 'Add social meta on page', false, true);
    $core->blog->settings->socialMeta->put('twitter_account', '', 'string', 'Twitter account', false, true);
    $core->blog->settings->socialMeta->put('facebook', true, 'boolean', 'Insert Facebook meta', false, true);
    $core->blog->settings->socialMeta->put('google', true, 'boolean', 'Insert Google+ meta', false, true);
    $core->blog->settings->socialMeta->put('twitter', true, 'boolean', 'Insert Twitter meta', false, true);
    $core->blog->settings->socialMeta->put('photo', false, 'boolean', 'Photoblog', false, true);
    $core->blog->settings->socialMeta->put('description', '', 'string', 'Default description', false, true);
    $core->blog->settings->socialMeta->put('image', '', 'string', 'Default image', false, true);

    $core->setVersion('socialMeta', $new_version);

    return true;
} catch (Exception $e) {
    $core->error->add($e->getMessage());
}
return false;
