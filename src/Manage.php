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
use formCheckbox;
use formFieldset;
use formForm;
use formInput;
use formLabel;
use formLegend;
use formPara;
use formSubmit;
use formText;
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
     * Opens a module.
     *
     * Will be replaced by dcPage::openModule() in 2.26
     *
     * @param      string       $title  The title
     * @param      null|string  $head   The head
     */
    public static function openModule(string $title = DC_VENDOR_NAME, ?string $head = '')
    {
        echo
        '<html>' .
        '<head>' .
        '<title>' . $title . '</title>' .
        $head .
        '</head>' .
        '<body>';
    }

    /**
     * Closes a module.
     *
     * Will be replaced by dcPage::closeModule() in 2.26
     */
    public static function closeModule()
    {
        echo
        '</body>' .
        '</html>';
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

        self::openModule(__('socialMeta'));

        echo dcPage::breadcrumb(
            [
                html::escapeHTML(dcCore::app()->blog->name) => '',
                __('socialMeta')                            => '',
            ]
        );
        echo dcPage::notices();

        echo formForm::init('frmreport')
            ->action(dcCore::app()->admin->getPageURL())
            ->method('post')
            ->fields([
                // Activation
                formPara::init()
                    ->separator(' ')
                    ->items([
                        formCheckbox::init('sm_active', $sm_active)
                            ->value(1),
                        formLabel::init(__('Active socialMeta'))
                            ->for('sm_active')
                            ->class('classic'),
                    ]),
                // Options
                formFieldset::init('sm_opt')
                    ->legend(formLegend::init(__('Options')))
                    ->class('fieldset')
                    ->fields([
                        (formPara::init())
                            ->separator(' ')
                            ->items([
                                formCheckbox::init('sm_on_post', $sm_on_post)
                                    ->value(1),
                                formLabel::init(__('Add social meta on posts'))
                                    ->for('sm_on_post')
                                    ->class('classic'),
                            ]),
                        formPara::init()
                            ->separator(' ')
                            ->items([
                                formCheckbox::init('sm_on_page', $sm_on_page)
                                    ->value(1),
                                formLabel::init(__('Add social meta on pages'))
                                    ->for('sm_on_page')
                                    ->class('classic'),
                            ]),
                        formText::init('hr'),
                        // Specific Facebook
                        formPara::init()
                            ->separator(' ')
                            ->items([
                                formCheckbox::init('sm_facebook', $sm_facebook)
                                    ->value(1),
                                formLabel::init(__('Use Open Graph (Mastodon/Facebook) social meta:'))
                                    ->for('sm_facebook')
                                    ->class('classic'),
                            ]),
                        formText::init(
                            'pre',
                            html::escapeHTML(
                                '<!-- Open Graph (Mastodon/Facebook) -->' . "\n" .
                                '<meta property="og:title" content="Plugin socialMeta 0.2 pour Dotclear" />' . "\n" .
                                '<meta property="og:url" content="http://open-time.net/post/2014/01/20/Plugin-socialMeta-02-pour-Dotclear" />' . "\n" .
                                '<meta property="og:site_name" content="Open-Time" />' . "\n" .
                                '<meta property="og:description" content="Nouvelle version de ce petit plugin, ..." />' . "\n" .
                                '<meta property="og:image" content="http://open-time.net/public/illustrations/2014/.googleplus-twitter-facebook_m.jpg" />' . "\n" .
                                '<meta property="og:image:alt" content="G+, Twitter et Facebook"/>' . "\n"
                            )
                        ),
                        // Specific Google
                        formPara::init()
                            ->separator(' ')
                            ->items([
                                formCheckbox::init('sm_google', $sm_google)
                                    ->value(1),
                                formLabel::init(__('Use Google social meta:'))
                                    ->for('sm_google')
                                    ->class('classic'),
                            ]),
                        formText::init(
                            'pre',
                            html::escapeHTML(
                                '<!-- Google -->' . "\n" .
                                '<meta itemprop="name" content="Plugin socialMeta 0.2 pour Dotclear" />' . "\n" .
                                '<meta itemprop="description" content="Nouvelle version de ce petit plugin, ..." />' . "\n" .
                                '<meta itemprop="image" content="http://open-time.net/public/illustrations/2014/.googleplus-twitter-facebook_m.jpg" />' . "\n"
                            )
                        ),
                        // Specific Twitter
                        formPara::init()
                            ->separator(' ')
                            ->items([
                                formCheckbox::init('sm_twitter', $sm_twitter)
                                    ->value(1),
                                formLabel::init(__('Use Twitter social meta:'))
                                    ->for('sm_twitter')
                                    ->class('classic'),
                            ]),
                        formText::init(
                            'pre',
                            html::escapeHTML(
                                '<!-- Twitter -->' . "\n" .
                                '<meta name="twitter:card" content="summary" />' . "\n" .
                                '<meta name="twitter:title" content="Plugin socialMeta 0.2 pour Dotclear" />' . "\n" .
                                '<meta name="twitter:description" content="Nouvelle version de ce petit plugin, ..." />' . "\n" .
                                '<meta name="twitter:image" content="http://open-time.net/public/illustrations/2014/.googleplus-twitter-facebook_m.jpg"/>' . "\n" .
                                '<meta name="twitter:image:alt" content="G+, Twitter et Facebook"/>' . "\n" .
                                '<meta name="twitter:site" content="@franckpaul" />' . "\n" .
                                '<meta name="twitter:creator" content="@franckpaul" />' . "\n"
                            )
                        ),
                    ]),
                // Settings
                formFieldset::init('sm_conf')
                    ->legend(formLegend::init(__('Settings')))
                    ->class('fieldset')
                    ->fields([
                        // Twitter account
                        formPara::init()
                            ->separator(' ')
                            ->items([
                                formLabel::init(__('Twitter account:'))
                                    ->for('sm_twitter_account'),
                                formInput::init('sm_twitter_account')
                                    ->value(html::escapeHTML($sm_twitter_account))
                                    ->size(30)
                                    ->maxlength(128)
                                    ->extra('aria-describedby="prefix-twitter_account"'),
                            ]),
                        formPara::init('prefix-twitter_account')
                            ->class('form-note')
                            ->items([
                                formText::init('', __('With or without @ prefix.')),
                            ]),
                        // Photoblog
                        formPara::init()
                            ->separator(' ')
                            ->items([
                                formCheckbox::init('sm_photo', $sm_photo)
                                    ->value(1),
                                formLabel::init(__('This blog is a photoblog'))
                                    ->for('sm_photo')
                                    ->class('classic')
                                    ->extra('aria-describedby="summary_large_image"'),
                            ]),
                        formPara::init('summary_large_image')
                            ->class('form-note')
                            ->items([
                                formText::init('', __('Will use "summary_large_image" twitter card type rather than "summary", and will include the first original photo if possible rather than the medium thumbnail.')),
                            ]),
                        // Default description
                        formPara::init()
                            ->separator(' ')
                            ->items([
                                formLabel::init(__('Default description:'))
                                    ->for('sm_description'),
                                formInput::init('sm_description')
                                    ->value(html::escapeHTML($sm_description))
                                    ->size(80)
                                    ->maxlength(255)
                                    ->extra('aria-describedby="default_description"'),
                            ]),
                        formPara::init('default_description')
                            ->class('form-note')
                            ->items([
                                formText::init('', __('Will be used if post (or page) have no text.')),
                            ]),
                        // Default image
                        formPara::init()
                            ->separator(' ')
                            ->items([
                                formLabel::init(__('Default image (URL):'))
                                    ->for('sm_image'),
                                formInput::init('sm_image')
                                    ->value(html::escapeHTML($sm_image))
                                    ->size(80)
                                    ->maxlength(255)
                                    ->extra('aria-describedby="default_image"'),
                            ]),
                        formPara::init('default_image')
                            ->class('form-note')
                            ->items([
                                formText::init('', __('Will be used if post (or page) have no image.')),
                            ]),
                    ]),
                // Button
                formPara::init()
                    ->items([
                        formSubmit::init('frmsave')
                            ->accesskey('s')
                            ->value(__('Save')),
                        dcCore::app()->formNonce(false),
                    ]),
            ])
            ->render();

        self::closeModule();
    }
}
