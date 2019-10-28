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

const FILE_INDEX='/var/www/html/filelist_s3.txt';

class S3Info {
    public $datetime;
    public $bytes;
    public $filename; 
    
    function __construct($line){
	$this->datetime = substr($line, 0, 19);
	$left = trim(substr($line, 20));
	$rest = explode( " ", $left, 2 );
	if ( count($rest) == 2) {
	    $this->bytes    = $rest[0];
	    $this->filename = $rest[1];
	} else {
	    $this->bytes    = 'n/a';
	    $this->filename = 'n/a';
	}
    }

    function match($term) {
	return stripos( $this->filename, $term );
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
     */
    public static function onSpecialSearchResultsAppend( $that, $out, $term ) {
        $res = array();
        $fp = fopen(FILE_INDEX,"r");
        while ( !feof($fp)){
            $v    = new S3Info( fgets($fp) );
	    if ($v->match($term)) {
		array_push($res, $v->tr() );
		if ( count($res) > 100 ){
		    array_push($res, "<tr><td colspan='3'>Only the first 100 hits are shown</td></tr>");
		    break;
                }
            }
        }
        if ( count($res) == 0){
            if ($term){
                $out->addHTML("<p><b>No files matching " . $term . "</b></p>");
            }
        } else {
	    $out->addHTML("<h3>S3 Search Results:</h3>");
            $out->addHTML("<table>");
            $out->addHTML("<tr><th>Date</th><th>Size</th><th>File Name</th></tr>");
            foreach ($res as $row){
                $out->addHTML($row);
            }
            $out->addHTML("</table>");
        }
    }
}

