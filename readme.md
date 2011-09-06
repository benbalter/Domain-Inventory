# Domain Inventory

*Tracks Federal .Govs by Agency, Status, Non-WWW Support, IPv6 Support, CDN, CMS, Cloud Provider, Analytics, JavaScript Libraries, and HTTPs support.*

* Uses [WordPress](http://wordpress.org) to organize data.
* Uses [Site-Inspector](https://github.com/benbalter/Site-Inspector) to gather data. 
* Recommend displaying with [Faceted Search Widget](http://wordpress.org/extend/plugins/faceted-search-widget/) and [Count Shortcode](http://wordpress.org/extend/plugins/count-shortcode/)
* Example: http://dotgov.benbalter.com

## Included Files

* **domain-inventory.php** - Main file in the form of a WordPress plugin to curate the domains
* **data_dump.xml** - WordPress compatible XML dump of domains and data
* **domain_list.csv** - List of Federal Executive .govs in CSV format
* **import.php** - script to import CSV into WordPress (run directly)
* **count-post.php** - generates initial post to be used with count shortcode, helps with UI (run directly)
* **count-severs.php** - example of how to count servers by OS and version (run directly)
* **dups.php** - generates list of duplicate domains based on MD5 checksum of content (run directly)

## Notes

* The CMS and Script detection, although generally accurate, in not foolproof. It works by sniffing references to known elements of CMSs (such as WordPress's use of the wp-content directory), and may generated some false positives as a result.
* To update the site-inspector submodule:

```
cd domain-inspector
git submodule init
git submodule update
```