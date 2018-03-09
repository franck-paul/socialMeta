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

// dead but useful code, in order to have translations
__('socialMeta') . __('Add social meta to your posts and pages');

$_menu['Blog']->addItem(__('socialMeta'),
    'plugin.php?p=socialMeta',
    urldecode(dcPage::getPF('socialMeta/icon.png')),
    preg_match('/plugin.php\?p=socialMeta(&.*)?$/', $_SERVER['REQUEST_URI']),
    $core->auth->check('admin', $core->blog->id));
