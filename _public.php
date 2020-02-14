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

$core->addBehavior('publicHeadContent', ['dcSocialMeta', 'publicHeadContent']);

class dcSocialMeta
{
    public static function publicHeadContent()
    {
        global $core, $_ctx;

        $core->blog->settings->addNamespace('socialMeta');
        if ($core->blog->settings->socialMeta->active) {
            if (($core->url->type == 'post') || ($core->url->type == 'pages')) {

                if (($_ctx->posts->post_type == 'post' && $core->blog->settings->socialMeta->on_post) ||
                    ($_ctx->posts->post_type == 'page' && $core->blog->settings->socialMeta->on_page)) {
                    if (!$core->blog->settings->socialMeta->facebook &&
                        !$core->blog->settings->socialMeta->google &&
                        !$core->blog->settings->socialMeta->twitter) {
                        return;
                    }

                    // Post/Page URL
                    $url = $_ctx->posts->getURL();
                    // Post/Page title
                    $title = html::escapeHTML($_ctx->posts->post_title);
                    // Post/Page content
                    $content = $_ctx->posts->getExcerpt() . ' ' . $_ctx->posts->getContent();
                    $content = html::decodeEntities(html::clean($content));
                    $content = preg_replace('/\s+/', ' ', $content);
                    $content = html::escapeHTML($content);
                    $content = text::cutString($content, 180);
                    if ($content == '') {
                        // Use default description if any
                        $content = $core->blog->settings->socialMeta->description;
                        if ($content == '') {
                            // Use blog description if any
                            $content = $core->blog->desc;
                            if ($content == '') {
                                // Use blog title
                                $content = $core->blog->name;
                            }
                        }
                    }
                    // Post/Page first image
                    $media = new ArrayObject([
                        'img'   => '',
                        'alt'   => '',
                        'large' => false
                    ]);
                    // Let 3rd party plugins the opportunity to give media info
                    $core->callBehavior('socialMetaMedia', $media);

                    if ($media['img'] == '') {
                        if ($core->blog->settings->socialMeta->photo) {
                            // Photoblog, use original photo rather than small one
                            $media['img'] = context::EntryFirstImageHelper('o', true, '', true);
                            if ($media['img'] != '') {
                                $media['large'] = true;
                                $tag          = context::EntryFirstImageHelper('o', true, '', false);
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
                    if ($media['img'] == '' && $core->blog->settings->socialMeta->description != '') {
                        // Use default image as decoration if set
                        $media['img'] = $core->blog->settings->socialMeta->image;
                        $media['alt'] = '';
                    }
                    if (strlen($media['img']) && substr($media['img'], 0, 4) != 'http') {
                        $root = preg_replace('#^(.+?//.+?)/(.*)$#', '$1', $core->blog->url);
                        $media['img']  = $root . $media['img'];
                    }

                    if ($core->blog->settings->socialMeta->facebook) {
                        // Facebook meta
                        echo
                        '<!-- Facebook -->' . "\n" .
                        '<meta property="og:type" content="article" />' . "\n" .
                        '<meta property="og:title" content="' . $title . '" />' . "\n" .
                        '<meta property="og:url" content="' . $url . '" />' . "\n" .
                        '<meta property="og:site_name" content="' . $core->blog->name . '" />' . "\n" .
                            '<meta property="og:description" content="' . $content . '" />' . "\n";
                        if (strlen($media['img'])) {
                            echo
                                '<meta property="og:image" content="' . $media['img'] . '" />' . "\n";
                        }
                    }
                    if ($core->blog->settings->socialMeta->google) {
                        // Google+
                        echo
                            '<!-- Google+ -->' . "\n" .
                            '<meta itemprop="name" content="' . $title . '" />' . "\n" .
                            '<meta itemprop="description" content="' . $content . '" />' . "\n";
                        if (strlen($media['img'])) {
                            echo
                                '<meta itemprop="image" content="' . $media['img'] . '" />' . "\n";
                        }
                    }
                    if ($core->blog->settings->socialMeta->twitter) {
                        // Twitter account
                        $account = $core->blog->settings->socialMeta->twitter_account;
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
    }
}
