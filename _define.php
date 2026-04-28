<?php

/**
 * @brief socialMeta, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul
 *
 * @copyright Franck Paul contact@open-time.net
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
$this->registerModule(
    'socialMeta',
    'Add social meta to your posts and pages',
    'Franck Paul',
    '7.6',
    [
        'date'        => '2026-03-04T17:39:04+0100',
        'requires'    => [['core', '2.36']],
        'permissions' => 'My',
        'type'        => 'plugin',

        'details'    => 'https://open-time.net/?q=socialMeta',
        'support'    => 'https://github.com/franck-paul/socialMeta',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/socialMeta/main/dcstore.xml',
        'license'    => 'gpl2',
    ]
);
