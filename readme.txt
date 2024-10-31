=== Plugin Name ===
Contributors: BenjaminSommer
Donate link: http://ssr.benjaminsommer.com/
Tags: search, find, shortcodes, post content, undo, safe, replace
Requires at least: 2.8
Tested up to: 3.4.2
Stable tag: 2.0.1
License: CC GNU GPL 2.0 license

Safely search and replace with advanced options and undo operations.

== Description ==

**Shortcodes**
Quite a lot of plugins use shortcodes - deactivating them leaves your shortcodes 
unused and plain to your visitors. With this feature, you can remove existing 
shortcodes, rename them (in case of an update or plugin switch), and even 
remove, rename and add their attributes.


**Search and replace made easy**
Easily search in your post titles, contents, excerpts and comments for 
words and replace them with something else. A visual preview is almost instant, 
using modern AJAX technologies.


**Undo recent tasks**
All operations, or tasks which you can do with this plugin can be undone 
safely anytime later.

**More Tasks To Come**


== Installation ==

= Software Requirements =
1. PHP 5.2
1. Mysqli 5.5 (plugin uses prepared statements to substantially speed up execution)

= Install =
1. Download and install from within WordPress
1. Choose: Activate or Network Activate
1. Configure AcademicPress

= Notice =
Use of this plugin assumes acceptance of this plugin's license and its Terms of Use, to be found in license.txt.

== Frequently Asked Questions ==

= Not all tasks can be undone. Why? =

The reason is quite simple: to ensure database integrity. For example, when changing post contents in two separate tasks, you firstly have to undo the most recent tasks, then the tasks before that and so on. Simply in temporal reverse order.

== Screenshots ==

1. **Overview**: Main Workspace, with information about recent changes, updates, tips and tricks.
2. **Undo Recent Tasks**: A list of recent tasks which you can undo, anytime.
3. **Change Shortcodes**: Remove, rename or edit shortcodes and their attributes/values.
4. **Screen When Changing Shortcodes**: To be on the safe side, tasks are generate and stored on disk. Only of this step, they are actually executed. Then, visit the subpage "Undo", or proceed to other tasks.


== Changelog ==

= 2.0.1 =
* Improved tab navigation: jQuery code is more robust and can be used for multiple tab areas/wrappers.
* Improved graphical user interface: settings for SimpleSearch and Shortcodes have been arranged more clearly.
* Added icons for task pages

= 2.0 =
* Rebuild plugin from ground up
* Support for undo operations
* Supports shortcodes and simple search and replace operations

= 1.2 =
* Fixed Ssr_DataDescriptor: Method createDatablock() created empty datablock files in case the data has to be stored in memory instead.
* Fixed Ssr_Filter: Method filter() triggered an error in case the filtering result was an empty string.
* Updated and fixed Ssr_DatablockListViewer: Display has been changed to be more intuitive and all special characters including html tags are displayed now.
* Fixed Ssr_Filter and Ssr_TableFilter: In case of multiple filters for one table column, the output of the previous filter was not used as input for the next filter (has been fixed now; so the order matters!); UI has been updated to change filter order.
* Enhanced Ssr_Filter: Prepend a search pattern with `LITERAL ` to explicitly search and replace without regular expressions (e.g. when replacing `\\` with `\`).
* Fixed Ssr_TableFilter: Table columns with regular expression selectors haven't been retrieved from the database properly.

= 1.1.5 =
* Fixed regexp recognition: Ssr_Selector and Ssr_Filter now work for all possible regular expressions; recognition is done via preg_match directly.

= 1.1.4 =
* Added Usage Hints: The choice for a particular database table is now easier with the help of usage hints, i.e. what the table is about.
* Improved Table Selection: Only non-empty database tables (with at least one row) are displayed and thus can be chosen to speed up selection process.
* Added Configuration Options: TABLENAMES_ALLOWED and TABLENAMES_BANNED to restrict selection of database tables (for security).
* Updated German Translation: Table usage hints are already translated into German.

= 1.1.3 =
* Fixed case of some include commands: On some UNIX/LINUX systems, the include command is case sensitive; and some inclusions didn't work properly, which has been fixed with this release.
* Added instant view of calculated Simulation: after simulation has been calculated by the server, the user is automatically redirected to the results view (without clicking a further link)
* Changed default datablock viewer: In `Simulation View`, results are displayed using the ListViewer by default now.

= 1.1.2 =
* Added support for full database connection parameters: host, user, password, port, dbname, socket.
* Added custom configuration file: config.php can be used to override predefined settings.

= 1.1.1 = 
* Added Support for UI Language German
* Improved Result Feedback: Results can be listed as complex sql-like tables or as simple lists.
* Added Class to SSR API: DatablockListViewer
* Renamed Class DatablockViewer to DatablockTableViewer in SSR API

= 1.1 =
* Added Message Boxes: The user is notified in case no tasks or reports are found, instead of showing an empty table with headers and footers.
* Added Functionality to UI Listings: Reports and previous tasks can be removed. There is a detailed view of tasks now available.
* Fixed Readme: For some reasons, WordPress haven't parsed the readme correctly.

= 1.0 =
* Initial release


== Upgrade Notice ==

= 1.1.2 =
Update should fix some connection problems when using ports, mysqli and predefined parameters used by $wpdb.

= 1.0 =
Use of this plugin enables the possibility to safely undo previous operations (search & replace), even if they have been performed a longer time ago.