https://www.mediawiki.org/wiki/Manual:Developing_extensions
https://www.mediawiki.org/wiki/Extension:Example
https://www.mediawiki.org/wiki/Extension:BoilerPlate
https://www.mediawiki.org/wiki/Manual:Hooks

https://www.mediawiki.org/wiki/Extension:CirrusSearch
https://www.mediawiki.org/wiki/Manual:Coding_conventions
https://www.mediawiki.org/wiki/Best_practices_for_extensions

https://www.mediawiki.org/wiki/Manual:How_to_debug
https://www.mediawiki.org/wiki/Manual:$wgDebugLogFile
https://www.siteground.com/kb/how_to_set_my_mediawiki_in_debug_mode/

https://www.mediawiki.org/wiki/Manual:Structured_logging


# For MediaWiki  1.30 and later:
https://www.mediawiki.org/wiki/API:Extensions
** Prefix
** Parameters
** Execution and output
** Caching
** Token handling
** Master database access
** Returning errors
* Documentation
* Extending core modules
* List of extensions with API functionality
* Testing your extension
  Visit api.php
  Visit Special:ApiSandbox
  aVisit pi.php?action=paraminfo&modules=myext 

For loading CSS, JavaScript, and other browser things:

https://www.mediawiki.org/wiki/ResourceLoader

# These are the hooks that are defined
SearchEngine::completionSearchBackend
SearchAfterNoDirectMatch
SearchGetNearMatch
SearchGetNearMatchBefore
SearchGetNearMatchComplete
SearchResultInitFromTitle
SearchIndexFields
SearchDataForIndex
ShowSearchHit
ShowSearchHitTitle
SpecialLogAddLogSearchRelations
SpecialSearchCreateLink
SpecialSearchGoResult
SpecialSearchNogomatch
SpecialSearchPowerBox
SpecialSearchProfileForm
SpecialSearchProfiles
SpecialSearchResults
SpecialSearchResultsPrepend
SpecialSearchResultsAppend
SpecialSearchSetupEngine

https://www.mediawiki.org/wiki/Manual:Hooks/SearchAfterNoDirectMatch
 -- Returns a single page that is close to the requested page

https://www.mediawiki.org/wiki/Manual:Hooks/SearchGetNearMatch
 -- Returns a single page that is close to the requested page

# But from searching myself through wiki/includes/search/*, I only found these hooks
- PrefixSearchBackend
- PrefixSearchExtractNamespace
- PrefixSearchBackend
- SearchIndexFields
- SearchResultsAugment
- SearchableNamespaces
- SearchResultsAugment
- SearchGetNearMatchBefore
- SearchAfterNoDirectMatch
- SearchGetNearMatch
- SearchResultInitFromTitle

PrefixSearchBackend (called from PrefixSearch.php) - returns a set of titles.
called with [ $namespaces, $search, $limit, &$srchres, $offset ]
SearchResultInitFromTitle


https://phabricator.wikimedia.org/source/mediawiki/browse/master/docs/hooks.txt
https://www.mediawiki.org/wiki/Manual:Hooks
http://ucosp.ca/winter-2014/2014/03/extending-mediawiki-using-extensions-and-hooks/

https://www.mediawiki.org/wiki/Extension:MagicNoCache
https://www.mediawiki.org/wiki/Manual:Cache

SearchNearMatcher.php  :: SearchNearMatcher::getNearMatch()

Creating responses - in wiki/includes/Title.php
use Title
newFromText( $title, $defualtNamespace = NS_MAIN); -- Returns the title handle for a term that exists given text.
  $title->isSpecialPage()
  $title->isExternal()
  $title->exists()        
newFromURL( $url );
newFromID( $id, $flags=0);
newFromIDs( $id, $flags=0);

$page = WikiPage::factory ( $title ) - returns page
  $page->hasViewableContent()
  



Better things to hook:

PrefixSearchBackend (called from PrefixSearch.php) - returns a set of titles.
called with [ $namespaces, $search, $limit, &$srchres, $offset ]

- I could make S3 or NativeFiles its own namespace

- SearchNearMatcher getNearMatchInternal is running


Special:Search - generated in includes/specials/SpecialSearch.php:
- uses the string $messageName "searchmenu-new"
- does the full-text search?

references:

use MediaWiki\MediaWikiServices;
use MediaWiki\Widget\Search\BasicSearchResultSetWidget;
use MediaWiki\Widget\Search\FullSearchResultWidget;
use MediaWiki\Widget\Search\InterwikiSearchResultWidget;
use MediaWiki\Widget\Search\InterwikiSearchResultSetWidget;
use MediaWiki\Widget\Search\SimpleSearchResultWidget;
use MediaWiki\Widget\Search\SimpleSearchResultSetWidget;

		$request = $this->getRequest();
                $request->getText( 'search' ) );

		// Close <div class='searchresults'>
		$out->addHTML( "</div>" );

       in showResults( $term ):

		Hooks::run( 'SpecialSearchResultsAppend', [ $this, $out, $term ] );



output is done by:

includes/OutputPage.php   class OutputPage
 addHeadItem(name,value)
 addHeadItems( $values)
 addBodyClasses( $classes )
 addSubtitle( )
 addHTML( $text );
 addElement( $element, array $attribs = [], $contents = '')
 addWikiText( $text, $linestart-true, $interface=true);
 addWikiTextWithTitle()
 addMeta
 addLink
 addScript

 
                          
# Add these to LocalSettings.php for debugging:
error_reporting( -1 );
ini_set( 'display_startup_errors', 1 );
ini_set( 'display_errors', 1 );

$wgShowExceptionDetails=true;
$wgDebugToolbar=true;
$wgShowDebug=true;
$wgDevelopmentWarnings=true;
#$wgDebugDumpSql = true;
$wgDebugLogFile = '/tmp/debug.log';
#$wgDebugComments = true;
$wgEnableParserCache = false;
$wgCachePages = false;
