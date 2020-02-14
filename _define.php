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

if (!defined('DC_RC_PATH')) {return;}

$this->registerModule(
    "socialMeta",                              // Name
    "Add social meta to your posts and pages", // Description
    "Franck Paul",                             // Author
    '0.6',                                     // Version
    [
        'requires'    => [['core', '2.13']], // Dependencies
        'permissions' => 'admin',            // Permissions
        'type'        => 'plugin'           // Type
    ]
);
