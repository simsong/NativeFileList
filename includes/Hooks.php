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

class Hooks {
	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/SearchAfterNoDirectMatch
         * @called from https://gerrit.wikimedia.org/g/mediawiki/core/+/master/includes/search/SearchNearMatcher.php
	 * @param $searchterm
         * @param $title - array of titles
         * Returns true false if something found, true otherwise.
         */
	public static function onSearchAfterNoDirectMatch( $searchterm, &$title ) {
               wfDebug( __METHOD__ . ': onSearchAfterNoDirectMatch started' );
               $title = Title::newFromText( "created by newFromText\nThis is the next line" );
               return false;
	}

	public static function onSpecialSearchResultsAppend( $that, $out, $term ) {
               wfDebug( __METHOD__ . ': onPrefixSearchBackend started' );

               $out->addHTML(" <div class='lumberjack'>I'm a lumberjack and I'm okay. Your search term was <b>");
               $out->addHTML($term);
               $out->addHTML("</b></div>");
               $out->addHTML("<form><input type='text'></input><input type='submit'></input></form>");
               return false; // stop hook evaluation
	}
}

