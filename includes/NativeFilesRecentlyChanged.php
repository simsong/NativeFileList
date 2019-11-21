<?php

// namespace MediaWiki\Extension\NativeFileList;

class NativeFilesRecentlyChanged extends SpecialPage {

    const PAGENAME = 'NativeFilesRecentlyChanged';

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
		$wikitext = 'Native Files Recently Changed!';
        echo $wikitext;
		$output->addWikiText( $wikitext );
	}
}

