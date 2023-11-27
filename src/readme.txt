=== Translatio ===
Contributors: yushao
Link: https://www.translatio.io/
Tags: translation, localization, multilingual, language, woocommerce
Requires at least: 5.0
Tested up to: 6.0.2
Stable tag: 1.9.4
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Make your website multilingual ready at ease with live translation or with support of full translation cycle, with machine translation integration.

== Description ==

Translatio is an open source tool for internationalization and localization of Wordpress based websites. Translatio provides two translation workflows:

* Live Translation workflow
* Full Professional Translation workflow

Live translation workflow provides instant translation based on Google Translate engine. Simply configure the extra languages you want to support through the intutuive setup interface and save, then you are done.

Full Professional workflow provides more control and higher translation quality, which involves extracting the text for translation, translating and integrating back into Wordpress. More specifically Translatio plugin hosts your translations locally for proof reading, editing and final publication. You can intergrate machine translation of your choice to prepare your contents, then leveraging fully featured translation editor with any 3rd party translation agencies.

Translatio provides multiple ways to handle different development phases of the Wordpress websites with intuitive and easy to use interfaces, some features include:

* Live Translation powered by Google Translate
* Support Taxonomies translation, including Categories, Tags
* Support Woo Commerce 
* Full cycle to keep translation locally for proof read, edit and publish.
* Support Search Engeine Optimizaion/SEO URL
* Support new block Gutenberg editor and classic editor.
* Support Google Translate integration with editing capability
* Language switcher based on
  * Draggable floating meanu
  * Sidebar widget
  * Along with title or description
  * With any page or post
* Language switcher is in language name or flags
* Detect browser language setting
* Support browser cookie
* Premium service available
* Live support community

== Installation ==

1. Install and Activate "Translatio" from the Plugin directory.
1. Setup the plugin from Translatio -> Setup.

More information at [Translatio for Wordpress Getting Started & FAQ](https://github.com/translatio-io/translatio)

== Frequently Asked Questions ==

= There is no translation after I setup everything 

Make sure translation is enabled in the Translatio Setup page:

<kbd>![Enable Translation](https://github.com/translatio-io/translatio/blob/master/doc/translatio-enabletranslation.png "Translatio Enable Translation")</kbd>

= Translatio shows connecting error code 7 in Wordpress

On CentOS/Feodra Linux system, the error is mostly due to the SE Linux setting which blocks the network connection, using following command to change the SELinux setting: setsebool httpd_can_network_connect on


== Screenshots ==

1. Main Menu
2. Setup Page
3. Live Translation
4. Start Translate Posts
5. Finish Translation
6. Language Switcher Locations
7. Translations Page
8. 3 Usage Modes
9. Setup Translatio External Editor
10. External Editor Screen
11. Globalization Switcher Block
12. Globalization Switcher Widget

== Changelog ==

= 1.9.4 =
* adding email filed, submenu adjustment

= 1.9.3 =
* Translatio widget/block/menu update

= 1.9.2 =
* update some urls to translatio.io

= 1.9.1 =
* change names to Translatio

= 1.9.0 =
* email translation

= 1.8.9 =
* Wooocmmerce product names in order confirmation translation
* Globalization i18n domain, POT, Chinese translations

= 1.8.0 =
* Wooocmmerce improvements

= 1.7.0 =
* Taxonomies Translation

= 1.6.5 =
* SEO compatible URL

= 1.6.0 =
* tested with woocommerce

= 1.5.0 =
* added bulk action for Page and Post and other UI improvement

= 1.4.0 =
* "g11n_translation" listing page add columns

= 1.3.0 =
* Live Translation

= 1.2.0 =
* Miscellaneous Improvement.

= 1.1.0 =
* Miscellaneous Improvement.
* Machine translation integration

= 1.0.0 =
* Initial Release.

== Upgrade Notice ==

= 1.9.0 =
* email translation

= 1.8.0 =
* Wooocmmerce improvements

= 1.7.0 =
* Taxonomies Translation

= 1.6.5 =
* SEO compatible URL

= 1.6.0 =
* tested with woocommerce

= 1.5.0 =
* added bulk action for Page and Post and other UI improvement

= 1.4.0 =
* "g11n_translation" listing page add columns

= 1.3.0 =
* Live Translation is added. It is powered by Google Translate

= 1.2.0 =
* Miscellaneous Improvement.

= 1.1.0 =
* Miscellaneous Improvement.
* Machine translation integration
