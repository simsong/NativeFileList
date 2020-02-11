<?php
/**
 * Utility functions for the NativeFileList extension.
 *
 * @author DJ Streat
 */


namespace MediaWiki\Extension\NativeFileList;

class NativeFileListUtils {

	static $NflDB = null;

	public static function getDB() {
		if ( self::$NflDB != null && self::$NflDB->isOpen() ) {
			return self::$NflDB;
		}

		//  Declare mediawiki globals
		global $wgDBUser, $wgDBpassword, $wgDBprefix, $wgDBServer;

		//  Declare extension globals
		global $nflDBprefix;

		$dbr = wfGetDB( DB_REPLICA );
		$server = $dbr->getServer();
		$name = $dbr->getDBname();
		$type = $dbr->getType();

		$dbTablePrefix = $wgDBprefix . $nflDBprefix;

		$params = array(
			'host' => $dbServer,
			'user' => $dbUser,
			'password' => $dbPassword,
			'dbname' => $dbName,
			'tablePrefix' => $dbTablePrefix,
		);

		return self::$NflDB;
		
	}


}
?>