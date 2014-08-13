=== typofr ===
Contributors: boris.schapira
Tags: typography
Tested up to: 3.9
Stable tag: 0.6
Requires at least: 3.0
License: MIT
License URI: https://raw.githubusercontent.com/borisschapira/typofr/master/LICENSE

A Wordpress plugin for french typography management, powered by the JoliTypo library.

== Description ==

Don't you ever get tired of your CMS bad management of french typography ?
Ellipsis, hyphenation, quotes, there are a lot of things that are not well managed by neither the browser, nor Wordpress. And on a Responsive Web Design, a bad arrangement of characters can be very ugly.

TypoFR is a Wordpress plugin for french typography management, powered by [JoliTypo](https://github.com/jolicode/JoliTypo), that solve all of the microtyphic glitches inside your HTML content.

== Warnings ==

1. Because of its object-oriented, namespaced code, TypoFR is not available for Wordpress blogs running on PHP <= 5.3.2.
2. TypoFR corrects all of your content just-in-time. Your blog can suffer from degraded performances if you do not use a cache-management plugin, like [W3 Total Cache](https://wordpress.org/plugins/w3-total-cache/ "W3 Total Cache Plugin").

== Features ==

TypoFR uses JoliTypo for content fixing :
* Dimension : replaces the letter x between numbers (12 x 123) by a times entity (×, the real math symbol).
* Ellipsis : replaces the three dot ... by an ellipsis ….
* FrenchQuotes : converts dumb quotes " " to smart English style quotation marks “ ”.
* FrenchNoBreakSpace : replaces some classic spaces by non breaking spaces following the French typographic code. No break space are placed before :, thin no break space before ;, ! and ?.
* Hyphen (automatic hyphenation) : enables word-hyphenation, using the pattern-files from OpenOffice which are based on the pattern-files created for TeX
* CurlyQuote (Smart Quote) : replace straight quotes ' by curly one's ’.
* Trademark : handle trade­mark symbol ™, a reg­is­tered trade­mark symbol ®, and a copy­right symbol ©. This fixer replace commonly used approximations: (r), (c) and (TM). A non-breaking space is put between numbers and copyright symbol too.

More information on JoliTypo fixers on the [JoliTypo GitHub Repository](https://github.com/jolicode/JoliTypo).

== Installation ==

1. Copy the `typofr` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go make something fun of your life, you've finished here.

== Screenshots ==

1. Wordpress admin settings management screenshot-1.png.
2. What does this plugin ? See an example with screenshot-2.png

== Changelog ==

= 0.6 =

* Fix issue with options [yes/no], not considered

= 0.5 =

* Better UTF8 management (both removing encoding manipulation in TypoFR and improving JoliTypo dependancy). See : https://github.com/jolicode/JoliTypo/issues/7

= 0.4 =

* Adding i18n support
* Adding french translation

= 0.3 =

* Better OOP structure
* Back-Office settings for content to fix
* Back-Office settings for fix to apply

= 0.2 =

* Added a message regarding the installation of a cache-management plugin

= 0.1 =

* Use of Jolitypo as a typographic library

== Thanks ==

* https://github.com/damienalexandre, lead developer of the JoliTypo library
* https://github.com/darklg for its wordpress skills and input