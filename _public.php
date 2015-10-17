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

if (!defined('DC_RC_PATH')) { return; }

$core->addBehavior('publicHeadContent', array('dcSocialMeta','publicHeadContent'));

class dcSocialMeta
{
	public static function publicHeadContent()
	{
		global $core,$_ctx;

		$core->blog->settings->addNamespace('socialMeta');
		if ($core->blog->settings->socialMeta->active)
		{
			if (($core->url->type == 'post') || ($core->url->type == 'pages')) {

				if (($_ctx->posts->post_type == 'post' && $core->blog->settings->socialMeta->on_post) ||
					($_ctx->posts->post_type == 'page' && $core->blog->settings->socialMeta->on_page))
				{
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
					$content = $_ctx->posts->getExcerpt().' '.$_ctx->posts->getContent();
					$content = html::decodeEntities(html::clean($content));
					$content = preg_replace('/\s+/',' ',$content);
					$content = html::escapeHTML($content);
					$content = text::cutString($content,180);
					// Post/Page first image
					$img = context::EntryFirstImageHelper('s','',false,true);
					if (strlen($img) && substr($img,0,4) != 'http') {
						$root = preg_replace('#^(.+?//.+?)/(.*)$#','$1',$core->blog->url);
						$img = $root.$img;
					}

					if ($core->blog->settings->socialMeta->facebook) {
						// Facebook meta
						echo
						'<!-- Facebook -->'."\n".
						'<meta property="og:title" content="'.$title.'" />'."\n".
						'<meta property="og:description" content="'.$content.'" />'."\n";
						if (strlen($img)) {
							echo
							'<meta property="og:image" content="'.$img.'" />'."\n";
						}
					}
					if ($core->blog->settings->socialMeta->google) {
						// Google+
						echo
						'<!-- Google+ -->'."\n".
						'<meta itemprop="name" content="'.$title.'" />'."\n".
						'<meta itemprop="description" content="'.$content.'" />'."\n";
						if (strlen($img)) {
							echo
							'<meta itemprop="image" content="'.$img.'" />'."\n";
						}
					}
					if ($core->blog->settings->socialMeta->twitter) {
						// Twitter account
						$account = $core->blog->settings->socialMeta->twitter_account;
						if (strlen($account) && substr($account,0,1) != '@') {
							$account = '@'.$account;
						}
						// Twitter
						echo
						'<!-- Twitter -->'."\n".
						'<meta name="twitter:card" content="summary" />'."\n".
						'<meta name="twitter:url" content="'.$url.'" />'."\n".
						'<meta name="twitter:title" content="'.$title.'" />'."\n".
						'<meta name="twitter:description" content="'.$content.'" />'."\n";
						if (strlen($img)) {
							echo
							'<meta name="twitter:image:src" content="'.$img.'"/>'."\n";
						}
						if (strlen($account)) {
							echo
							'<meta name="twitter:site" content="'.$account.'" />'."\n".
							'<meta name="twitter:creator" content="'.$account.'" />'."\n";
						}
					}
				}
			}
		}
	}
}
