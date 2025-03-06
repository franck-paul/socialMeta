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
$this->registerModule(
    'socialMeta',
    'Add social meta to your posts and pages',
    'Franck Paul',
    '6.0',
    [
        'date'        => '2025-03-06T12:47:47+0100',
        'requires'    => [['core', '2.31']],
        'permissions' => 'My',
        'type'        => 'plugin',

        'details'    => 'https://open-time.net/?q=socialMeta',
        'support'    => 'https://github.com/franck-paul/socialMeta',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/socialMeta/main/dcstore.xml',
        'license'    => 'gpl2',
    ]
);
