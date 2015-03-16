<?php

class CommentHandler {
	public function insert_comment($sid, $msg, $parent, $author_name) {
		// Connect to database
		try {
			$handler = new Database();
			
			// Insert comment to database
			if ($parent !== 'NULL') {
				$handler->beginTransaction(); // If comment has a parent begin transaction
			}
			
			$res = $handler->prepare('INSERT INTO `comment`(`sid`, `author_name`, `message`, `parent`) VALUES (:sid, :author_name, :message, :parent)');
			$res->execute( array(
				':sid' => $sid,
				':author_name' => $author_name,
				':message' => $msg,
				':parent' => $parent
			));
			
			if ($res->rowCount() !== 1) {
				return false;
			}
			
			// Get cid of last comment
			$cid = $handler->lastInsertId();
			
			if ($parent !== 'NULL') {
				$res = $handler->prepare('UPDATE `comment` SET `children` = 1 WHERE `cid` = :parent');
				$res->execute( array(
					':parent' => $parent
				));
				$handler->commit(); // Commit only if both queries succeed
			}
		}
		catch (PDOException $e) {
			if ($parent !== 'NULL') {
				$handler->rollback();
			}
			return false;
		}
		
		return $cid;
	}
}
