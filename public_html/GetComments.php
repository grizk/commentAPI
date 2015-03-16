<?php

// Step 1: Include composer autoload file
require __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

if(isset($_GET['sid'], $_GET['limit'], $_GET['level'])) {
	$comment_section = new CommentSection($_GET['sid'], $_GET['limit'],$_GET['level']); 
	$comment_section->doComments();
}
else if (isset($_GET['sid'])) {
	$comment_section = new CommentSection($_GET['sid'], 10, 1); 
	$comment_section->doComments();
}

	
?>
