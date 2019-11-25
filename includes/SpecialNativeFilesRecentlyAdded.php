<?php

namespace MediaWiki\Extension\NativeFileList;

class SpecialNativeFilesRecentlyAdded extends \SpecialPage {
    
    const PAGENAME = 'NativeFilesRecentlyAdded';

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
		$wikitext = 'Native Files Recently Added!';
		$scriptEmbed = 
		`
				$.get( "fs_search.php" function() {
					console.log( "recieved" );
				})
					.done(function() {
						console.log( "recieved" );
					});
		`;
        echo $wikitext;
		$output->addWikiText( $wikitext );
		$output->addInlineScript( $scriptEmbed );
	}
}

