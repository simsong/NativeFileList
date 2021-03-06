<?php

use MediaWiki\MediaWikiServices;
use Mediawiki\Logger\LoggerFactory;
use MediaWiki\Extension\NativeFileList\S3Info as S3Info;

class SpecialNativeFilesRecentlyAdded extends \SpecialPage {
    
	const PAGENAME = 'NativeFilesRecentlyAdded';
	const PAGENICKNAME = 'added';
	const SEARCH_LIMIT = 100;

	function __construct() {
		parent::__construct( self::PAGENAME );
    }
    
    protected function getGroupName() {
        return 'Native File List';
    }

	function execute( $par ) {
		#Things that need to be done at the beginning of an execute function
        parent::execute( $par );
		$request = $this->getRequest();
		$this->setHeaders();
		$out = $this->getOutput();

		#Setup Logger
        $logger = LoggerFactory::getInstance( 'NativeFileList' );

		# Set globals
        global $wgDBprefix, $nflDBprefix;
		$prefix = $nflDBprefix;

		#check prefixes
		wfDebugLog( 'NativeFileList', 
			"PREFIX: " . $prefix,
			'public');

		# Get request data from, e.g.
		$param = $request->getText( 'param' );

		# Query DB

		/**
		 * SQL QUERY:
		 * SELECT fileid, pathid, size,  dirnameid, dirname, filenameid, filename, mtime
		 * FROM `{db}`.{prefix}files NATURAL JOIN {prefix}paths NATURAL JOIN {prefix}dirnames NATURAL JOIN {prefix}filenames 
		 * WHERE scanid={scan0} AND pathid NOT IN (SELECT pathid FROM {prefix}files WHERE scanid={scan1})
		 */
		$dbr = wfGetDB( DB_REPLICA );

		$scans = array();

		$scanQuery = "SELECT scanid FROM  " . $dbr->tableName($prefix . "scans");
		$scanQuery .= " ORDER BY scanid DESC LIMIT 2";

		$scanQ = $dbr->query($scanQuery);

		foreach ($scanQ as $row) {
			array_push( $scans, $row->scanid );
		}

		if (count($scans) < 2) {
			$out->addWikiText("Not enough scans to populate. Please wait until there are more scans.");
			return;
		}

		$res = array();

		$query = "SELECT fileid, pathid, rootid, rootdir, size,  dirnameid, dirname, filenameid, filename, mtime ";
		$query .= "FROM " . $dbr->tableName($prefix . "files") . " NATURAL JOIN " . $dbr->tableName($prefix . "paths") . " NATURAL JOIN " . $dbr->tableName($prefix . "roots");
		$query .= " NATURAL JOIN " . $dbr->tableName($prefix . "dirnames") . " NATURAL JOIN " . $dbr->tableName($prefix . "filenames") . " ";
		$query .= "WHERE scanid=" . $scans[0] . " AND pathid NOT IN (SELECT pathid FROM " . $dbr->tableName($prefix . "files") . " WHERE scanid=" . $scans[1] . ") LIMIT " . self::SEARCH_LIMIT;

		$q = $dbr->query( $query );
		foreach ($q as $row) {
			$v    = new S3Info( $row->mtime, $row->size, $row->rootdir, $row->dirname, $row->filename );
			array_push($res, $v );
		}
        if ( count($res) >= self::SEARCH_LIMIT ) {
			// array_push($res, "<tr><td colspan='3'>Only the first " . self::SEARCH_LIMIT . " hits are shown</td></tr>");
			$out->addWikiText( "Only the first " . self::SEARCH_LIMIT . " hits are shown" );
        }

		if ( count($res) > 0 ) {
            $table = "{| class='wikitable' \n";
            $table .= "! Date: \n";
            $table .= "! Size: \n";
            $table .= "! Root: \n";
            $table .= "! Directory: \n";
            $table .= "! File Name: \n";
            $table .= "! Discussion\n";
            foreach ($res as $row) {
                $table .= "|-\n";
                $table .= "| " . date("d-m-Y", $row->datetime) . " \n";
                $table .= "| " . $row->bytes . " \n";
                $table .= "| " . $row->root . " \n";
                $table .= "| " . $row->directory . " \n";
                $table .= "| " . $row->filename . " \n";
                $table .= "| [[Talk:" . $row->root . ":" . $row->directory . "/" . $row->filename . "]] \n";
            }
			$table .= "|}";
            $out->addWikiText($table);
        }
	}
}

