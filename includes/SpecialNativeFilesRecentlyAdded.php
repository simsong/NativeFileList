<?php

namespace MediaWiki\Extension\NativeFileList;

use MediaWiki\MediaWikiServices;
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
        parent::execute( $par );
		$request = $this->getRequest();
		$output = $this->getOutput();
		$this->setHeaders();

		# Set globals
        global $wgDBprefix, $nflDBprefix;

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

		$scanQ = $dbr->select(
			$wgDBprefix . $nflDBprefix . 'scans' ,
			array('scanid'),
			array( ),
			__METHOD__,
			array(
				'LIMIT' => '2',
				'ORDER BY' => 'scanid DESC'
			)
		);

		$output->addWikiText( $scanQ->scanid );

        $q = $dbr->select(
			array( $wgDBprefix . $nflDBprefix . 'files',
			$wgDBprefix . $nflDBprefix . 'paths as p',
			$wgDBprefix . $nflDBprefix . 'filenames as fn',
			$wgDBprefix . $nflDBprefix . 'dirnames as d'), 
			array( 'fileid', 'p.pathid', 'size', 'd.dirnameid','dirname', 'fn.filenameid', 'filename','mtime' ), 
			array( 
				'scanid=15'#,
				// 'pathid NOT IN (SELECT pathid FROM ' $wgDBprefix . $nflDBprefix . 'files WHERE scanid={scan1})',
				), # WHERE
			__METHOD__,
			array( 'LIMIT' => self::SEARCH_LIMIT ),
			array(
				'pathid' => array( 'NATURAL JOIN' ),
				'dirnameid' => array( 'NATURAL JOIN' ),
				'filenameid' => array( 'NATURAL JOIN' ),
			));

		foreach ($q as $row) {
			$v    = new S3Info( $row->mtime, $row->bytes, $row->dirname, $row->filename );
			array_push($res, $v->tr() );
		}
        if (count($res)==(int)constant("SEARCH_LIMIT")){
            array_push($res, "<tr><td colspan='3'>Only the first " . constant("SEARCH_LIMIT") . " hits are shown</td></tr>");
        }

		if ( count($res) == 0){
			if ($term){
				$out->prependHTML("<p><b>No files matching " . $term . "</b></p>");
			}
		} 

        if ( count($res) > 0 ){
            $out->prependHTML("<h3>S3 Search Results:</h3>");
			$out->prependHTML("<table>");
			$out->prependHTML("<tr><th>Date</th><th>Size</th><th>Directory</th><th>File Name</th></tr>");
			foreach ($res as $row){
				$out->prependHTML($row);
			}
			$out->prependHTML("</table>");
		}


		$output->addWikiText( '[[{{ns:11}}:Foo|Text]]');
	}
}

