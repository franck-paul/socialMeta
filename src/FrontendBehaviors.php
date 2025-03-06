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

use ArrayObject;
use Dotclear\App;
use Dotclear\Core\Frontend\Ctx;
use Dotclear\Helper\Date;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\Text;

class FrontendBehaviors
{
    public static function publicHeadContent(): string
    {
        $settings = My::settings();

        // Check settings and context
        if (!$settings->active) {
            // Plugin inactive on this blog
            return '';
        }

        if (!$settings->facebook && !$settings->google && !$settings->twitter) {
            // None of social metadata section is enabled for this blog
            return '';
        }

        // Check if context is a single one (post, page, …)
        $single = false;
        if (App::url()->getType() === 'post' && App::frontend()->context()->posts->post_type === 'post') {
            // Its a single post
            if (!$settings->on_post) {
                return '';
            }
            $single = true;
        } elseif (App::url()->getType() === 'pages' && App::frontend()->context()->posts->post_type == 'page') {
            // Its a single page
            if (!$settings->on_page) {
                return '';
            }
            $single = true;
        } elseif (!$settings->on_other) {
            return '';
        }

        if ($single) {
            // Post/Page URL
            $url = App::frontend()->context()->posts->getURL();
            // Post/Page title
            $title = Html::escapeHTML(App::frontend()->context()->posts->post_title);
            // Post/Page content
            $content = App::frontend()->context()->posts->getExcerpt() . ' ' . App::frontend()->context()->posts->getContent();
            $content = Html::decodeEntities(Html::clean($content));
            $content = preg_replace('/\s+/', ' ', $content);
            $content = Html::escapeHTML($content);
            $content = Text::cutString($content, 180);
            if ($content == '') {
                // Use default description if any
                $content = $settings->description;
                if ($content == '') {
                    // Use blog description if any
                    $content = Html::clean(App::blog()->desc());
                    if ($content == '') {
                        // Use blog title
                        $content = App::blog()->name();
                    }
                }
            }
            // Post/Page first image
            $media = new ArrayObject([
                'img'   => '',
                'alt'   => '',
                'large' => false,
            ]);
            // Give 3rd party plugins the opportunity to give media info
            App::behavior()->callBehavior('socialMetaMedia', $media);
            if ($media['img'] === '' && $settings->photo) {
                // Photoblog, use original photo rather than small one
                $media['img'] = Ctx::EntryFirstImageHelper('o', true, '', true);
                if ($media['img'] !== '') {     // @phpstan-ignore-line Ctx::EntryFirstImageHelper() may return empty string
                    $media['large'] = true;
                    $tag            = Ctx::EntryFirstImageHelper('o', true, '', false);
                    if (preg_match('/alt="([^"]+)"/', $tag, $malt)) {
                        $media['alt'] = $malt[1];
                    }
                }
            }
            if ($media['img'] === '') {
                $media['img'] = Ctx::EntryFirstImageHelper('m', true, '', true);
                if ($media['img'] !== '') {     // @phpstan-ignore-line Ctx::EntryFirstImageHelper() may return empty string
                    $tag = Ctx::EntryFirstImageHelper('m', true, '', false);
                    if (preg_match('/alt="([^"]+)"/', $tag, $malt)) {
                        $media['alt'] = $malt[1];
                    }
                }
            }
            if ($media['img'] === '' && $settings->image !== '') {
                // Use default image as decoration if set
                $media['img'] = $settings->image;
                $media['alt'] = '';
            }
            if (strlen((string) $media['img']) && !str_starts_with((string) $media['img'], 'http')) {
                $root         = preg_replace('#^(.+?//.+?)/(.*)$#', '$1', (string) App::blog()->url());
                $media['img'] = $root . $media['img'];
            }
        } else {
            // Home, Posts, Archive, Archive month, Tags, Tag, Series, Serie, …
            $url   = App::blog()->url();
            $title = App::blog()->name();

            switch (App::url()->getType()) {
                case 'archive':
                    $url = App::blog()->url() . App::url()->getURLFor('archive');
                    $title .= ' - ' . __('Archives');

                    if (!is_null(App::frontend()->context()->archives)) {
                        // Month archive
                        $url = App::frontend()->context()->archives->url();
                        $title .= ' &rsaquo ' . Date::dt2str('%B %Y', App::frontend()->context()->archives->dt);
                    }

                    break;

                case 'category':
                    $url = App::blog()->url() . App::url()->getURLFor('category', App::frontend()->context()->categories->cat_url);
                    // Add category parents' title
                    $categories = App::blog()->getCategoryParents((int) App::frontend()->context()->categories->cat_id);
                    $first      = true;
                    while ($categories->fetch()) {
                        $title .= ($first ? ' - ' : ' &rsaquo; ') . $categories->cat_title;
                        $first = false;
                    }
                    // Add current category title
                    $title .= ($first ? ' - ' : ' &rsaquo; ') . App::frontend()->context()->categories->cat_title;

                    break;

                case 'tags':
                    $url = App::blog()->url() . App::url()->getURLFor('tags');
                    $title .= ' - ' . __('Tags');

                    break;

                case 'tag':
                    $url = App::blog()->url() . App::url()->getURLFor('tag', rawurlencode(App::frontend()->context()->meta->meta_id));
                    $title .= ' - ' . __('Tag') . ' &rsaquo; ' . App::frontend()->context()->meta->meta_id;

                    break;

                case 'series':
                    $url = App::blog()->url() . App::url()->getURLFor('series');
                    $title .= ' - ' . __('Series');

                    break;

                case 'serie':
                    $url = App::blog()->url() . App::url()->getURLFor('serie', rawurlencode(App::frontend()->context()->meta->meta_id));
                    $title .= ' - ' . __('Serie') . ' &rsaquo; ' . App::frontend()->context()->meta->meta_id;

                    break;

                case 'home':    // Home page
                case 'posts':   // List of posts if static home
                default:
                    break;
            }

            // Use default description if any
            $content = $settings->description;
            if ($content == '') {
                // Use blog description if any
                $content = Html::clean(App::blog()->desc());
                if ($content === '') {
                    // Use blog title
                    $content = App::blog()->name();
                }
            }

            $media = new ArrayObject([
                'img'   => '',
                'alt'   => '',
                'large' => false,
            ]);
            // Give 3rd party plugins the opportunity to give media info
            App::behavior()->callBehavior('socialMetaMedia', $media);

            if ($media['img'] === '' && $settings->image !== '') {
                // Use default image as decoration if set
                $media['img']   = $settings->image;
                $media['large'] = $settings->photo ? true : false;
                $media['alt']   = '';
            }
        }

        // Everything is ready, it's time to output social metadata

        if ($settings->facebook) {
            // Mastodon account
            $account = (string) $settings->mastodon_account;
            if (strlen($account) && str_starts_with($account, '@')) {
                // Remove the first @ in @name@domain (see https://github.com/mastodon/mastodon/pull/30398)
                $account = ltrim($account, '@');
            }

            // Facebook/Mastodon meta
            echo
            '<!-- Facebook/Mastodon (Open Graph) -->' . "\n" .
            '<meta property="og:type" content="article">' . "\n" .
            '<meta property="og:title" content="' . $title . '">' . "\n" .
            '<meta property="og:url" content="' . $url . '">' . "\n" .
            '<meta property="og:site_name" content="' . App::blog()->name() . '">' . "\n" .
            '<meta property="og:description" content="' . $content . '">' . "\n";
            if (strlen((string) $media['img']) !== 0) {
                echo
                '<meta property="og:image" content="' . $media['img'] . '">' . "\n";
                if (isset($media['alt']) && $media['alt'] !== '') {
                    echo
                    '<meta property="og:image:alt" content="' . $media['alt'] . '">' . "\n";
                }
            }

            if (strlen($account) !== 0) {
                echo
                '<meta name="fediverse:creator" content="' . $account . '">' . "\n";
            }
        }

        if ($settings->google) {
            // Google+
            echo
            '<!-- Google -->' . "\n" .
            '<meta itemprop="name" content="' . $title . '">' . "\n" .
            '<meta itemprop="description" content="' . $content . '">' . "\n";
            if (strlen((string) $media['img']) !== 0) {
                echo
                '<meta itemprop="image" content="' . $media['img'] . '">' . "\n";
            }
        }

        if ($settings->twitter) {
            // Twitter account
            $account = (string) $settings->twitter_account;
            if (strlen($account) && !str_starts_with($account, '@')) {
                $account = '@' . $account;
            }

            // Twitter
            echo
            '<!-- Twitter -->' . "\n" .
            '<meta name="twitter:card" content="' . ($media['large'] ? 'summary_large_image' : 'summary') . '">' . "\n" .
            '<meta name="twitter:title" content="' . $title . '">' . "\n" .
            '<meta name="twitter:description" content="' . $content . '">' . "\n";
            if (strlen((string) $media['img']) !== 0) {
                echo
                '<meta name="twitter:image" content="' . $media['img'] . '">' . "\n";
                if ($media['alt'] != '') {
                    echo
                    '<meta name="twitter:image:alt" content="' . $media['alt'] . '">' . "\n";
                }
            }

            if (strlen($account) !== 0) {
                echo
                '<meta name="twitter:site" content="' . $account . '">' . "\n" .
                '<meta name="twitter:creator" content="' . $account . '">' . "\n";
            }
        }

        return '';
    }
}
