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
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'socialMeta',
    'Add social meta to your posts and pages',
    'Franck Paul',
    '1.0',
    [
        'requires'    => [['core', '2.24']],
        'permissions' => dcCore::app()->auth->makePermissions([
            dcAuth::PERMISSION_ADMIN,
        ]),
        'type' => 'plugin',

        'details'    => 'https://open-time.net/?q=socialMeta',
        'support'    => 'https://github.com/franck-paul/socialMeta',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/socialMeta/master/dcstore.xml',
    ]
);
