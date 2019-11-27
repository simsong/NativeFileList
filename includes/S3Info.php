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
    public $directory;
    public $filename; 

    function __construct($datetime, $bytes, $directory, $filename){
        $this->datetime = $datetime;
        $this->bytes = $bytes;
        $this->directory = $directory;
        $this->filename = $filename;
    }

    function tr() {
        echo $bytes;
        $talk_url = "index.php?title=Talk:" . $this->filename . "&action=edit&redlink=1";
        return "<tr><td class='nfl-date'>". date("d-m-Y", $this->datetime)."</td>".
        "<td class='nfl-bytes' style='text-align:right'>".$this->bytes."</td>".
        "<td class='nfl-directory' style='text-align:left'>".$this->directory."</td>".
        "<td class='nfl-filename'>".$this->filename."</td>".
        "<td><a class='new' href='" . $talk_url . "'>[Talk]</a></td>".
        "</tr>";
    }
}

?>