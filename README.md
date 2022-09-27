=== Multisite Author Bio ===

Contributors: CodeAdapted
Tags: multisite, multisite author bio, author bio variations, multisite user bio, user bio variations, author bio, user description
Requires at least: 5.0 or higher
Tested up to: 6.0.2
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Multisite Author Bio allows you to add unique user biographical information for each Multisite instance.

== Description ==

Although there are a few other plugins out there that allow you to create author bio variations for each multisite instance, none offer the simplicity that Multisite Author Bio does.

Gone are the days of having to manually switch between sites in your network to update or view the user's biographical information. With Multisite Author Bio you can view and update all of the different variations of the author bio from the same user edit page.

For instance, let's say you have 10 sites in you network and you are currently in the `test.example.com` WP Admin and you want to create a variation of the author bio for a user on each site. Instead of having to open up a new window or tab to update the bio for each site you can do it all from the user's edit page from the `test.example.com` site. This saves time and saving time is always a plus.

Once, installed and activated the plugin is ready to rock and roll. No additional configuration is necessary.

### Important

Although installing and activating the plugin won't affect a normal website, Multisite Author Bio is meant to work on a Multisite network and, thus, does not benefit you unless you have multisite enabled.

Please keep in mind that the options added to the database are not removed on uninstall by default in order to preserve data. If you would like to clear all data added by the plugin you can go to the plugin's settings page and check `Clear translation data on uninstall`.

### For Developers

The data is stored in the main network's site database to limit the amount of data created by the plugin.
- `mab_clear_data` is stored in `wp_options` and determines whether the plugin's data should be removed on uninstall.
- `mab_profile_bio_[site_name]` is stored in `wp_usermeta` and is the author bio variant for `[site_name]`.

Multisite Author Bio uses the to `get_the_author_user_description` filter to update the author bio for each network site. If an author bio variation is not present for a specific site it will use the default author biographical information from the main network site.


Please visit the github repository on https://github.com/codeadapted/multisite-author-bio if you want to contribute, post a specific feature request or bug report.

== Installation ==

1. Upload the plugin folder to your /wp-content/plugins/ folder or upload it through WordPress.
2. Go to the Network **Plugins** page and activate the plugin.

== Frequently Asked Questions ==

= How do I use this plugin? =

Once, installed and activated the plugin is ready to rock and roll. No additional configuration is necessary.

Navigate to a user's edit page and scroll to the bottom. You will see the Multisite Author Bio section near the bottom. The plugin will automatically select the option for the current site if you are not on the main network site.

= How to uninstall the plugin? =

Deactivate and delete the plugin. Please keep in mind that the options added to the database are not removed on uninstall by default in order to preserve data. If you would like to clear all data added by the plugin you can go to the plugin's settings page and check `Clear translation data on uninstall`.

== Screenshots ==
1. Go to the Network **Plugins** page.
2. Upload and install Multisite Author Bio.
3. Network activate the plugin.
4. Main network site author biographical information.
5. Author bio on main network site.
6. Scroll down the user's edit page until you see Multisite Author Bio.
7. Click on the dropdown to select the site name you wish to update/view the author bio for.
8. The textarea will appear with the author bio for the site selected. You can update this by clicking `Update User`.
9. Updated author bio on another network site.
10. If you want to remove the plugin and clear the database of all data associated with it go to `Settings > Mutlisite Auther Bio`.
11. Check `Clear translation data on uninstall` and click `Save Changes`.

== Changelog ==
= 1.0 =
* Plugin released.
