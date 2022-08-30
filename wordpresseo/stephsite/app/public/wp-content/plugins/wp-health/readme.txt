=== Manage Backup & Monitor - WP Umbrella WordPress Monitoring & Automatic Backups ===
Contributors: gmulti, truchot
Tags: backup, monitor, automatic backups, monitoring, WordPress backup
Requires at least: 5.1+
Tested up to: 6.0
Requires PHP: 7.2
Stable tag: v2.2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Manage WordPress Sites effortlessly: automatic backup, uptime monitoring, safe update and much more.

== Description ==

Managing multiple WordPress sites has never been so easy: automatic backup, uptime monitoring, safe update for plugins and themes, and much more.

WP Umbrella is the best alternative to ManageWP.

= Manage =

**Single dashboard with 1-click access**
Manage all your sites from our single dashboard and safely bulk update plugins and theme.

= Monitor =
**The most comprehensive monitoring for WordPress**
Monitor uptime, downtime, performance, Google PageSpeed, PHP errors and WordPress error logs.

= Backup =
**Automatic backups and manual backup**
Use WP Umbrella to make daily and automatic backup (incremental backup) or manual backup.
Our backup is reliable and will make your life easier if you migrate or restore your website.

= Premium / Freemium =

Create an account to get your API key and enjoy 14 day trial with all features (backup, uptime monitoring, etc). Then you only have access to our health check and safe update technology.

== Installation ==

= Minimum Requirements for WP Umbrella =
* WordPress 4.9 or greater
* PHP version 7.2 or greater

== Frequently Asked Questions ==

= How do I create my first backup? =

Go to WP Umbrella's application, click on your website, then on the backup tab. And click on "Backup  now". It always take some times to make the first backup.

= Where are backups stored? =

We store backups on European servers to be compliant with GDPR regulation.

= How do I restore a backup? =

In WP Umbrella's application, select the backup you want to restore and click on "Get your backup".

= How to run automatic backups? =

In WP Umbrella's application, click on the backup tab, then on settings. From here you can enable automatic backup (daily, weekly, monthly, etc) and define your automatic backups settings.

= What do you monitor? =

We monitor uptime, downtime, speed, performance, php errors and WordPress error logs. Curious about it? Read our guide about [WordPress monitoring!](https://wp-umbrella.com/blog/monitoring-wordpress-the-ultimate-guide/)

= Where can I learn about multiple sites management best practices? =

We suggest you to read our guide about [multiple WordPress sites management!](https://wp-umbrella.com/blog/manage-multiple-wordpress-sites-one-dashboard/)

= Why is monitoring important?=

Downtime happens more than you think. Monitoring your WordPress is the best way to keep your website in good shape.

= How are you better than ManageWP? =

WP Umbrella is easier to use and faster than managewp.

= What is the difference between WP Umbrella and Query Monitor? =

With Query Monitor, you can identify errors while growing a page. WP Umbrella monitors your website and will alert you automatically when WordPress errors arise.

== Changelog ==

= 2.2.4 (08-08-2022) =
- Improved: Retrieve updates for all pro plugins.
- Improved: Compatibility with hosters for updating plugins with restrictions.
- Impvoed: Error prevention on backups with large files.

= 2.2.3 (08-03-2022) =
- Bugfix: compatibility with Elementor editor.

= 2.2.2 (08-03-2022) =
- New: add filter for backup compatibility with Bedrock
- Improved: Updating plugins with WP Engine hosting

= 2.2.1 (08-01-2022) =
- Bugfix: Undefined constant WP_UMBRELLA_API_KEY

= 2.2.0 (08-01-2022) =
- New: Creation of a centralized controller to improve connectivity
- New: Verification that we do not save a WordPress present in a sub-folder.
- Improved: Check the weight of the lines to save the database
- Improved: Added a filter to adjust the source that saves the files
- Improved: Distinct separation between the file and database backup process.

= 2.1.5 (07-28-2022) =
- Bugfix: Fixed a bug that caused the backup database to not be saved.

= 2.1.4 (07-17-2022) =
- Improved: Acceleration of the backup process
- Improved: Separation of the table process backup according to its size.

= 2.1.3 (07-05-2022) =
- Improved: Separation of the database backup process table by table
- Improved: Prevent error and cleanup directory if backup process fail
- Improved: Update plugin premium

= 2.1.2 (06-24-2022) =
- Improved: SQL query of the improved database backup.
- Improved: Adding the number of posts and attachments at the time of backup
- Bugifx: disk_free_space miss paramater
- Improved: get themes data
- Improved: Reduction of the possible memory for the database backup to avoid an allowed memory size

= 2.1.1 (06-22-2022) =
- Bugfix: Blocking the backup process on non-recoverable files
- Improved: Snapshot of disk space and php memory limits.

= 2.1.0 (06-13-2022) =
- Improved: Change in the backup process
- Improved: Safe update
- New: Add a scan for the backup to check its feasibility
- New: Snapshot theme data

= 2.0.6 (04-06-2022) =
- Bugfix: Get plugins data

= 2.0.5 (04-05-2022) =
- Improved: one-click connection WP

= 2.0.4 (03-31-2022) =
- Improved: SSL communication API.

= 2.0.3 (03-28-2022) =
- Improved: Database backup for a multi-site installation on a single database.
- Improved: Adding the cache on the White Label API request.

= 2.0.2 (03-24-2022) =
- Improved: update of the WordPress Core

= 2.0.1 (03-15-2022) =
- Bugfix: Duplicate class name

Our full changeling can be accessed [Here!](https://wp-umbrella.com/change-log/)