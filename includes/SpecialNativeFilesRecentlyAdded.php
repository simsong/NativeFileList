<?php

namespace MediaWiki\Extension\NativeFileList;

use MediaWiki\MediaWikiServices;
use Mediawiki\Logger\LoggerFactory;
use MediaWiki\Extension\NativeFileList\S3Info as S3Info;

class SpecialNativeFilesRecentlyAdded extends \SpecialPage {
    
	const PAGENAME = 'NativeFilesRecentlyAdded';
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
		$out = $this->getOutput();
		$this->setHeaders();

		#Setup Logger
        $logger = LoggerFactory::getInstance( 'NativeFileList' );

		# Set globals
        global $wgDBprefix, $nflDBprefix;
		$prefix = $wgDBprefix . $nflDBprefix;

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

		echo $scanQuery;

		$scanQ = $dbr->query($scanQuery);

		// $scanQ = $dbr->select(
		// 	$prefix . 'scans' ,
		// 	array('scanid'),
		// 	array( ),
		// 	__METHOD__,
		// 	array(
		// 		'LIMIT' => '2',
		// 		'ORDER BY' => 'scanid DESC'
		// 	)
		// );

		foreach ($scanQ as $row) {
			array_push( $scans, $row->scanid );
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

