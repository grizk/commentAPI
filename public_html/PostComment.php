<?php

require __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

if ( isset($_POST['msg'], $_POST['author-name'], $_POST['sid']) ) {

	$sid = (int) $_POST['sid'];
	$msg = trim($_POST['msg']);
	
	if ( empty($_POST['parent']) || !isset($_POST['parent']) ) {
		$parent = null;
	}
	else {
		$parent = (int) $_POST['parent'];
	}
	
	$author_name = trim($_POST['author-name']);
	
	$status_msg = array();
	
	// Author surname must be empty, is supposed to be filled only by bots
	if ( (!empty($msg) || $msg === '0') && !empty($author_name) ) {
		
		// Validate comment length
		if ( Validation::len($msg, 255, 1) !== true ) {
			$status_msg[] = 'Your comment cannot exceed 255 characters';
		}
	
		// Validate parent sid
		if ( Validation::sid($sid) !== true ) {
			$status_msg[] = 'Invalid section ID';
		}
		
		// Validate parent id
		if ( Validation::parent($parent) !== true ) {
			$status_msg[] = 'Invalid parent ID';
		}
		
		
		
		// If all user provided data is valid and trimmed
		if ( $status_msg === array() ) {
		
			$comment_handler = new CommentHandler();
			
			// Insert the comment
			if ( ( $msg_id = $comment_handler->insert_comment($sid, $msg, $parent, $author_name) ) !== false ) {
				$response = array (
					'status_code' => 0,
					'message_id' => $msg_id,
					'author' => $author_name
				);
			}
			else {
				$response = array ( // Database error
					'status_code' => 4,
					'status_msg' => array('An error has been occurred')
				);
			}
			
		}
		else {
			$response = array ( // User provided invalid data
				'status_code' => 3,
				'status_msg' => $status_msg,
				'parent' => $parent
			);
		}
			
	}
	else {
		$response = array ( // One or more fileds are empty 
			'status_code' => 2,
			'status_msg' => array('You must fill all fields')
		);
	}
}
else {
	$response = array ( // One or more fileds are not set 
		'status_code' => 1,
		'status_msg' => array('An error has been occurred'),
		'author' => $author_name
	);
}

header('Content-type: application/json');
echo json_encode($response);