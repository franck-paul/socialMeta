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
use dcNsProcess;
use dcPage;
use Exception;
use form;
use html;
use http;

class Manage extends dcNsProcess
{
    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        if (is_null(dcCore::app()->blog->settings->socialMeta->active)) {
            try {
                // Add default settings values if necessary
                dcCore::app()->blog->settings->socialMeta->put('active', false, 'boolean', 'Active', false);
                dcCore::app()->blog->settings->socialMeta->put('on_post', true, 'boolean', 'Add social meta on post', false);
                dcCore::app()->blog->settings->socialMeta->put('on_page', false, 'boolean', 'Add social meta on page', false);
                dcCore::app()->blog->settings->socialMeta->put('twitter_account', '', 'string', 'Twitter account', false);
                dcCore::app()->blog->settings->socialMeta->put('facebook', true, 'boolean', 'Insert Facebook meta', false);
                dcCore::app()->blog->settings->socialMeta->put('google', true, 'boolean', 'Insert Google meta', false);
                dcCore::app()->blog->settings->socialMeta->put('twitter', true, 'boolean', 'Insert Twitter meta', false);
                dcCore::app()->blog->settings->socialMeta->put('photo', false, 'boolean', 'Photoblog', false);
                dcCore::app()->blog->settings->socialMeta->put('description', '', 'string', 'Default description', false);
                dcCore::app()->blog->settings->socialMeta->put('image', '', 'string', 'Default image', false);

                dcCore::app()->blog->triggerBlog();
                http::redirect(dcCore::app()->admin->getPageURL());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        self::$init = true;

        return self::$init;
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!self::$init) {
            return false;
        }

        if (!empty($_POST)) {
            try {
                $sm_active          = !empty($_POST['sm_active']);
                $sm_on_post         = !empty($_POST['sm_on_post']);
                $sm_on_page         = !empty($_POST['sm_on_page']);
                $sm_twitter_account = trim(html::escapeHTML($_POST['sm_twitter_account']));
                $sm_facebook        = !empty($_POST['sm_facebook']);
                $sm_google          = !empty($_POST['sm_google']);
                $sm_twitter         = !empty($_POST['sm_twitter']);
                $sm_photo           = !empty($_POST['sm_photo']);
                $sm_description     = trim(html::escapeHTML($_POST['sm_description']));
                $sm_image           = trim(html::escapeHTML($_POST['sm_image']));

                # Everything's fine, save options
                dcCore::app()->blog->settings->socialMeta->put('active', $sm_active);
                dcCore::app()->blog->settings->socialMeta->put('on_post', $sm_on_post);
                dcCore::app()->blog->settings->socialMeta->put('on_page', $sm_on_page);
                dcCore::app()->blog->settings->socialMeta->put('twitter_account', $sm_twitter_account);
                dcCore::app()->blog->settings->socialMeta->put('facebook', $sm_facebook);
                dcCore::app()->blog->settings->socialMeta->put('google', $sm_google);
                dcCore::app()->blog->settings->socialMeta->put('twitter', $sm_twitter);
                dcCore::app()->blog->settings->socialMeta->put('photo', $sm_photo);
                dcCore::app()->blog->settings->socialMeta->put('description', $sm_description);
                dcCore::app()->blog->settings->socialMeta->put('image', $sm_image);

                dcCore::app()->blog->triggerBlog();

                dcPage::addSuccessNotice(__('Settings have been successfully updated.'));
                http::redirect(dcCore::app()->admin->getPageURL());
            } catch (Exception $e) {
                dcCore::app()->error->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!self::$init) {
            return;
        }

        $sm_active          = (bool) dcCore::app()->blog->settings->socialMeta->active;
        $sm_on_post         = (bool) dcCore::app()->blog->settings->socialMeta->on_post;
        $sm_on_page         = (bool) dcCore::app()->blog->settings->socialMeta->on_page;
        $sm_twitter_account = dcCore::app()->blog->settings->socialMeta->twitter_account;
        $sm_facebook        = (bool) dcCore::app()->blog->settings->socialMeta->facebook;
        $sm_google          = (bool) dcCore::app()->blog->settings->socialMeta->google;
        $sm_twitter         = (bool) dcCore::app()->blog->settings->socialMeta->twitter;
        $sm_photo           = (bool) dcCore::app()->blog->settings->socialMeta->photo;
        $sm_description     = dcCore::app()->blog->settings->socialMeta->description;
        $sm_image           = dcCore::app()->blog->settings->socialMeta->image;

        echo
        '<html>' .
        '<head>' .
        '<title>' . __('socialMeta') . '</title>' .
        '</head>' .
        '<body>';

        echo dcPage::breadcrumb(
            [
                html::escapeHTML(dcCore::app()->blog->name) => '',
                __('socialMeta')                            => '',
            ]
        );
        echo dcPage::notices();

        echo
        '<form action="' . dcCore::app()->admin->getPageURL() . '" method="post">' .
        '<p>' . form::checkbox('sm_active', 1, $sm_active) . ' ' .
        '<label for="sm_active" class="classic">' . __('Active socialMeta') . '</label></p>' .

        '<h3>' . __('Options') . '</h3>' .

        '<p>' . form::checkbox('sm_on_post', 1, $sm_on_post) . ' ' .
        '<label for="sm_on_post" class="classic">' . __('Add social meta on posts') . '</label></p>' .
        '<p>' . form::checkbox('sm_on_page', 1, $sm_on_page) . ' ' .
        '<label for="sm_on_page" class="classic">' . __('Add social meta on pages') . '</label></p>' .

        '<hr />' .

        '<p>' . form::checkbox('sm_facebook', 1, $sm_facebook) . ' ' .
        '<label for="sm_facebook" class="classic">' . __('Use Facebook social meta:') . '</label></p>' .
        '<pre>' .
        html::escapeHTML(
            '<!-- Facebook -->' . "\n" .
            '<meta property="og:title" content="Plugin socialMeta 0.2 pour Dotclear" />' . "\n" .
            '<meta property="og:url" content="http://open-time.net/post/2014/01/20/Plugin-socialMeta-02-pour-Dotclear" />' . "\n" .
            '<meta property="og:site_name" content="Open-Time" />' . "\n" .
            '<meta property="og:description" content="Nouvelle version de ce petit plugin, ..." />' . "\n" .
            '<meta property="og:image" content="http://open-time.net/public/illustrations/2014/.googleplus-twitter-facebook_m.jpg" />' . "\n"
        ) .
        '</pre>' .

        '<p>' . form::checkbox('sm_google', 1, $sm_google) . ' ' .
        '<label for="sm_google" class="classic">' . __('Use Google social meta:') . '</label></p>' .
        '<pre>' .
        html::escapeHTML(
            '<!-- Google -->' . "\n" .
            '<meta itemprop="name" content="Plugin socialMeta 0.2 pour Dotclear" />' . "\n" .
            '<meta itemprop="description" content="Nouvelle version de ce petit plugin, ..." />' . "\n" .
            '<meta itemprop="image" content="http://open-time.net/public/illustrations/2014/.googleplus-twitter-facebook_m.jpg" />' . "\n"
        ) .
        '</pre>' .

        '<p>' . form::checkbox('sm_twitter', 1, $sm_twitter) . ' ' .
        '<label for="sm_twitter" class="classic">' . __('Use Twitter social meta:') . '</label></p>' .
        '<pre>' .
        html::escapeHTML(
            '<!-- Twitter -->' . "\n" .
            '<meta name="twitter:card" content="summary" />' . "\n" .
            '<meta name="twitter:title" content="Plugin socialMeta 0.2 pour Dotclear" />' . "\n" .
            '<meta name="twitter:description" content="Nouvelle version de ce petit plugin, ..." />' . "\n" .
            '<meta name="twitter:image" content="http://open-time.net/public/illustrations/2014/.googleplus-twitter-facebook_m.jpg"/>' . "\n" .
            '<meta name="twitter:image:alt" content="G+, Twitter et Facebook"/>' . "\n" .
            '<meta name="twitter:site" content="@franckpaul" />' . "\n" .
            '<meta name="twitter:creator" content="@franckpaul" />' . "\n"
        ) .
        '</pre>' .

        '<h3>' . __('Settings') . '</h3>' .

        '<p><label for="sm_twitter_account">' . __('Twitter account:') . '</label> ' .
        form::field('sm_twitter_account', 30, 128, html::escapeHTML($sm_twitter_account), '', '', false, 'aria-describedby="prefix-twitter_account"') . '</p>' .
        '<p class="form-note" id="prefix-twitter_account">' . __('With or without @ prefix.') . '</p>' .

        '<p>' . form::checkbox('sm_photo', 1, $sm_photo, '', '', false, 'aria-describedby="summary_large_image"') . ' ' .
        '<label for="sm_photo" class="classic">' . __('This blog is a photoblog') . '</label></p>' .
        '<p class="form-note" id="summary_large_image">' . __('Will use "summary_large_image" twitter card type rather than "summary", and will include the first original photo if possible rather than the medium thumbnail.') . '</p>' .

        '<p><label for="sm_description">' . __('Default description:') . '</label> ' .
        form::field('sm_description', 80, 255, html::escapeHTML($sm_description), '', '', false, 'aria-describedby="default_description"') . '</p>' .
        '<p class="form-note" id="default_description">' . __('Will be used if post (or page) have no text.') . '</p>' .

        '<p><label for="sm_image">' . __('Default image (URL):') . '</label> ' .
        form::field('sm_image', 80, 255, html::escapeHTML($sm_image), '', '', false, 'aria-describedby="default_image"') . '</p>' .
        '<p class="form-note" id="default_image">' . __('Will be used if post (or page) have no image.') . '</p>' .

        '<p>' . dcCore::app()->formNonce() . '<input type="submit" value="' . __('Save') . '" /></p>' .
        '</form>';

        echo
        '</body>' .
        '</html>';
    }
}