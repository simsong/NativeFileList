# Native File List mediawiki-extension
An extension that adds search for filesystems and S3 buckets. Designed for compatibility with [Filetime_Tools](https://github.com/simsong/filetime_tools)

# Installation
To install the NativeFileList extension, first go to the extensions folder of your mediawiki installation:
`cd mediawiki/extensions`

Then clone this repository into that folder:
`https://github.com/simsong/NativeFileList.git`

Finally, run the `update.php` script to create the database schema:
`php mediawiki/maintenance/update.php`

You should be all set!

# Configuration
The default configuration for this should be sufficient for any implementation. All of the tables for the configuration begin with your mediawiki database prefix (Ex. `mw2_`) followed by the NativeFileList prefix.