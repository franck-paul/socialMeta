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
declare(strict_types=1);

if (isset($this) && is_object($this) && method_exists($this, 'registerModule') && isset($this->id) && is_string($this->id)) {
    $this->registerModule(
        'socialMeta',
        'Add social meta to your posts and pages',
        'Franck Paul',
        '8.3',
        [
            'date'        => '2026-09-30T12:38:42+0200',
            'requires'    => [['core', '2.39']],
            'permissions' => 'My',
            'type'        => 'plugin',

            'details'    => 'https://open-time.net/?q=socialMeta',
            'support'    => 'https://github.com/franck-paul/socialMeta',
            'repository' => 'https://raw.githubusercontent.com/franck-paul/socialMeta/main/dcstore.xml',
            'license'    => 'gpl2',
        ]
    );
}
