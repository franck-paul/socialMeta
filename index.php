<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of socialMeta, a plugin for Dotclear 2.
#
# Copyright (c) Franck Paul and contributors
# carnet.franck.paul@gmail.com
#
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_CONTEXT_ADMIN')) { return; }

$core->blog->settings->addNamespace('socialMeta');
if (is_null($core->blog->settings->socialMeta->active)) {
	try {
		// Add default settings values if necessary
		$core->blog->settings->socialMeta->put('active',false,'boolean','Active',false);
		$core->blog->settings->socialMeta->put('on_post',true,'boolean','Add social meta on post',false);
		$core->blog->settings->socialMeta->put('on_page',false,'boolean','Add social meta on page',false);
		$core->blog->settings->socialMeta->put('twitter_account','','string','Twitter account',false);

		$core->blog->triggerBlog();
		http::redirect($p_url);
	}
	catch (Exception $e) {
		$core->error->add($e->getMessage());
	}
}

$sm_active = (boolean) $core->blog->settings->socialMeta->active;
$sm_on_post = (boolean) $core->blog->settings->socialMeta->on_post;
$sm_on_page = (boolean) $core->blog->settings->socialMeta->on_page;
$sm_twitter_account = $core->blog->settings->socialMeta->twitter_account;

if (!empty($_POST))
{
	try
	{
		$sm_active = !empty($_POST['sm_active']);
		$sm_on_post = !empty($_POST['sm_on_post']);
		$sm_on_page = !empty($_POST['sm_on_page']);
		$sm_twitter_account = trim(html::escapeHTML($_POST['sm_twitter_account']));

		# Everything's fine, save options
		$core->blog->settings->addNamespace('socialMeta');
		$core->blog->settings->socialMeta->put('active',$sm_active);
		$core->blog->settings->socialMeta->put('on_post',$sm_on_post);
		$core->blog->settings->socialMeta->put('on_page',$sm_on_page);
		$core->blog->settings->socialMeta->put('twitter_account',$sm_twitter_account);

		$core->blog->triggerBlog();

		dcPage::addSuccessNotice(__('Settings have been successfully updated.'));
		http::redirect($p_url);
	}
	catch (Exception $e)
	{
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
	array(
		html::escapeHTML($core->blog->name) => '',
		__('socialMeta') => ''
	));
echo dcPage::notices();

echo
'<form action="'.$p_url.'" method="post">'.
'<p>'.form::checkbox('sm_active',1,$sm_active).' '.
'<label for="sm_active" class="classic">'.__('Active socialMeta').'</label></p>'.

'<h3>'.__('Options').'</h3>'.

'<p>'.form::checkbox('sm_on_post',1,$sm_on_post).' '.
'<label for="sm_on_post" class="classic">'.__('Add social meta on posts').'</label></p>'.
'<p>'.form::checkbox('sm_on_page',1,$sm_on_page).' '.
'<label for="sm_on_page" class="classic">'.__('Add social meta on pages').'</label></p>'.

'<h3>'.__('Settings').'</h3>'.

'<p><label for="sm_twitter_account">'.__('Twitter account:').'</label> '.
form::field('sm_twitter_account',30,128,html::escapeHTML($sm_twitter_account)).'</p>'.
'<p class="form-note">'.__('With or without @ prefix.').'</p>'.

'<p>'.$core->formNonce().'<input type="submit" value="'.__('Save').'" /></p>'.
'</form>';

?>
</body>
</html>
