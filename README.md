**Browser-based Language**
===============

**Name**: Browser-based Language  
**Author**: Destroy666  
**Version**: 1.1  

**Info**:
---------

Plugin for MyBB forum software, coded for versions 1.8.x (may work in 1.6.x/1.4.x after some changes).  
It sets forum language based on the HTTP_ACCEPT_LANGUAGE header sent by the browser if no language is chosen by the user.  
Released under GNU GPL v3, 29 June 2007. Read the LICENSE.md file for more information.  

**Support/bug reports**: 
------------------------

**Support**: official MyBB forum - http://community.mybb.com/mods.php?action=profile&uid=58253 (don't PM me, post on forums)  
**Bug reports**: my github - https://github.com/Destroy666x  

**Changelog**:
--------------

**1.1** - small correction
**1.0** - initial release  

**Installation**:
-----------------

1. Upload everything from upload folder to your forum root (where index.php, forumdisplay.php etc. are located).
2. Activate plugin in ACP -> Configuration -> Plugins.

**Troubleshooting**:
------------------------------

* Some stuff (login, 2FA, etc.) in ACP is not translated due to MyBB hook inaccessibility.
* You need to install any translations you want to provide to users automatically. If a browser is set to French only and there's no French translation, MyBB will load the default forum language for the browser user.
* As mentioned earlier, the browser needs to send a proper HTTP_ACCEPT_LANGUAGE header.
* Language codenames in `inc/language/[language_name].php`'s `$langinfo['htmllang']` variable have to be compatible with http://www.metamodpro.com/browser-language-codes

**Translations**:
-----------------

Feel free to submit translations to github in Pull Requests. Also, if you want them to be included on the MyBB mods site, ask me to provide you the contributor status for my project.

**Donations**:
-------------

Donations will motivate me to work on further MyBB plugins. Feel free to use the button in the ACP Plugins section anytime.  
Thanks in advance for any input.