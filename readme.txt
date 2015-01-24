=== Coaching Staffs ===
Contributors: MarkODonnell
Donate link: http://shoalsummitsolutions.com
Tags: sports,games,roster,sports teams,team roster,sports roster,sports team roster  
Requires at least: 3.6
Tested up to: 4.0
Stable tag: 0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Manages multiple sports coaching staffs. Displays tabular rosters, a single coach bios, and coaches galleries.

== Description ==

The MSTW Coaching Staffs plugin manages coaching staff rosters for sports teams. The plugin supports multiple coaches, staffs, and teams. It provides several views of staffs including coaches table (screenshot-1), a coaches gallery (screenshot-2), and single coach profiles (screenshot-3). Samples of all of the above displays are available on the [Shoal Summit Solutions Plugin Development Site](http://shoalsummitsolutions.com/dev).

This plugin is designed to handle some challenges unique to high school coaching staffs, where coaches often coach two (or more) teams in different roles. For example, one coach can be the Head Coach of the Junior Varsity team and the Offensive Line coach for the Varsity team. That coach can be shown as the first coach on the JV staff and somewhere lower on the Varsity staff listings. Here's how to do it:

1. Begin by adding the Coaches using the Coaching Staffs -> All Coaches -> Add New Coach admin page. (screenshot-4 and screenshot-5)
2. Next add the Coaching Staffs, using the Coaching Staffs -> Staffs admin page. (screenshot-6)
3. Finally add the Staff Positions, using the Coach Staffs -> All Staff Positions -> Add New Staff Position admin page. (screenshot-7 and screenshot-8)

*It is important that you enter/add data in this order because the coach and staff must be entered before they can be associated with a staff position.*

To display a staff table via the short code enter 
`[mstw-cs-table staff=staff-slug]`
on the TEXT or HTML tab, NOT the VISUAL tab, of a page, post, or text widget. You MUST provide a `staff` parameter or nothing will be displayed. Many other parameters are available, which you can read about on the [Shoal Summit Solutions](http://shoalsummitsolutions.com/category/users-manuals/cs-plugin/) site. Looking the samples on [my plugin development site](http://shoalsummitsolutions.com/dev/coaching-staffs/) is highly recommended.

To learn how to install and use the single coach profile page and the coaching staff gallery page, please read the instructions on the Installation tab and on the [Shoal Summit Solutions](http://shoalsummitsolutions.com/category/users-manuals/cs-plugin/) site.

The plugin is internationalized and ready for translation. I am happy to help translators. A default .pot file is in the /lang directory.

= NOTES =
The Coaching Staffs plugin is part of the My Sports Team Website (MSTW) framework. Others include Schedules & Scoreboards, Team Rosters, League Standings, MSTW CSV Exporter, and Game Locations and Game Schedules (both now deprecated).  All are available on [WordPress.org](http://wordpress.org/extend/plugins/).

= Helpful Links =
* [**See what the plugin in action on the MSTW Dev Site -»**](http://dev.shoalsummitsolutions.com/)
* [**Read the (site admin) user's manual at shoalsummitsolutions.com -»**](http://shoalsummitsolutions.com/category/cs-plugin)

== Installation ==

All the normal installation methods for WordPress plugins work. See [the installation manual page](http://shoalsummitsolutions.com/cs-installation/) for details.
*Upon installation make sure the WP default timezone is set correctly in the Wordpress Settings->General screen.*

= If you plan to use the coach profile pages (linked from the coaching staff table and/or the coaching gallery page), then you must copy the following page template from the coaching-staffs/theme-templates directory to your theme's main directory: =

1.	single-coach.php

= If you plan to use the Coaches Gallery page, then you must copy the following page template from the coaching-staffs/theme-templates directory to your theme's main directory: =

1.	taxomony-staffs.php

== Frequently Asked Questions ==

[The FAQs may be found here.](http://shoalsummitsolutions.com/cs-faq/)

== Screenshots ==

1. Sample of a Coaching Staff Table [shortcode] display
2. Sample a Coaches Gallery page
3. Sample of a Single Coach Profile page
4. All Coaches admin page
5. Add/Edit Coach admin page
6. Staffs admin page
7. All Staff Positions admin page
8. Add/Edit Staff Position admin page
9. Display Settings admin page

== Upgrade Notice ==

This version fixes a couple of significant bugs. No functionality has been changed.
 

== Changelog ==

= 0.3 =
* Corrected a major bug that prevented some of the display settings from working correctly.
* Fixed several PHP notices in mstw_coaching_staffs_admin.php, single_coach.php, and taxonomy_staffs.php. None caused reported problems but in the right circumstances they could have. I'm sure there is more work to be done to remove them all. Please let me know if you come across any.
* Changed the display settings validation callback so empty strings remained empty strings and were not converted to zeros.
* Updated mstw_admin_utils.php include file so that settings field instructions were displayed more cleanly in "table format".
*Updated the default WordPress internationalization/translation file - /lang/mstw-coaching-staffs-en_US.pot.

= 0.2 =
* Added show/hide controls for all data fields to Display Settings.
* Added numerous other Display Settings, primarily settings for the gallery, including:
Show/Hide Title, Title Color, Corner Style, Photo Size (width x height), Border Color, Border Width
* Also added settings for the Single Coach's Profile Border and Width
* Fancied up the styles on the gallery & profile photos in the default stylesheet
* Added link to Coach's Profiles from coach's photos (as well as their names) 

= 0.1 =
* Initial release.

