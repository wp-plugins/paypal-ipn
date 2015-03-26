=== PayPal IPN for WordPress ===
Contributors: angelleye
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SG9SQU2GBXJNA
Tags: paypal, ipn, instant payment notification, automation
Requires at least: 3.8
Tested up to: 4.1.1
Stable tag: 1.0.4
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

Some things you could potentially do with IPN are...

* Automatically generate custom, branded email notifications for buyers and/or sellers.
* Automatically update databases when transactions occur for customer, order, and inventory tracking.
* Automatically post new messages on a Facebook or Twitter account when an item sells.
* Automatically deliver e-goods for digital products like music, videos, and documents.

You can automate all sorts of things with IPN, so the list really goes on and on.  Also, it all happens in instantly as transactions hit your PayPal account.  It's really a very powerful tool that too few people utilize.

= How do I enable IPN in my PayPal account? =

* Take a look at [PayPal's IPN Setup Guide](https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNSetup/) for details on enabling IPN within your PayPal account.
* You can find your IPN URL under Settings -> PayPal IPN in your WordPress admin panel.

= Where can I find more detailed documentation? =

* We have [documentation available on our website](http://www.angelleye.com/category/docs/paypal-ipn-for-wordpress/).

= Why am I not seeing transactions in my PayPal IPN dashboard in WordPress? =

* Make sure you have added the IPN URL for the plugin (Available in WordPress under Settings -> PayPal IPN) to your PayPal profile under the [IPN settings area](https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNSetup/).
* If you are using PayPal Standard Payments buttons, make sure you don't have the "notify" parameter set to some other URL.
* If you are using PayPal API's, make sure you don't have the NOTIFYURL parameter set to some other URL.
* If you still have problems you may [start a thread in the plugin support forum](https://wordpress.org/support/plugin/paypal-ipn).

= How can I test that my IPN solution is working as expected? =

* Take a look at [this article I wrote covering the topic of general IPN testing and troubleshooting](https://www.angelleye.com/test-paypal-ipn/).  I think it will help!

== Changelog ==

= 1.0.4 - 03.26.2015 =
* Fix - Resolves issue where direct hits to the IPN URL were creating empty records in the system.
* Fix - Resolves PHP notices when updating plugin.
* Fix - Resolves issue where search functionality was not working correctly.
* Tweak - Search now works across all columns.
* Feature - Adaptive Payments compatibility.

= 1.0.3 - 01.28.2015 =
* Fix - More adjustments to resolve issues with plugin repo name change.
* Fix - Corrects random typos through-out the plugin.

= 1.0.2 - 01.27.2015 =
* Fix - Adjusts areas of the plugin where the slug needed to be updated to match repo name.
* Fix - Adjusts WooCommerce compatibility so that IPN forwarding will not occur unless PayPal Standard is enabled.

= 1.0.1 =
* Fix - Adjusts post type icon so it will work regardless of plugin folder name.

= 1.0.0 =

* Logs all IPN transaction data in the WordPress database.
* Provides developer hooks for extending functionality and automating tasks within extension plugins.