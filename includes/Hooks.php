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

const FILE_INDEX='/Users/simsong/Sites/filelist.txt';

class Hooks {
    /**
     * @see https://www.mediawiki.org/wiki/Manual:Hooks/SearchAfterNoDirectMatch
     * @called from https://gerrit.wikimedia.org/g/mediawiki/core/+/master/includes/search/SearchNearMatcher.php
     * @param $searchterm
     * @param $title - array of titles
     * Returns true false if something found, true otherwise.
     */
    public static function onSpecialSearchResultsAppend( $that, $out, $term ) {
        wfDebug('hello mom');
        $res = array();
        $fp = fopen(FILE_INDEX,"r");
        $count = 0;
        while ( !feof($fp)){
            $line = fgets($fp);
            $v = explode("," , $line, 3);   // [0]==time_t, [1]==bytes, [2]==name
            if ( count($v) == 3 ){
                $name = $v[2];
                if (stripos( $name, $term) ){
                    $nt = str_replace("T"," ",date("c",$v[0]));
                    $nt = str_replace("+00:00","",$nt);
                    array_push($res,"<tr><td>".$nt."</td><td>".$v[1]."</td><td>".$v[2]."</td></tr>");
                    $count = $count + 1;
                    if ($count > 100){
                        break;
                    }
                }
            }
        }
        if ( count($res) == 0){
            if ($term){
                $out->addHTML("<p><b>No files matching " . $term . "</b></p>");
            }
        } else {
            wfDebug("found ".count($res));
            $out->addHTML("<table>");
            $out->addHTML("<tr><th>Date</th><th>Size</th><th>File Name</th></tr>");
            foreach ($res as $row){
                $out->addHTML($row);
            }
            $out->addHTML("</table>");
        }
    }
}

