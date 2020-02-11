<?php
/**
 * S3Info helper class for the NativeFileList extension.
 *
 * @author DJ Streat
 */


namespace MediaWiki\Extension\NativeFileList;

class S3Info {
    public $datetime;
    public $bytes;
    public $root;
    public $directory;
    public $filename; 

    function __construct($datetime, $bytes, $root, $directory, $filename){
        $this->datetime = $datetime;
        $this->bytes = $bytes;
        $this->root = $root;
        $this->directory = $directory;
        $this->filename = $filename;
    }

    function tr( $talkExists) {
        echo $bytes;
        $talk_url = "";
        $talk_new = "";
        if ( $talkExists ) {
            $talk_url = "index.php?title=Talk:" . $this->root . ":" . $this->directory . "/" . $this->filename;
        } else {
            $talk_url = "index.php?title=Talk:" . $this->root . ":" . $this->directory . "/" . $this->filename  . "&action=edit";
            $talk_new = "class'new'";
        }
        return "<tr><td class='nfl-date'>". date("d-m-Y", $this->datetime)."</td>".
        "<td class='nfl-bytes' style='text-align:right'>".$this->bytes."</td>".
        "<td class='nfl-bytes' style='text-align:left'>".$this->root."</td>".
        "<td class='nfl-directory' style='text-align:left'>".$this->directory."</td>".
        "<td class='nfl-filename'>".$this->filename."</td>".
        "<td><a " . $talk_new . " href='" . $talk_url . "'>[Talk]</a></td>".
        "</tr>";
    }
}

?>