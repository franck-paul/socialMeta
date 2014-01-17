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

$new_version = $core->plugins->moduleInfo('socialMeta','version');
$old_version = $core->getVersion('socialMeta');

if (version_compare($old_version,$new_version,'>=')) return;

try
{
	$core->blog->settings->addNamespace('socialMeta');
	$core->blog->settings->socialMeta->put('active',false,'boolean','Active',false,true);
	$core->blog->settings->socialMeta->put('on_post',true,'boolean','Add social meta on post',false,true);
	$core->blog->settings->socialMeta->put('on_page',false,'boolean','Add social meta on page',false,true);
	$core->blog->settings->socialMeta->put('twitter_account','','string','Twitter account',false,true);

	$core->setVersion('socialMeta',$new_version);

	return true;
}
catch (Exception $e)
{
	$core->error->add($e->getMessage());
}
return false;
