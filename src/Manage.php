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
use Dotclear\Helper\Process\TraitProcess;
use Exception;

class Manage
{
    use TraitProcess;

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
                // Post data helpers
                $getBool = fn (string $name): bool => !empty($_POST[$name]);
                $getStr  = fn (string $name, string $default = ''): string => isset($_POST[$name]) && is_string($val = $_POST[$name]) ? $val : $default;

                $sm_active           = $getBool('sm_active');
                $sm_on_post          = $getBool('sm_on_post');
                $sm_on_page          = $getBool('sm_on_page');
                $sm_on_other         = $getBool('sm_on_other');
                $sm_twitter_account  = trim(Html::escapeHTML($getStr('sm_twitter_account')));
                $sm_mastodon_account = trim(Html::escapeHTML($getStr('sm_mastodon_account')));
                $sm_facebook         = $getBool('sm_facebook');
                $sm_google           = $getBool('sm_google');
                $sm_twitter          = $getBool('sm_twitter');
                $sm_photo            = $getBool('sm_photo');
                $sm_description      = trim(Html::escapeHTML($getStr('sm_description')));
                $sm_image            = trim(Html::escapeHTML($getStr('sm_image')));

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

                App::backend()->notices()->addSuccessNotice(__('Settings have been successfully updated.'));
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

        $settings = My::settings();

        // Variable data helpers
        $getBool = fn (mixed $var): bool => (bool) $var;
        $getStr  = fn (mixed $var, string $default = ''): string => $var !== null && is_string($val = $var) ? $val : $default;

        $sm_active           = $getBool($settings->active);
        $sm_on_post          = $getBool($settings->on_post);
        $sm_on_page          = $getBool($settings->on_page);
        $sm_on_other         = $getBool($settings->on_other);
        $sm_twitter_account  = $getStr($settings->twitter_account);
        $sm_mastodon_account = $getStr($settings->mastodon_account);
        $sm_facebook         = $getBool($settings->facebook);
        $sm_google           = $getBool($settings->google);
        $sm_twitter          = $getBool($settings->twitter);
        $sm_photo            = $getBool($settings->photo);
        $sm_description      = $getStr($settings->description);
        $sm_image            = $getStr($settings->image);

        App::backend()->page()->openModule(My::name());

        echo App::backend()->page()->breadcrumb(
            [
                Html::escapeHTML(App::blog()->name()) => '',
                __('socialMeta')                      => '',
            ]
        );
        echo App::backend()->notices()->getNotices();

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
                        (new Note())
                            ->text(__('Example of Open Graph metadata inserted into the header:')),
                        (new Text(
                            'pre',
                            sprintf(
                                html::escapeHTML(
                                    '<meta property="og:type" content="website">' . "\n" .
                                    '<meta property="og:title" content="%s">' . "\n" .
                                    '<meta property="og:url" content="%s">' . "\n" .
                                    '<meta property="og:site_name" content="%s">' . "\n" .
                                    '<meta property="og:description" content="%s">' . "\n" .
                                    '<meta property="og:image" content="%s">' . "\n" .
                                    '<meta property="og:image:alt" content="%s">' . "\n" .
                                    '<meta property="fediverse:creator" content="%s">' . "\n"
                                ),
                                '<mark>' . App::blog()->name() . '</mark>',
                                '<mark>' . App::blog()->url() . '</mark>',
                                '<mark>' . App::blog()->name() . '</mark>',
                                '<mark>' . $sm_description . '</mark>',
                                '<mark>' . $sm_image . '</mark>',
                                '',
                                '<mark>' . $sm_mastodon_account . '</mark>'
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
                        (new Note())
                            ->text(__('Example of Google metadata inserted into the header:')),
                        (new Text(
                            'pre',
                            sprintf(
                                html::escapeHTML(
                                    '<meta itemprop="name" content="%s">' . "\n" .
                                    '<meta itemprop="description" content="%s">' . "\n" .
                                    '<meta itemprop="image" content="%s">' . "\n"
                                ),
                                '<mark>' . App::blog()->name() . '</mark>',
                                '<mark>' . $sm_description . '</mark>',
                                '<mark>' . $sm_image . '</mark>'
                            )
                        )),
                        // Specific Twitter
                        (new Para())
                            ->separator(' ')
                            ->items([
                                (new Checkbox('sm_twitter', $sm_twitter))
                                    ->value(1),
                                (new Label(__('Use Twitter/X social meta:')))
                                    ->for('sm_twitter')
                                    ->class('classic'),
                            ]),
                        (new Note())
                            ->text(__('Example of Twitter/X metadata inserted into the header:')),
                        (new Text(
                            'pre',
                            sprintf(
                                html::escapeHTML(
                                    '<meta name="twitter:card" content="summary">' . "\n" .
                                    '<meta name="twitter:title" content="%s">' . "\n" .
                                    '<meta name="twitter:description" content="%s">' . "\n" .
                                    '<meta name="twitter:image" content="%s">' . "\n" .
                                    '<meta name="twitter:image:alt" content="">' . "\n" .
                                    '<meta name="twitter:site" content="%s">' . "\n" .
                                    '<meta name="twitter:creator" content="%s">' . "\n"
                                ),
                                '<mark>' . App::blog()->name() . '</mark>',
                                '<mark>' . $sm_description . '</mark>',
                                '<mark>' . $sm_image . '</mark>',
                                '<mark>' . $sm_twitter_account . '</mark>',
                                '<mark>' . $sm_twitter_account . '</mark>',
                            )
                        )),
                    ]),
                // Settings
                (new Fieldset('sm_conf'))
                    ->legend((new Legend(__('Settings'))))
                    ->class('fieldset')
                    ->fields([
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

        App::backend()->page()->closeModule();
    }
}
