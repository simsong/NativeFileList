<?php
  /**
   * This program is free software; you can redistribute it and/or modify
   * it under the terms of the GNU General Public License as published by
   * the Free Software Foundation; either version 2 of the License, or
   * (at your option) any later version.
   *
   * This program is distributed in the hope that it will be useful,
   * but WITHOUT ANY WARRANTY; without even the implied warranty of
   * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   * GNU General Public License for more details.
   *
   * You should have received a copy of the GNU General Public License along
   * with this program; if not, write to the Free Software Foundation, Inc.,
   * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
   *
   * @file
   */

namespace MediaWiki\Extension\NativeFileList;

use Title;
use DatabaseUpdater;

const FILE_INDEX='/var/www/html/filelist_s3.txt';

// CONSTANTS
define('SEARCH_LIMIT', '100');

class S3Info {
    public $datetime;
    public $bytes;
    public $filename; 
    
    function __construct($datetime, $bytes, $filename){
	$this->datetime = $datetime;
	$this->bytes = $bytes;
	$this->filename = $filename;
    }

    function tr() {
	$talk_url = "index.php?title=Talk:" . $this->filename . "&action=edit&redlink=1";
	return "<tr><td class='nfl-date'>".$this->datetime."</td>".
	    "<td class='nfl-bytes' style='text-align:right'>".$this->bytes."</td>".
	    "<td class='nfl-filename'>".$this->filename."</td>".
	    "<td><a class='new' href='" . $talk_url . "'>[Talk]</a></td>".
	    "</tr>";
    }
}

class Hooks {

    /**
     * @see https://www.mediawiki.org/wiki/Manual:Hooks/SearchAfterNoDirectMatch
     * @called from https://gerrit.wikimedia.org/g/mediawiki/core/+/master/includes/search/SearchNearMatcher.php
     * @param $searchterm
     * @param $title - array of titles
     * Returns true false if something found, true otherwise.
     * https://hotexamples.com/examples/-/-/wfGetDB/php-wfgetdb-function-examples.html
     */
    public static function onSpecialSearchResultsAppend( $that, $out, $term ) {
        echo 'Starting Special Search\n';
        $res = array();
        $dbr = wfGetDB( DB_REPLICA );
        $q = $dbr->select(
                array('das_s3bucket.files', 'das_s3bucket.filenames'), 
                array('fileid','mtime','size','filename'), 
                array('filename ' . $dbr->buildLike( $dbr->anyString() , $term , $dbr->anyString())),
                __METHOD__,
                array('LIMIT' => constant("SEARCH_LIMIT")),
                array(
                    'fileid' => array( 'NATURAL JOIN' )
                ));
        foreach ($q as $row) {
                $v    = new S3Info( $row->mtime, $row->bytes, $row->filename );
            array_push($res, $v->tr() );
            }
        if (count($res)==(int)constant("SEARCH_LIMIT")){
            array_push($res, "<tr><td colspan='3'>Only the first " . constant("SEARCH_LIMIT") . " hits are shown</td></tr>");
        }

            if ( count($res) == 0){
                if ($term){
                    $out->addHTML("<p><b>No files matching " . $term . "</b></p>");
                }
            } 

        if ( count($res) > 0 ){
            $out->addHTML("<h3>S3 Search Results:</h3>");
                $out->addHTML("<table>");
                $out->addHTML("<tr><th>Date</th><th>Size</th><th>File Name</th></tr>");
                foreach ($res as $row){
                    $out->addHTML($row);
                }
                $out->addHTML("</table>");
            }
    }


    /**
     * @see https://stackoverflow.com/questions/58680500/proper-way-to-create-new-sql-table-in-a-mediawiki-extension/58683843#58683843
     * @called from https://www.mediawiki.org/wiki/Manual:Hooks/LoadExtensionSchemaUpdates
     * @param $updater
     * Fired when MediaWiki is updated to allow extensions to update the database
     * 
     * This hook is activated when you run:
     * $ php maintenance/update.php
     */

    public static function onLoadExtensionSchemaUpdates( $updater ) {
        // print "onLoadExtensionSchemaUpdates\n";
        // add metadata table if not exists
        $updater->addExtensionTable(
            'das',
            dirname ( __DIR__ ) . '/sql/FILE_SCHEMA.sql'
        );
        
        // print "Finished setting up file tables!\n";
    }
}