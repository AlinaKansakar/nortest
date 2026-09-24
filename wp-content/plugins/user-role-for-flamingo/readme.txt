=== User Role for Flamingo ===
Contributors: yannickzipf
Tags: flamingo, forms, user role, capabilities, contact form 7
Tested up to: 6.4
Requires PHP: 7.0
Requires at least: 5.0
Stable tag: 1.0.1
License: GPLv2 or later

Configure special user role to access the flamingo contacts and messages wihtout admin permissions.

== Description ==
Imagine a situation where you (administrator) have an RSVP form through Contact Form 7 on your page and colleact the responses via Flamingo. You want somebody of your staff to oversee and handle the responses. Until now you would have to create a wordpress user and grant admin permissions to this user. This is because users without admin permissions cannot access the contacts and responses captured by Flamingo.
But now: ✨ TADA! ✨
This plugin creates a special role, which you can assign to your staff. Only admins can then decide, which specific Flamingo permissions this role should have. Your member of staff can then only access the Flamingo pages granted to this role.

= Credits =
[Flamingo Vectors by Vecteezy](https://www.vecteezy.com/free-vector/flamingo)

== Installation ==
Installing the plugin is easy. Just follow one of the following methods:

= Install Results for Handball4All from within Wordpress =

1. Visit the plugins page within your dashboard and select ‘Add New’
2. Search for \"Results for Handball4All\"
3. Activate Results for Handball4All from your Plugins page
4. You\'re done!

= Install Results for Handball4All Manually =

1. From the dashboard of your site, navigate to Plugins --> Add New.
2. Select the Upload option and hit \"Choose File.\"
3. When the popup appears select the results-h4a-x.x.zip file from your desktop. (The \'x.x\' will change depending on the current version number).
4. Follow the on-screen instructions and wait as the upload completes.
5. When it\'s finished, activate the plugin via the prompt. A message will show confirming activation was successful.

That\'s it! 

== Frequently Asked Questions ==
= Do I need the Flamingo plugin to use the User Role for Flamingo plugin? =

Yes, of course. Running User Role for Flamingo without the Flamingo plugin will have no effect.

= Where can I control the capabilities of the new user role? =

You can find the settings page in the WordPress Backend under Users > Flamingo User Role.

= How do I assign this new user role to a user? =

You do this as you already assigned the Administrator role to your user.
Use the following steps:
Users > All Users
Choose a User > Edit
Under \'Role\' choose \'Flamingo User\'

= Can I rename the new Role from \'Flamingo User\' to something different? =

No.

== Screenshots ==
1. Location of the settings page in the admin menu (only Administrators)
2. Settings page to configure the Flamingo user role (only Administrators)
3. Role assignment to a test-user (only Administrators)
4. Menu of user with Flamingo user role.

== Changelog ==
= [1.0.1] 2023-11-26 =
* Docs: Tested up to WordPress 6.4
= [1.0.0] 2022-11-14 =
* Initial Version