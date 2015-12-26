<?php

/*
Name: Browser-based Language
Author: Destroy666
Version: 1.1
Info: Plugin for MyBB forum software, coded for versions 1.8.x (may work in 1.6.x/1.4.x after some changes).
It sets forum language based on the HTTP_ACCEPT_LANGUAGE header sent by the browser if no language is chosen by the user.
Released under GNU GPL v3, 29 June 2007. Read the LICENSE.md file for more information.
Support: official MyBB forum - http://community.mybb.com/mods.php?action=profile&uid=58253 (don't PM me, post on forums)
Bug reports: my github - https://github.com/Destroy666x
© 2015 - date('Y')
*/

function browser_based_language_info()
{
	global $lang;
	
	$lang->load('browser_based_language_acp');
	
	return array(
		'name'			=> $lang->browser_based_language,
		'description'	=> $lang->browser_based_language_info.'
<br />
<br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="ZRC6HPQ46HPVN">
<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" style="border: 0;" name="submit" alt="Donate">
<img alt="" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" style="border: 0; width: 1px; height: 1px;">
</form>',
		'website'		=> 'http://community.mybb.com/mods.php?action=profile&uid=58253',
		'author'		=> 'Destroy666',
		'authorsite'	=> 'https://github.com/Destroy666x',
		'codename'		=> 'browser_based_language',
		'version'		=> 1.1,
		'compatibility'	=> '*'
    );
}

function browser_based_language_activate()
{
}

function browser_based_language_deactivate()
{
}

// Highest priority in case other plugins load other language files
$plugins->add_hook('global_start', 'browser_based_language_frontend', 0);
$plugins->add_hook('admin_tabs', 'browser_based_language_backend', 0);

function browser_based_language_frontend()
{
	global $lang, $mybb;
	
	// If there's no user language or cookied language, load it automatically
	if((!isset($mybb->user['language']) || !$lang->language_exists($mybb->user['language']))
	&& (!isset($mybb->cookies['mybblang']) || !$lang->language_exists($mybb->cookies['mybblang'])))
	{
		$languages = get_preferred_languages();
		
		foreach($languages as $langcode => $priority)
		{
			$avi = check_if_lang_available(trim($langcode), $mybb->settings['bblanguage']);
			
			if($avi === true)
				break;
			elseif($avi !== false)
			{
				$mybb->settings['bblanguage'] = $avi;
				$lang->set_language($avi);
				$lang->load('global');
				$lang->load('messages');
				
				break;
			}
		}
	}
}

function browser_based_language_backend($modules)
{
	global $lang, $admin_options;
	
	// If there's no set CP language, load it automatically
	if(!isset($admin_options['cplanguage']) || !$lang->language_exists($admin_options['cplanguage']))
	{
		$languages = get_preferred_languages();
		
		foreach($languages as $langcode => $priority)
		{
			$avi = check_if_lang_available(trim($langcode), $mybb->settings['cplanguage'], true);
			
			if($avi === true)
				break;
			elseif($avi !== false)
			{
				global $page;
				
				$lang->set_language($avi, 'admin');
				$lang->load('global');
				$lang->load('messages', true);
				
				// Override menu and breadcrumb to set proper language strings..
				$page->_menu = array();
				
				foreach($modules as $m => $p)
				{
					if($p == 1)
					{
						$lang->load("{$m}_module_meta", false, true);
						$meta_function = "{$m}_meta";
						$meta_function();
					}
				}
				
				$page->_breadcrumb_trail = array();
				$page->add_breadcrumb_item($lang->home, "index.php");
				
				break;
			}
		}
	}
	
	return $modules;
}

/**
 * Gets languages and their priorities based on the HTTP_ACCEPT_LANGUAGE header sent by the browser.
 *	
 * @return array Language codenames (keys) and their priorities (values) sorted by priority.
 */
function get_preferred_languages()
{
	$languages = array();
	
	if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
	{
		preg_match_all('/([a-z]{1,3}(-[a-z]{1,2})?)(\s*;\s*q\s*=\s*(0\.[0-9]+|1))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $parts);
		
		if($parts)
		{
			// There may be no priority, set it to 1 in that case
			foreach($parts[4] as &$priority)
				$priority = $priority ? (float)$priority : 1;
			
			$languages = array_combine($parts[1], $parts[4]);
			arsort($languages);
		}
	}
	
	return $languages;
}

/**
 * Checks whether language codename is available in one of translations.
 * Codenames have to be compatible with http://www.metamodpro.com/browser-language-codes
 *
 * @param string $codename Language codename to check.
 * @param string $avoid Language filename which should be prevented from loading again.
 * @param bool $admin Whether ACP files are needed or not.
 * @return string|bool Language name to load if it exists, true if already loaded, false if not.
 */
function check_if_lang_available($codename, $avoid, $admin=false)
{
	// Cache available codenames
	static $codename_cache = array();
	
	if($codename_cache)
	{
		if(isset($codename_cache[$codename]))
		{
			if($codename_cache[$codename] != $avoid)
				return $codename_cache[$codename];
			else
				return true;
		}
		
		return false;
	}
	
	global $lang;
	
	$languages = $lang->get_languages($admin);
	
	foreach($languages as $filename => $name)
	{
		require "{$lang->path}/{$filename}.php";
		
		if($langinfo['htmllang'] == $codename)
		{
			if($filename != $avoid)
				return $filename;
			else
				return true;
		}

		$codename_cache[$langinfo['htmllang']] = $filename;
	}
	
	return false;
}