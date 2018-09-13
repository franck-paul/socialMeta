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

if (!defined('DC_CONTEXT_ADMIN')) {return;}

$core->blog->settings->addNamespace('socialMeta');
if (is_null($core->blog->settings->socialMeta->active)) {
    try {
        // Add default settings values if necessary
        $core->blog->settings->socialMeta->put('active', false, 'boolean', 'Active', false);
        $core->blog->settings->socialMeta->put('on_post', true, 'boolean', 'Add social meta on post', false);
        $core->blog->settings->socialMeta->put('on_page', false, 'boolean', 'Add social meta on page', false);
        $core->blog->settings->socialMeta->put('twitter_account', '', 'string', 'Twitter account', false);
        $core->blog->settings->socialMeta->put('facebook', true, 'boolean', 'Insert Facebook meta', false);
        $core->blog->settings->socialMeta->put('google', true, 'boolean', 'Insert Google+ meta', false);
        $core->blog->settings->socialMeta->put('twitter', true, 'boolean', 'Insert Twitter meta', false);
        $core->blog->settings->socialMeta->put('photo', false, 'boolean', 'Photoblog', false);
        $core->blog->settings->socialMeta->put('description', '', 'string', 'Default description', false);
        $core->blog->settings->socialMeta->put('image', '', 'string', 'Default image', false);

        $core->blog->triggerBlog();
        http::redirect($p_url);
    } catch (Exception $e) {
        $core->error->add($e->getMessage());
    }
}

$sm_active          = (boolean) $core->blog->settings->socialMeta->active;
$sm_on_post         = (boolean) $core->blog->settings->socialMeta->on_post;
$sm_on_page         = (boolean) $core->blog->settings->socialMeta->on_page;
$sm_twitter_account = $core->blog->settings->socialMeta->twitter_account;
$sm_facebook        = (boolean) $core->blog->settings->socialMeta->facebook;
$sm_google          = (boolean) $core->blog->settings->socialMeta->google;
$sm_twitter         = (boolean) $core->blog->settings->socialMeta->twitter;
$sm_photo           = (boolean) $core->blog->settings->socialMeta->photo;
$sm_description     = $core->blog->settings->socialMeta->description;
$sm_image           = $core->blog->settings->socialMeta->image;

if (!empty($_POST)) {
    try
    {
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
        $core->blog->settings->addNamespace('socialMeta');
        $core->blog->settings->socialMeta->put('active', $sm_active);
        $core->blog->settings->socialMeta->put('on_post', $sm_on_post);
        $core->blog->settings->socialMeta->put('on_page', $sm_on_page);
        $core->blog->settings->socialMeta->put('twitter_account', $sm_twitter_account);
        $core->blog->settings->socialMeta->put('facebook', $sm_facebook);
        $core->blog->settings->socialMeta->put('google', $sm_google);
        $core->blog->settings->socialMeta->put('twitter', $sm_twitter);
        $core->blog->settings->socialMeta->put('photo', $sm_photo);
        $core->blog->settings->socialMeta->put('description', $sm_description);
        $core->blog->settings->socialMeta->put('image', $sm_image);

        $core->blog->triggerBlog();

        dcPage::addSuccessNotice(__('Settings have been successfully updated.'));
        http::redirect($p_url);
    } catch (Exception $e) {
        $core->error->add($e->getMessage());
    }
}

?>
<html>
<head>
  <title><?php echo __('socialMeta'); ?></title>
</head>

<body>
<?php
echo dcPage::breadcrumb(
    [
        html::escapeHTML($core->blog->name) => '',
        __('socialMeta')                    => ''
    ]);
echo dcPage::notices();

echo
'<form action="' . $p_url . '" method="post">' .
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
'<label for="sm_google" class="classic">' . __('Use Google+ social meta:') . '</label></p>' .
'<pre>' .
html::escapeHTML(
    '<!-- Google+ -->' . "\n" .
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
form::field('sm_twitter_account', 30, 128, html::escapeHTML($sm_twitter_account)) . '</p>' .
'<p class="form-note">' . __('With or without @ prefix.') . '</p>' .

'<p>' . form::checkbox('sm_photo', 1, $sm_photo) . ' ' .
'<label for="sm_photo" class="classic">' . __('This blog is a photoblog') . '</label></p>' .
'<p class="form-note">' . __('Will use "summary_large_image" twitter card type rather than "summary", and will include the first original photo if possible rather than the medium thumbnail.') . '</p>' .

'<p><label for="sm_description">' . __('Default description:') . '</label> ' .
form::field('sm_description', 80, 255, html::escapeHTML($sm_description)) . '</p>' .
'<p class="form-note">' . __('Will be used if post (or page) have no text.') . '</p>' .

'<p><label for="sm_image">' . __('Default image (URL):') . '</label> ' .
form::field('sm_image', 80, 255, html::escapeHTML($sm_image)) . '</p>' .
'<p class="form-note">' . __('Will be used if post (or page) have no image.') . '</p>' .

'<p>' . $core->formNonce() . '<input type="submit" value="' . __('Save') . '" /></p>' .
    '</form>';

?>
</body>
</html>
