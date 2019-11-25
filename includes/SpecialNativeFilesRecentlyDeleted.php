<?php

namespace MediaWiki\Extension\NativeFileList;

class SpecialNativeFilesRecentlyDeleted extends \SpecialPage {

    const PAGENAME = 'NativeFilesRecentlyDeleted';

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

		# Get request data from, e.g.
		$param = $request->getText( 'param' );

		# Do stuff
		# ...
		$wikitext = 'Native Files Recently Deleted!';
        echo $wikitext;
		$output->addWikiText( $wikitext );
	}
}

