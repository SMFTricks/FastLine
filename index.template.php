<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	/* The version this template/theme is for.
		This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '2.0';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = false;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = true;
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?fin20" />';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />';
	
	// Buttonlist new style
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/buttonlistvariant.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	// Here comes the JavaScript bits!
	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?fin20"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : '', '
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>';

	echo '
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings, $boardurl;

echo '
<div class="main_header">';

		echo !empty($settings['forum_width']) ? '
	<div id="wrapper" style="width: ' . $settings['forum_width'] . '">' : '', '';
		
		// The top bar
		top_bar();
	
	echo '
		<div id="header">
			<h1 class="forumtitle">
				<a href="', $scripturl, '">', empty($context['header_logo_url_html_safe']) ? '<img src="' . $settings['images_url'] . '/theme/logo.png" alt="' . $context['forum_name'] . '" />' : '<img src="' . $context['header_logo_url_html_safe'] . '" alt="' . $context['forum_name'] . '" />', '</a>
			</h1>';
		
		// The user area
		user_area();
	
	
		echo '
			<br class="clear" />
		</div>', !empty($settings['forum_width']) ? '
	</div>' : '';

echo '
</div>';
	
	// Show the menu here, according to the menu sub template.
	template_menu();
	
	echo !empty($settings['forum_width']) ? '
<div id="wrapper2" style="width: ' . $settings['forum_width'] . '">' : '', '';
	
	// Show the navigation tree.
	theme_linktree_main();

	// The main content should go here.
	echo '
	<div id="content_section">
		<div id="main_content_section">';

	// Custom banners and shoutboxes should be placed here, before the linktree.

}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
		</div>
	</div>';

	// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
	echo '
	<div id="footer_section">
		<div class="floatright">
			<div id="quicknav">
				<ul>
					<li style="display: none:"></li>';
							if(!empty($settings['twitter_username']))
							echo '
					<li><a class="social_icon twitter" href="https://twitter.com/', $settings['twitter_username'] , '" target="_blank" rel="noopener"></a></li>';
					
							if(!empty($settings['facebook_username']))
							echo '
					<li><a class="social_icon facebook" href="https://facebook.com/', $settings['facebook_username'] , '" target="_blank" rel="noopener"></a></li>';
										
							if(!empty($settings['youtube_username']))
							echo '
					<li><a class="social_icon youtube" href="https://youtube.com/', $settings['youtube_username'] , '" target="_blank" rel="noopener"></a></li>';
					
							echo '
					<li><a class="social_icon rss" href="', empty($settings['rss_url']) ? '' . $scripturl . '?action=.xml;type=rss' : '' . $settings['rss_url'] . '', '" target="_blank" rel="noopener"></a></li>';

						echo '
				</ul>	
			</div>
		</div>
		<ul class="reset left">
			<li class="copyright">', theme_copyright(), '</li>
			<li class="copyright">', SH_copyright(), ' | <a href="'. $scripturl. '">', $context['forum_name'], ' &copy; ', date('Y'), '</a></li>
			<li><a id="button_xhtml" href="http://validator.w3.org/check?uri=referer" target="_blank" class="new_win" title="', $txt['valid_xhtml'], '"><span>', $txt['xhtml'], '</span></a></li>
			', !empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']) ? '<li><a id="button_rss" href="' . $scripturl . '?action=.xml;type=rss" class="new_win"><span>' . $txt['rss'] . '</span></a></li>' : '', '
			<li class="last"><a id="button_wap2" href="', $scripturl , '?wap2" class="new_win"><span>', $txt['wap2'], '</span></a></li>
		</ul>';

	// Show the load time?
	if ($context['show_load_time'])
		echo '
		<p>', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</p>';

	echo '
		<br class="clear" />
	</div>', !empty($settings['forum_width']) ? '
</div>' : '';
}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	echo '
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree_main($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
	<div class="main_page_lnktree">
	<div class="navigate_section">
		<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		echo '
			</li>';
	}
	echo '
		</ul>
	</div>
	</div>';

	$shown_linktree = true;
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
	<div class="navigate_section">
		<ul>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		echo '
			</li>';
	}
	echo '
		</ul>
	</div>';

	$shown_linktree = true;
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
		<div class="wrapper_totalmenu">';
		
	echo !empty($settings['forum_width']) ? '
		<div class="main_bar" style="width: ' . $settings['forum_width'] . '">' : '', '';
		
		echo '
			<ul class="menu">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li id="button_', $act, '">
					<a class="', $button['active_button'] ? 'active ' : '', 'firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
						<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $button['title'], '</span>
					</a>';
		if (!empty($button['sub_buttons']))
		{
			echo '
					<ul>';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
						<li>
							<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
								<span', isset($childbutton['is_last']) ? ' class="last"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</span>
							</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
							<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
								<li>
									<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
										<span', isset($grandchildbutton['is_last']) ? ' class="last"' : '', '>', $grandchildbutton['title'], '</span>
									</a>
								</li>';

					echo '
							</ul>';
				}

				echo '
						</li>';
			}
				echo '
					</ul>';
		}
		echo '
				</li>';
	}

	echo '
			</ul>';
			
	echo '
		', !empty($settings['forum_width']) ? '
		</div>' : '';
		
	echo '
		</div>';
}

// The top bar, for links and that stuff
function top_bar()
{
	global $settings, $txt, $context, $scripturl;
	
	echo '
		<div class="top_bar">
			<ul class="links floatleft">
				<li><a href="', empty($settings['link_top1_url']) ? '' : '' . $settings['link_top1_url'] . '', '">', empty($settings['link_top1']) ? $txt['link_top1'] : $settings['link_top1'], '</a></li>
				<li><a href="', empty($settings['link_top2_url']) ? '' : '' . $settings['link_top2_url'] . '', '">', empty($settings['link_top2']) ? $txt['link_top2'] : $settings['link_top2'], '</a></li>
				<li><a href="', empty($settings['link_top3_url']) ? '' : '' . $settings['link_top3_url'] . '', '">', empty($settings['link_top3']) ? $txt['link_top3'] : $settings['link_top3'], '</a></li>
				<li><a href="', empty($settings['link_top4_url']) ? '' : '' . $settings['link_top4_url'] . '', '">', empty($settings['link_top4']) ? $txt['link_top4'] : $settings['link_top4'], '</a></li>
				<li><a href="', empty($settings['link_top5_url']) ? '' : '' . $settings['link_top5_url'] . '', '">', empty($settings['link_top5']) ? $txt['link_top5'] : $settings['link_top5'], '</a></li>
			</ul>';
			
	if ($context['in_maintenance'] && $context['user']['is_admin'] || !empty($context['unapproved_members']) || !empty($context['open_mod_reports']) && $context['show_open_reports'])
	{
	echo '
			<div class="header_buttons">';
		
	// Is the forum in maintenance mode?
		if ($context['in_maintenance'] && $context['user']['is_admin'])
			echo '
				<div class="header_button"><a href="', $scripturl, '?action=admin">', $txt['maintain_mode_on'], '</a></div>';

		// Are there any members waiting for approval?
		if (!empty($context['unapproved_members']))
			echo '
				<div class="header_button"><a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $context['unapproved_members'] == 1 ? $txt['approve_thereis'] : $txt['approve_thereare'], ' ', $context['unapproved_members'] == 1 ? $txt['approve_member'] : $context['unapproved_members'] . ' ' . $txt['approve_members'], ' ', $txt['approve_members_waiting'], '</a></div>';

		if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
			echo '
				<div class="header_button"><a href="', $scripturl, '?action=moderate;area=reports">', sprintf($txt['mod_reports_waiting'], $context['open_mod_reports']), '</a></div>';
				
	echo '
			</div>';
	}
		
	elseif (!empty($context['user']['is_logged']))
		echo '
			<div class="header_buttons">
				<div class="header_button"><a href="', $scripturl, '?action=unread">', $txt['unread_topics_visit'], '</a></div>
				<div class="header_button"><a href="', $scripturl, '?action=unreadreplies">', $txt['unread_replies'], '</a></div>
			</div>';
	
	else
		echo '
			<div class="header_buttons">
				<div class="header_button"><a href="', $scripturl, '?action=register">', $txt['register'], '</a></div>
				<div class="header_button"><a href="', $scripturl, '?action=login">', $txt['login'], '</a></div>
			</div>';
			
	echo '
		</div>';
}

// Theme copyright, DO NOT REMOVE THIS!
function SH_copyright()
{
	$SH = ' Theme by <a href="https://smftricks.com">SMF Tricks</a>';
	return $SH;	
}

// And then... The user area
function user_area()
{
	global $settings, $txt, $scripturl, $context;
	
	echo '
		<div class="user_info">';
		
	// Some buttons
	if (!empty($context['user']['is_logged']))
	{
		echo '
			<img class="avatar" src="', !empty($context['user']['avatar']['href']) ? $context['user']['avatar']['href'] : $settings['images_url']. '/theme/noavatar.png', '" alt="', $txt['profile'], '" />
			<span class="name"><a href="'. $scripturl. '?action=profile">', $context['user']['name'], '</a></span><br />
			<div class="block">
				<ul class="reset">
					<li class="option"><a href="', $scripturl, '?action=profile;area=forumprofile"><img src="', $settings['images_url'], '/md/user/forumprofile.png" alt="" /></a></li>
					<li class="option"><a href="', $scripturl, '?action=profile;area=account"><img src="', $settings['images_url'], '/md/user/account.png" alt="" /></a></li>
					<li class="option"><a href="', $scripturl, '?action=profile;area=statistics"><img src="', $settings['images_url'], '/md/user/stats.png" alt="" /></a></li>
					<li class="option"><a href="', $scripturl, '?action=profile;area=showposts"><img src="', $settings['images_url'], '/md/user/posts.png" alt="" /></a></li>
				</ul>
			</div>';
	}
	// Otherwise they're a guest - this time ask them to either register or login - lazy bums...
	else
	{
			echo '
			<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/sha1.js"></script>
			<form id="guest_form" action="', $scripturl, '?action=login2" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
				<div class="info">', $txt['hello_guest']. ' ' .$context['user']['name'], '</div>
				<input type="text" name="user" size="10" class="input_text" />
				<input type="password" name="passwrd" size="10" class="input_password" />
				<select name="cookielength">
					<option value="60">', $txt['one_hour'], '</option>
					<option value="1440">', $txt['one_day'], '</option>
					<option value="10080">', $txt['one_week'], '</option>
					<option value="43200">', $txt['one_month'], '</option>
					<option value="-1" selected="selected">', $txt['forever'], '</option>
				</select>
				<input type="submit" value="', $txt['login'], '" class="button_submit" /><br />
				<div class="info">', $txt['quick_login_dec'], '</div>';
	
			if (!empty($modSettings['enableOpenID']))
				echo '
				<br /><input type="text" name="openid_identifier" id="openid_url" size="25" class="input_text openid_login" />';
	
			echo '
				<input type="hidden" name="hash_passwrd" value="" />
				<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
			</form>';
	}
	
	echo '
		</div>';
	

}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}

?>