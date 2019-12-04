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
use Mediawiki\Logger\LoggerFactory;
use MediaWiki\MediaWikiServices;
use MediaWiki\Extension\NativeFileList\S3Info as S3Info;

const FILE_INDEX='/var/www/html/filelist_s3.txt';

// CONSTANTS
define('SEARCH_LIMIT', '100');

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
        // echo 'Starting Special Search\n';

        global $wgDBprefix, $nflDBprefix;

        $logger = LoggerFactory::getInstance( 'NativeFileList' );

        //GET CONFIG -> DBPREFIX
        // $config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'NativeFileList' );
        // $nflDBprefix = $config->get( 'DBprefix' );

        $res = array();
        $dbr = wfGetDB( DB_REPLICA );
        echo $nflDBprefix;
        $q = $dbr->select(
                array( $wgDBprefix . $nflDBprefix . 'files',
                $wgDBprefix . $nflDBprefix . 'filenames',
                $wgDBprefix . $nflDBprefix . 'dirnames', 
                $wgDBprefix . $nflDBprefix . 'roots as r'),
                array('fileid','r.rootid','dirnameid','mtime','size','r.rootdir','dirname','filename'), 
                array('filename ' . $dbr->buildLike( $dbr->anyString() , $term , $dbr->anyString())),
                __METHOD__,
                array('LIMIT' => constant("SEARCH_LIMIT")),
                array(
                    'fileid' => array( 'NATURAL JOIN' ),
                    'r.rootid' => array ( 'NATURAL JOIN' ),
                    'dirnameid' => array( 'NATURAL JOIN' ),
                ));


        $prefix = $wgDBprefix . $nflDBprefix;

        // $query = "SELECT fileid, r.rootid, dirnameid, mtime, size, rootdir, dirname, filename ";
        // $query .= "FROM " . $dbr->tableName($prefix . "files") . " NATURAL JOIN ";
        // $query .= $dbr->tableName($prefix . "filenames") . " NATURAL JOIN ";
        // $query .= $dbr->tableName($prefix . "dirnames") . " NATURAL JOIN ";
        // $query .= $dbr->tableName($prefix . "roots");
        // $query .= "FROM " . $dbr->tableName($prefix . "files") . "," . $dbr->tableName($prefix . "filenames") . "," . $dbr->tableName($prefix . "dirnames") . "," . $dbr->tableName($prefix . "roots") . " as r";
        // $query .= " WHERE (filename LIKE '% " . $term . "%' ESCAPE '`') LIMIT " . constant("SEARCH_LIMIT");
        


        // $q = $dbr->query($query);

        foreach ($q as $row) {
            $talkExists = false;
            $v = new S3Info( $row->mtime, $row->size, $row->rootdir, $row->dirname, $row->filename );
            // echo $row->mtime . " " .$row->size . " " .$row->rootdir . " " .$row->dirname . " " .$row->filename;
            $q->current();
            array_push( $res, $v );
        }
        if (count($res)==(int)constant("SEARCH_LIMIT")) {
            array_push($res, "<tr><td colspan='3'>Only the first " . constant("SEARCH_LIMIT") . " hits are shown</td></tr>");
        }

            if ( count($res) == 0) {
                if ($term){
                    $out->addHTML("<p><b>No files matching " . $term . "</b></p>");
                }
            } 

        if ( count($res) >= 0 ) {
            $table = "";
            $out->addHTML("<h3>S3 Search Results:</h3>");
            $table .= "{| class='wikitable'\n";
            // $out->addHTML("<table>");
            // $out->addHTML("<tr><th>Date</th><th>Size</th><th>Root</th><th>Directory</th><th>File Name</th></tr>");
            $table .= "! Date: \n";
            $table .= "! Size: \n";
            $table .= "! Root: \n";
            $table .= "! Directory: \n";
            $table .= "! File Name: \n";
            $table .= "! Discussion\n";
            foreach ($res as $row) {
                // $out->addHTML($row);
                $table .= "|-\n";
                $table .= "| " . date("d-m-Y", $row->datetime) . "\n";
                $table .= "| " . $row->bytes . "\n";
                $table .= "| " . $row->root . "\n";
                $table .= "| " . $row->directory . "\n";
                $table .= "| " . $row->filename . "\n";
                $table .= "| [[Talk:" . $row->root . ":" . $row->directory . "/" . $row->filename . "]]\n";
            }
            // $out->addHTML("</table>");
            $table .= "|}";
            $out->addWikiText($table);

        }
    }


    /**
     * @see https://stackoverflow.com/questions/58680500/proper-way-to-create-new-sql-table-in-a-mediawiki-extension/58683843#58683843
     * @called from https://www.mediawiki.org/wiki/Manual:Hooks/LoadExtensionSchemaUpdates
     * @param $updater - Database Updater
     * 
     * Fired when MediaWiki is updated to allow extensions to update the database
     * 
     * This hook is activated when you run:
     * $ php maintenance/update.php
     */

    public static function onLoadExtensionSchemaUpdates( $updater ) {

        global $wgDBprefix, $nflDBprefix;

        //GET CONFIG -> DBPREFIX
        $config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'NativeFileList' );
        $nflDBprefix = $config->get( 'DBprefix' );
        
        echo $wgDBprefix . $nflDBprefix . "\n";

        // runs entire script
        $updater->addExtensionTable(
            $nflDBprefix,
            dirname ( __DIR__ ) . '/sql/FILE_SCHEMA.sql'
        );
    }
}