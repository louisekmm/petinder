<?php
include_once '../../config.inc.php';
/* JPEGCam Test Script */
/* Receives JPEG webcam submission and saves to local file. */
/* Make sure your directory has permission to write files as your web server user! */



$filename = date('YmdHis') . '.jpg';
$result = file_put_contents( '../../content/camera/'.$filename, file_get_contents('php://input') );
if (!$result) {
	print "ERROR: Failed to write data to $filename, check permissions\n";
	exit();
}

$url = URL . 'content/camera/' . $filename;
print "$url\n";

