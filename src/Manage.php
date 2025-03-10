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

use Dotclear\App;
use Dotclear\Core\Backend\Notices;
use Dotclear\Core\Backend\Page;
use Dotclear\Core\Process;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Form;
use Dotclear\Helper\Html\Form\Input;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Note;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Single;
use Dotclear\Helper\Html\Form\Submit;
use Dotclear\Helper\Html\Form\Text;
use Dotclear\Helper\Html\Html;
use Exception;

class Manage extends Process
{
    /**
     * Initializes the page.
     */
    public static function init(): bool
    {
        return self::status(My::checkContext(My::MANAGE));
    }

    /**
     * Processes the request(s).
     */
    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        $settings = My::settings();
        if (is_null($settings->active)) {
            try {
                // Add default settings values if necessary
                $settings->put('active', false, App::blogWorkspace()::NS_BOOL, 'Active', false);
                $settings->put('on_post', true, App::blogWorkspace()::NS_BOOL, 'Add social meta on post', false);
                $settings->put('on_page', false, App::blogWorkspace()::NS_BOOL, 'Add social meta on page', false);
                $settings->put('on_other', false, App::blogWorkspace()::NS_BOOL, 'Add social meta on other contexts', false);
                $settings->put('twitter_account', '', App::blogWorkspace()::NS_STRING, 'Twitter account', false);
                $settings->put('mastodon_account', '', App::blogWorkspace()::NS_STRING, 'Mastodon account', false);
                $settings->put('facebook', true, App::blogWorkspace()::NS_BOOL, 'Insert Facebook meta', false);
                $settings->put('google', true, App::blogWorkspace()::NS_BOOL, 'Insert Google meta', false);
                $settings->put('twitter', true, App::blogWorkspace()::NS_BOOL, 'Insert Twitter meta', false);
                $settings->put('photo', false, App::blogWorkspace()::NS_BOOL, 'Photoblog', false);
                $settings->put('description', '', App::blogWorkspace()::NS_STRING, 'Default description', false);
                $settings->put('image', '', App::blogWorkspace()::NS_STRING, 'Default image', false);

                App::blog()->triggerBlog();
                My::redirect();
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        }

        if ($_POST !== []) {
            try {
                $sm_active           = !empty($_POST['sm_active']);
                $sm_on_post          = !empty($_POST['sm_on_post']);
                $sm_on_page          = !empty($_POST['sm_on_page']);
                $sm_on_other         = !empty($_POST['sm_on_other']);
                $sm_twitter_account  = trim(Html::escapeHTML($_POST['sm_twitter_account']));
                $sm_mastodon_account = trim(Html::escapeHTML($_POST['sm_mastodon_account']));
                $sm_facebook         = !empty($_POST['sm_facebook']);
                $sm_google           = !empty($_POST['sm_google']);
                $sm_twitter          = !empty($_POST['sm_twitter']);
                $sm_photo            = !empty($_POST['sm_photo']);
                $sm_description      = trim(Html::escapeHTML($_POST['sm_description']));
                $sm_image            = trim(Html::escapeHTML($_POST['sm_image']));

                # Everything's fine, save options
                $settings->put('active', $sm_active, App::blogWorkspace()::NS_BOOL);
                $settings->put('on_post', $sm_on_post, App::blogWorkspace()::NS_BOOL);
                $settings->put('on_page', $sm_on_page, App::blogWorkspace()::NS_BOOL);
                $settings->put('on_other', $sm_on_other, App::blogWorkspace()::NS_BOOL);
                $settings->put('twitter_account', $sm_twitter_account, App::blogWorkspace()::NS_STRING);
                $settings->put('mastodon_account', $sm_mastodon_account, App::blogWorkspace()::NS_STRING);
                $settings->put('facebook', $sm_facebook, App::blogWorkspace()::NS_BOOL);
                $settings->put('google', $sm_google, App::blogWorkspace()::NS_BOOL);
                $settings->put('twitter', $sm_twitter, App::blogWorkspace()::NS_BOOL);
                $settings->put('photo', $sm_photo, App::blogWorkspace()::NS_BOOL);
                $settings->put('description', $sm_description, App::blogWorkspace()::NS_STRING);
                $settings->put('image', $sm_image, App::blogWorkspace()::NS_STRING);

                App::blog()->triggerBlog();

                Notices::addSuccessNotice(__('Settings have been successfully updated.'));
                My::redirect();
            } catch (Exception $e) {
                App::error()->add($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Renders the page.
     */
    public static function render(): void
    {
        if (!self::status()) {
            return;
        }

        $settings            = My::settings();
        $sm_active           = (bool) $settings->active;
        $sm_on_post          = (bool) $settings->on_post;
        $sm_on_page          = (bool) $settings->on_page;
        $sm_on_other         = (bool) $settings->on_other;
        $sm_twitter_account  = $settings->twitter_account;
        $sm_mastodon_account = $settings->mastodon_account;
        $sm_facebook         = (bool) $settings->facebook;
        $sm_google           = (bool) $settings->google;
        $sm_twitter          = (bool) $settings->twitter;
        $sm_photo            = (bool) $settings->photo;
        $sm_description      = $settings->description;
        $sm_image            = $settings->image;

        Page::openModule(My::name());

        echo Page::breadcrumb(
            [
                Html::escapeHTML(App::blog()->name()) => '',
                __('socialMeta')                      => '',
            ]
        );
        echo Notices::getNotices();

        echo (new Form('frmreport'))
            ->action(App::backend()->getPageURL())
            ->method('post')
            ->fields([
                // Activation
                (new Para())
                    ->separator(' ')
                    ->items([
                        (new Checkbox('sm_active', $sm_active))
                            ->value(1),
                        (new Label(__('Active socialMeta')))
                            ->for('sm_active')
                            ->class('classic'),
                    ]),
                // Options
                (new Fieldset('sm_opt'))
                    ->legend((new Legend(__('Options'))))
                    ->class('fieldset')
                    ->fields([
                        (new Para())
                            ->separator(' ')
                            ->items([
                                (new Checkbox('sm_on_post', $sm_on_post))
                                    ->value(1),
                                (new Label(__('Add social meta on posts')))
                                    ->for('sm_on_post')
                                    ->class('classic'),
                            ]),
                        (new Para())
                            ->separator(' ')
                            ->items([
                                (new Checkbox('sm_on_page', $sm_on_page))
                                    ->value(1),
                                (new Label(__('Add social meta on pages')))
                                    ->for('sm_on_page')
                                    ->class('classic'),
                            ]),
                        (new Para())
                            ->separator(' ')
                            ->items([
                                (new Checkbox('sm_on_other', $sm_on_other))
                                    ->value(1),
                                (new Label(__('Add social meta on other contexts (home, archive, category, tags, ...)')))
                                    ->for('sm_on_other')
                                    ->class('classic'),
                            ]),
                        (new Single('hr')),
                        // Specific Facebook
                        (new Para())
                            ->separator(' ')
                            ->items([
                                (new Checkbox('sm_facebook', $sm_facebook))
                                    ->value(1),
                                (new Label(__('Use Open Graph (Mastodon/Facebook) social meta:')))
                                    ->for('sm_facebook')
                                    ->class('classic'),
                            ]),
                        (new Text(
                            'pre',
                            html::escapeHTML(
                                '<!-- Open Graph (Mastodon/Facebook) -->' . "\n" .
                                '<meta property="og:title" content="Plugin socialMeta 0.2 pour Dotclear">' . "\n" .
                                '<meta property="og:url" content="http://open-time.net/post/2014/01/20/Plugin-socialMeta-02-pour-Dotclear">' . "\n" .
                                '<meta property="og:site_name" content="Open-Time">' . "\n" .
                                '<meta property="og:description" content="Nouvelle version de ce petit plugin, ...">' . "\n" .
                                '<meta property="og:image" content="http://open-time.net/public/illustrations/2014/.googleplus-twitter-facebook_m.jpg">' . "\n" .
                                '<meta property="og:image:alt" content="G+, Twitter et Facebook">' . "\n" .
                                '<meta property="fediverse:creator" content="@franckpaul@mstdn.fr">' . "\n"
                            )
                        )),
                        // Specific Google
                        (new Para())
                            ->separator(' ')
                            ->items([
                                (new Checkbox('sm_google', $sm_google))
                                    ->value(1),
                                (new Label(__('Use Google social meta:')))
                                    ->for('sm_google')
                                    ->class('classic'),
                            ]),
                        (new Text(
                            'pre',
                            html::escapeHTML(
                                '<!-- Google -->' . "\n" .
                                '<meta itemprop="name" content="Plugin socialMeta 0.2 pour Dotclear">' . "\n" .
                                '<meta itemprop="description" content="Nouvelle version de ce petit plugin, ...">' . "\n" .
                                '<meta itemprop="image" content="http://open-time.net/public/illustrations/2014/.googleplus-twitter-facebook_m.jpg">' . "\n"
                            )
                        )),
                        // Specific Twitter
                        (new Para())
                            ->separator(' ')
                            ->items([
                                (new Checkbox('sm_twitter', $sm_twitter))
                                    ->value(1),
                                (new Label(__('Use Twitter social meta:')))
                                    ->for('sm_twitter')
                                    ->class('classic'),
                            ]),
                        (new Text(
                            'pre',
                            html::escapeHTML(
                                '<!-- Twitter -->' . "\n" .
                                '<meta name="twitter:card" content="summary">' . "\n" .
                                '<meta name="twitter:title" content="Plugin socialMeta 0.2 pour Dotclear">' . "\n" .
                                '<meta name="twitter:description" content="Nouvelle version de ce petit plugin, ...">' . "\n" .
                                '<meta name="twitter:image" content="http://open-time.net/public/illustrations/2014/.googleplus-twitter-facebook_m.jpg">' . "\n" .
                                '<meta name="twitter:image:alt" content="G+, Twitter et Facebook">' . "\n" .
                                '<meta name="twitter:site" content="@franckpaul">' . "\n" .
                                '<meta name="twitter:creator" content="@franckpaul">' . "\n"
                            )
                        )),
                    ]),
                // Settings
                (new Fieldset('sm_conf'))
                    ->legend((new Legend(__('Settings'))))
                    ->class('fieldset')
                    ->fields([
                        // Twitter account
                        (new Para())
                            ->separator(' ')
                            ->items([
                                (new Label(__('Twitter account:')))
                                    ->for('sm_twitter_account'),
                                (new Input('sm_twitter_account'))
                                    ->value(Html::escapeHTML($sm_twitter_account))
                                    ->size(30)
                                    ->maxlength(128)
                                    ->extra('aria-describedby="prefix-twitter_account"'),
                            ]),
                        (new Note('prefix-twitter_account'))
                            ->class('form-note')
                            ->text(__('With or without @ prefix.')),
                        // Mastodon account
                        (new Para())
                            ->separator(' ')
                            ->items([
                                (new Label(__('Mastodon account:')))
                                    ->for('sm_mastodon_account'),
                                (new Input('sm_mastodon_account'))
                                    ->value(Html::escapeHTML($sm_mastodon_account))
                                    ->size(30)
                                    ->maxlength(128)
                                    ->extra('aria-describedby="prefix-mastodon_account"'),
                            ]),
                        (new Note('prefix-twitter_account'))
                            ->class('form-note')
                            ->text(__('@user@mastodon_instance.ext (see your Mastodon profile)')),
                        // Photoblog
                        (new Para())
                            ->separator(' ')
                            ->items([
                                (new Checkbox('sm_photo', $sm_photo))
                                    ->value(1),
                                (new Label(__('This blog is a photoblog')))
                                    ->for('sm_photo')
                                    ->class('classic')
                                    ->extra('aria-describedby="summary_large_image"'),
                            ]),
                        (new Note('summary_large_image'))
                            ->class('form-note')
                            ->text(__('Will use "summary_large_image" twitter card type rather than "summary", and will include the first original photo if possible rather than the medium thumbnail for post and page contexts.')),
                        // Default description
                        (new Para())
                            ->separator(' ')
                            ->items([
                                (new Label(__('Default description:')))
                                    ->for('sm_description'),
                                (new Input('sm_description'))
                                    ->value(html::escapeHTML($sm_description))
                                    ->size(80)
                                    ->maxlength(255)
                                    ->extra('aria-describedby="default_description"'),
                            ]),
                        (new Note('default_description'))
                            ->class('form-note')
                            ->text(__('Will be used if post (or page) have no text and for other contexts.')),
                        // Default image
                        (new Para())
                            ->separator(' ')
                            ->items([
                                (new Label(__('Default image (URL):')))
                                    ->for('sm_image'),
                                (new Input('sm_image'))
                                    ->value(html::escapeHTML($sm_image))
                                    ->size(80)
                                    ->maxlength(255)
                                    ->extra('aria-describedby="default_image"'),
                            ]),
                        (new Note('default_image'))
                            ->class('form-note')
                            ->text(__('Will be used if post (or page) have no image and for other contexts.')),
                    ]),
                // Button
                (new Para())
                    ->items([
                        (new Submit('frmsave'))
                            ->accesskey('s')
                            ->value(__('Save')),
                        ...My::hiddenFields(),
                    ]),
            ])
            ->render();

        Page::closeModule();
    }
}
