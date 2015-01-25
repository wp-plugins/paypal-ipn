=== PayPal IPN for WordPress ===
Contributors: angelleye
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SG9SQU2GBXJNA
Tags: paypal, ipn, instant payment notification, automation
Requires at least: 3.8
Tested up to: 4.1
Stable tag: 1.0.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Developed by an Ace Certified PayPal Developer, official PayPal Partner, PayPal Ambassador, and 3-time PayPal Star Developer Award Winner.

== Description ==

= Introduction =

A PayPal Instant Payment Notification (IPN) toolkit that helps you automate tasks in real-time when transactions hit your PayPal account.

 * All PayPal IPN data is saved and available in your WordPress admin panel.
 * Developer hooks are provided for triggering events based on the transaction type or payment status of the IPN.
 * Extend the plugin with your own plugins or theme functions, or check out our premium extensions for easy automation of various tasks.

== Installation ==

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type "PayPal IPN" and click Search Plugins. Once you've found our plugin you can view details about it such as the the rating and description. Most importantly, of course, you can install it by simply clicking Install Now.

= Manual Installation =

1. Unzip the files and upload the folder into your plugins folder (/wp-content/plugins/) overwriting previous versions if they exist
2. Activate the plugin in your WordPress admin area.

== Screenshots ==

1. Categorized browser for all IPN transactions.
2. Parsed transaction data for an individual IPN.

== Frequently Asked Questions ==

= What is PayPal Instant Payment Notification (IPN)? =

Instant Payment Notification (IPN) is a message service that notifies you of events related to PayPal transactions. You can use IPN messages to automate back-office and administrative functions, such as fulfilling orders, tracking customers, and providing status and other transaction-related information.

= How do I enable IPN in my PayPal account? =

Take a look at [PayPal's IPN Setup Guide](https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNSetup/) for details on enabling IPN within your PayPal account.

You can find your IPN URL under Settings -> PayPal IPN in your WordPress admin panel.

= Where can I find more detailed documentation? =

We have [documentation available on our website](http://www.angelleye.com/category/docs/paypal-ipn-for-wordpress/).

== Changelog ==

= 1.0.1 =
* Fix - Adjusts post type icon so it will work regardless of plugin folder name.

= 1.0.0 =

* Logs all IPN transaction data in the WordPress database.
* Provides developer hooks for extending functionality and automating tasks within extension plugins.