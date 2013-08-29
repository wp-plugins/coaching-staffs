=== Coaching Staffs ===
Contributors: Mark O'Donnell
Donate link: http://shoalsummitsolutions.com
Tags: sports,games,roster,sports teams,team roster,sports roster,sports team roster  
Requires at least: 3.6
Tested up to: 3.6
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Manages multiple sports coaching staffs. Displays tabular rosters, a single coach bios, and coaches galleries.

== Description ==

Welcome to the MSTW Coaching Staffs Plugin from [Shoal Summit Solutions](http://shoalsummitsolutions.com/).

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

The plugin is internationalized and ready for translation. I am happy to help translators

**NOTES:**
The Coaching Staffs plugin is the fifth in a set of plugins supporting the My Sports Team Website (MSTW) framework. Others include Game Locations, Game Schedules, Team Rosters, and League Standings, which are all now available on [WordPress.org](http://wordpress.org/extend/plugins/). Statistics, Sponsors, Frequently Asked Questions, Users Guide, and more are planned for future development. If you are a developer and there is one you would really like to have, or if you would like to participate in the development of one, please contact me (mark@shoalsummitsolutions.com).

== Installation ==
Basic installation the **AUTOMATED** way:

1. Go to the Plugins->Installed plugins page in Wordpress Admin.
2. Click on Add New.
3. Search for Coaching Staffs.
4. Click Install Now.
5. Activate the plugin.
6. Use the new MSTW Coaching Staffs menu to create and manage your coaches, staffs, and staff positions.
7. Use the Display Settings admin page to configure the plugin, shortcode, and single player page.

Basic installation the **MANUAL** way:

1. Download the plugin from the wordpress site.
2. Copy the entire /coaching-staffs/ directory into your /wp-content/plugins/ directory.
3. Go to the Wordpress Admin Plugins page and activate the plugin.
4. Use the new MSTW Coaching Staffs menu to create and manage your coaches, staffs, and staff positions.
5. Use the Display Settings admin page to configure the plugin, shortcode, and single player page.

= If you plan to use the coach profile pages (linked from the coaching staff table and/or the coaching gallery page), then you must copy the following page template from the coaching-staffs/theme-templates directory to your theme's main directory: =

1.	single-coach.php

= If you plan to use the Coaches Gallery page, then you must copy the following page template from the coaching-staffs/theme-templates directory to your theme's main directory: =

1.	taxomony-staffs.php

== Frequently Asked Questions ==

= Where can I get more help? =
In the [plugin's forum](http://wordpress.org/support/plugin/coaching-staffs) on the Wordpress site, in the [Shoal Summit Solutions documentation](http://shoalsummitsolutions.com/category/users-manuals/cs-plugin/), and on [my plugin development site](http://shoalsummitsolutions.com/dev/coaching-staffs/). (See the next FAQ.)

= Where can I find some examples of the Coaching Staff plugin in action? =
Several examples are provided on [my plugin development site](http://shoalsummitsolutions.com/dev/coaching-staffs/). This is where I test and debug the plugin and the first place I consult when a question is posed on the WordPress forums. So it might be worth a glance.

= How do I change the look (text colors, background colors, etc.) of the coaching staff tables, the coaches gallery, and/or the single coach profile page? =
You can edit the plugin stylesheet (/css/mstw-cs-styles.css) to control all the displays as a group, or each staff's displays individually. You can use the Display Settings admin screen (screenshot-9) to style the table, single coach profile, and coaching staff gallery views. NOTE that the settings on the Display Settings admin screen will override the stylesheet. So if you plan to edit the stylesheet, you may want to leave all the display settings blank. You may also have to edit the single-coach.php and/or the taxonomy-staffs.php page templates to get things to 'fit' within the constraints of your theme. 

= How do I get my coaches images to work? =
A coach's image is loaded as the featured image when editing a coach and then appears on the single coach profile page and the coaches gallery page(s). By default, these images should be 150x150 pixels, or they may be distorted when displayed. You may also need to set the thumbnail size to 150x150 in the Admin Dashboard->Settings->Media admin screen. (This may cause problems with your theme, but it's what is there right now. It will be upgraded in the next release of the plugin.) You can set the size for this image in the Coaches table on the Display Settings page. It should be the same aspect ratio as the uploaded image. If there is no image for a coach, the plugin will look for these two files in the following order:

1. images/default-photo-staff-slug.jpg - which will be the default image for a particular staff, maybe the team logo, that you will have to load into that directory yourself. In this release it should be 150x150 pixels, or at least have the same square aspect ratio, or it will be distorted.

2. images/default-photo.jpg - this is the 'no image found' image that is provided with the plugin.

= The links from the coaches' names to their profile/bio pages appear to be broken. What did I do wrong? =
Maybe nothing. First, please review the installation instructions. *You must copy the single-coach.php template file into your theme's main directory.* 

= I'm positive I installed everything correctly, but the links to the single coach profile pages (coach bios) still don't work. What's wrong? =
You probably need to reset your permalinks. Go to your admin dashboard -> Settings -> Permalinks. Change the permalink setting and save. Then change it back. (FWIW, you want to use the Post-name setting, or you will run into (fixable) problems, but you must change it then change it back to reset the permalinks.)

= Okay, I got the links working but the single coach profile pages and/or the coaches gallery pages are all messed up. What did I do wrong? =
Probably nothing. These templates, and the associated stylesheets and settings, were tested with the WordPress Twenty Eleven theme. Your theme may be overriding some of the styles (often through use of the dreaded css `!important`), defining display areas that are too small (or too big) for various elements of the plugin, or any number of other issues. This can all be fixed via the plugin's stylesheet and/or modifications to the templates, but it must be done on a theme-by-theme basis. I can sometimes help; post questions the the plugin's forum page.

= Can I display more than one coaching staff table on a single page by using multiple shortcodes? =
Yes.

= Can I change the colors of different coaching staff tables on a single page or in different pages? =
Yes. If you inspect a coaches table (say using the Chrome or Firefox/Firebug developer's tools), you will find that some rules include "hooks" for finer grained control. For example,

`<table class="mstw-cs-table mstw-cs-table-team-slug">`

You can use the `.class-team-slug` class to create custom rules for all the table's elements in the plugin's stylesheet.

= Can I display more than one coaching staff gallery on a single page? =
No.

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

*If the plugin is working fine for you, you don't need this upgrade. Primarily it added a collection of display settings and other details.*

* When upgrading the existing coaches data will not be deleted. 
* Any changes to the plugin stylesheet (css/mstw-cs-style.css)*will* be overwritten, so if you have customized that file you will want to save it before upgrading.
* Any changes to the single-coach.php and taxonomy-staffs.php templates *will* be overwritten, so if you have customized either file for your theme, you will want to save it before upgrading.

* ALWAYS BACKUP YOUR DATABASE WHEN UPGRADING PLUGINS ... JUST IN CASE.
 

== Changelog ==

= 0.2 =
* Added show/hide controls for all data fields to Display Settings.
* Added numerous other Display Settings, primarily settings for the gallery, including:
Show/Hide Title, Title Color, Corner Style, Photo Size (width x height), Border Color, Border Width
* Also added settings for the Single Coach's Profile Border and Width
* Fancied up the styles on the gallery & profile photos in the default stylesheet
* Added link to Coach's Profiles from coach's photos (as well as their names) 

= 0.1 =
* Initial release.

