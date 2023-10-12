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
use context;
use dcCore;
use Dotclear\Helper\Html\Html;
use Dotclear\Helper\Text;

class FrontendBehaviors
{
    public static function publicHeadContent(): string
    {
        if (dcCore::app()->blog->settings->socialMeta->active) {
            if ((dcCore::app()->url->type == 'post') || (dcCore::app()->url->type == 'pages')) {
                if ((dcCore::app()->ctx->posts->post_type == 'post' && dcCore::app()->blog->settings->socialMeta->on_post) || (dcCore::app()->ctx->posts->post_type == 'page' && dcCore::app()->blog->settings->socialMeta->on_page)) {
                    if (!dcCore::app()->blog->settings->socialMeta->facebook && !dcCore::app()->blog->settings->socialMeta->google && !dcCore::app()->blog->settings->socialMeta->twitter) {
                        return '';
                    }

                    // Post/Page URL
                    $url = dcCore::app()->ctx->posts->getURL();
                    // Post/Page title
                    $title = Html::escapeHTML(dcCore::app()->ctx->posts->post_title);
                    // Post/Page content
                    $content = dcCore::app()->ctx->posts->getExcerpt() . ' ' . dcCore::app()->ctx->posts->getContent();
                    $content = Html::decodeEntities(Html::clean($content));
                    $content = preg_replace('/\s+/', ' ', $content);
                    $content = Html::escapeHTML($content);
                    $content = Text::cutString($content, 180);
                    if ($content == '') {
                        // Use default description if any
                        $content = dcCore::app()->blog->settings->socialMeta->description;
                        if ($content == '') {
                            // Use blog description if any
                            $content = Html::clean(dcCore::app()->blog->desc);
                            if ($content == '') {
                                // Use blog title
                                $content = dcCore::app()->blog->name;
                            }
                        }
                    }
                    // Post/Page first image
                    $media = new ArrayObject([
                        'img'   => '',
                        'alt'   => '',
                        'large' => false,
                    ]);
                    // Let 3rd party plugins the opportunity to give media info
                    dcCore::app()->callBehavior('socialMetaMedia', $media);

                    if ($media['img'] == '') {
                        if (dcCore::app()->blog->settings->socialMeta->photo) {
                            // Photoblog, use original photo rather than small one
                            $media['img'] = context::EntryFirstImageHelper('o', true, '', true);
                            if ($media['img'] != '') {
                                $media['large'] = true;
                                $tag            = context::EntryFirstImageHelper('o', true, '', false);
                                if (preg_match('/alt="([^"]+)"/', $tag, $malt)) {
                                    $media['alt'] = $malt[1];
                                }
                            }
                        }
                    }
                    if ($media['img'] == '') {
                        $media['img'] = context::EntryFirstImageHelper('m', true, '', true);
                        if ($media['img'] != '') {
                            $tag = context::EntryFirstImageHelper('m', true, '', false);
                            if (preg_match('/alt="([^"]+)"/', $tag, $malt)) {
                                $media['alt'] = $malt[1];
                            }
                        }
                    }
                    if ($media['img'] == '' && dcCore::app()->blog->settings->socialMeta->description != '') {
                        // Use default image as decoration if set
                        $media['img'] = dcCore::app()->blog->settings->socialMeta->image;
                        $media['alt'] = '';
                    }
                    if (strlen($media['img']) && substr($media['img'], 0, 4) != 'http') {
                        $root         = preg_replace('#^(.+?//.+?)/(.*)$#', '$1', dcCore::app()->blog->url);
                        $media['img'] = $root . $media['img'];
                    }

                    if (dcCore::app()->blog->settings->socialMeta->facebook) {
                        // Facebook meta
                        echo
                        '<!-- Facebook -->' . "\n" .
                        '<meta property="og:type" content="article" />' . "\n" .
                        '<meta property="og:title" content="' . $title . '" />' . "\n" .
                        '<meta property="og:url" content="' . $url . '" />' . "\n" .
                        '<meta property="og:site_name" content="' . dcCore::app()->blog->name . '" />' . "\n" .
                        '<meta property="og:description" content="' . $content . '" />' . "\n";
                        if (strlen($media['img'])) {
                            echo
                            '<meta property="og:image" content="' . $media['img'] . '" />' . "\n";
                            if (isset($media['alt']) && $media['alt'] !== '') {
                                echo
                                '<meta property="og:image:alt" content="' . $media['alt'] . '" />' . "\n";
                            }
                        }
                    }
                    if (dcCore::app()->blog->settings->socialMeta->google) {
                        // Google+
                        echo
                            '<!-- Google -->' . "\n" .
                            '<meta itemprop="name" content="' . $title . '" />' . "\n" .
                            '<meta itemprop="description" content="' . $content . '" />' . "\n";
                        if (strlen($media['img'])) {
                            echo
                                '<meta itemprop="image" content="' . $media['img'] . '" />' . "\n";
                        }
                    }
                    if (dcCore::app()->blog->settings->socialMeta->twitter) {
                        // Twitter account
                        $account = dcCore::app()->blog->settings->socialMeta->twitter_account;
                        if (strlen($account) && substr($account, 0, 1) != '@') {
                            $account = '@' . $account;
                        }
                        // Twitter
                        echo
                            '<!-- Twitter -->' . "\n" .
                            '<meta name="twitter:card" content="' . ($media['large'] ? 'summary_large_image' : 'summary') . '" />' . "\n" .
                            '<meta name="twitter:title" content="' . $title . '" />' . "\n" .
                            '<meta name="twitter:description" content="' . $content . '" />' . "\n";
                        if (strlen($media['img'])) {
                            echo
                                '<meta name="twitter:image" content="' . $media['img'] . '"/>' . "\n";
                            if ($media['alt'] != '') {
                                echo
                                    '<meta name="twitter:image:alt" content="' . $media['alt'] . '"/>' . "\n";
                            }
                        }
                        if (strlen($account)) {
                            echo
                                '<meta name="twitter:site" content="' . $account . '" />' . "\n" .
                                '<meta name="twitter:creator" content="' . $account . '" />' . "\n";
                        }
                    }
                }
            }
        }

        return '';
    }
}
