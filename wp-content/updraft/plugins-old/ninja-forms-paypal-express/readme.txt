=== Ninja Forms - PayPal Express Extension ===
Contributors: kstover, jameslaws
Donate link: http://ninjaforms.com
Tags: form, forms
Requires at least: 4.0
Tested up to: 4.6
Stable tag: 3.0.2

License: GPLv2 or later

== Description ==
The Ninja Forms PayPal Express Extension allows users to accept payments through their forms using the PayPal Express system.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `ninja-forms-paypal-express` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit the 'Forms' menu item in your admin sidebar
4. When you create a form, you will have now have PayPal options on the form settings page.

== Use ==

For help and video tutorials, please visit our website: [NinjaForms.com](http://ninjaforms.com)

== Changelog ==

= 3.0.2 (06 September 2016) =

* Changing to v3.0.2 to fix compatibility issue

= 3.0.1 (06 September 2016) =

*Bugs:*

* Fixed a bug with currency settings in Ninja Forms Three.

= 3.0.0 (03 August 2016) =

* Updated with Ninja Forms v3.x compatibility
* Deprecated Ninja Forms v2.9.x compatible code

= 1.0.10 (12 May 2015) =

*Changes:*

* Added a filter for currencies.

= 1.0.9 (17 November 2014) =

*Bugs:*

* PayPal API strings should now be trimmed to help prevent improper entry.
* Fixed several i18n issues.

= 1.0.8 (28 October 2014) =

*Bugs:*

* Fixed several PHP notices.

= 1.0.7 (24 July 2014) =

*Changes:*

* Compatibility with Ninja Forms 2.7.

= 1.0.6 =

*Changes:*

* Added a debug option for PayPal Express.
* Updated the SSL PEM file used by PayPal Express.

= 1.0.5 =

*Bugs:*

* Fixed a bug that prevented PayPal Express from working with field descriptions containing HTML characters.

= 1.0.4 =

*Changes:*

* Added a defined variable to make troubleshooting PayPal errors easier.

*Bugs:*

* Fixed a minor bug that could cause errors if an equation was used for the total field.

= 1.0.3 =

*Changes:*

* Added an option to fields so that users can determine whether or not to send a field to PayPal. This means that fields can contribute to a calculation and not be sent to PayPal.

*Bugs:*

* Fixed a bug that could cause the Subtotal to be sent to PayPal incorrectly.
* Fixed a bug that caused successful transactions to be recorded as errors.

= 1.0.2 =

*Bugs:*

* Fixed a bug that prevented checkboxes from working properly with PayPal Express totals.

*Changes:*

* Changed the license registration method to the one available with Ninja Forms 2.2.47.

= 1.0.1 =

*Bugs:*

* Fixed a bug that prevented the plugin from activating properly.

= 1.0 =

* Initial release