Domain Inventory
===============

Tracks Federal .Govs by Agency, Status, Non-WWW Support, IPv6 Support, CDN, CMS, Cloud Provider, Analytics, JavaScript Libraries, and HTTPs support.

* Uses WordPress to organize data.
* Uses Site-Inspector to gather data.
* Recommend displaying with Faceted Search Widget and Count Shortcode
* Example: http://dotgov.benbalter.com

Notes
=====

* The CMS and Script detection, although generally accurate, in not foolproof. It works by sniffing references to known elements of CMSs (such as WordPress's use of the wp-content directory), and may generated some false positives as a result.