<?php

// Step 1: Include composer autoload file
require __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

if(isset($_GET['cid'])) {
	$comment_section = new CommentSection(1, 0, 0, $_GET['cid']); 
	$comment_section->doComments();
}

	
?>
