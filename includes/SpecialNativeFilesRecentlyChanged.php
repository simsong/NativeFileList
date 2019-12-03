<?php

namespace MediaWiki\Extension\NativeFileList;

class SpecialNativeFilesRecentlyChanged extends \SpecialPage {

	const PAGENAME = 'NativeFilesRecentlyChanged';
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
		$out = $this->getOutput();
		$this->setHeaders();

		# Set globals
        global $wgDBprefix, $nflDBprefix;

		# Get request data from, e.g.
		$param = $request->getText( 'param' );

		# Query DB

		/**
		 * SQL QUERY:
		 * SELECT a.pathid as pathid, a.hashid, b.hashid FROM 
		 * (SELECT pathid, hashid, scanid FROM `{db}`.{prefix}files WHERE scanid={scan0}) as a 
		 * JOIN (SELECT pathid, hashid, scanid FROM `{db}`.{prefix}files WHERE scanid={scan1}) as b 
		 * ON a.pathid=b.pathid WHERE a.hashid != b.hashid".format(db=self.database,
		 */
		$dbr = wfGetDB( DB_REPLICA );

		$scans = array();
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

		foreach ($scanQ as $row) {
			array_push( $scans, $row->scanid );
		}

		$changedPathIds = array();

		$prefix = $wgDBprefix . $nflDBprefix;
		$query = "SELECT a.pathid as pathid, a.hashid, b.hashid ";
		$query .= "FROM (SELECT pathid, hashid, scanid FROM " . $dbr->tableName( $prefix . "files" ) . "WHERE scanid=" . $scans[0] . ") as a ";
		$query .= "JOIN (SELECT pathid, hashid, scanid FROM " . $dbr->tableName( $prefix . "files" ) . "WHERE scanid=". $scans[1] . ") as b ";
		$query .= "ON a.pathid=b.pathid WHERE a.hashid != b.hashid LIMIT " . self::SEARCH_LIMIT;

		$q = $dbr->query( $query );
		foreach ($q as $row) {
			array_push( $changedPathIds, $row->pathid );
		}

		$res = array();
		foreach($changedPathIds as $pathid) {
			$query = "SELECT fileid, pathid, rootid, rootdir, size,  dirnameid, dirname, filenameid, filename, mtime ";
			$query .= "FROM " . $dbr->tableName($prefix . "files") . " NATURAL JOIN " . $dbr->tableName($prefix . "paths") . " NATURAL JOIN " . $dbr->tableName($prefix . "roots");
			$query .= " NATURAL JOIN " . $dbr->tableName($prefix . "dirnames") . " NATURAL JOIN " . $dbr->tableName($prefix . "filenames") . " ";
			$query .= "WHERE pathid=" . $pathid;
			
			$q = $dbr->query($query);
			foreach($q as $row) {
				$v    = new S3Info( $row->mtime, $row->size, $row->rootdir, $row->dirname, $row->filename );
				array_push( $res, $v );
			}
		}




        if ( count($res) == self::SEARCH_LIMIT ) {
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