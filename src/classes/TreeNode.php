<?php

Class TreeNode {
	private $cid = null; // Comment ID
	private $sid = null; // Section ID
	private $message = ''; // Message text
	private $author = ''; // Author name
	private $time = ''; // The time when comment has been posted
	private $parent = null; // Comment ID of the parent comment
	private $children = false; // If the node has children 1 otherwise 0 (hasChildren() method returns true/false)
	private $childrenList = null; // Array with children objects
	private $preparedStatement = null; // The prepared statement that gets the child nodes
	private $parentid =null; // Comment id of the current root parent node
	
	public function __construct ( $sid, $rootNode = false, $pid = null, $preparedStatement = null ) {
		
		if($pid != null) {
			$this->parentid = $pid;
		}
		
		$this->sid = $sid;
		
		if ($this->hasChildren()) {
			if ( $preparedStatement === null ) { // If this is a root node the statement hasn't been prepared yet so we prepare it
				// Connect to database
				$handler = new Database();
				
				// Get comments from database
				$this->preparedStatement = $handler->prepare('
					SELECT `cid`, `message`, `parent`, `children`, `time`, `author_name` AS `author` 
					FROM `comment`
					WHERE `parent` = :parent
					ORDER BY `time` DESC
				');
			}
			else { // If this is not a root node the statement has been prepared already
				$this->preparedStatement = $preparedStatement;
			}			
			
			// Bind the cid of the current node as parent and execute the statement
			$this->preparedStatement->execute( array(
				':parent' => $this->cid
			));
			
			// If table is empty
			if ($this->preparedStatement->rowCount() === 0) {
				throw new Exception('Error: Database returned no results');
			}
			
			// Add children to node
			while ( $child = $this->preparedStatement->fetchObject('TreeNode', array(false, $this->preparedStatement)) ) {
				$this->addChild($child);
			}
		}
		else if ($rootNode === true) { // If this is a root node
			// Connect to database
			$handler = new Database();
			if($this->parentid == null) {
				// Get comments from database
			$this->preparedStatement = $handler->prepare('
				SELECT `cid`, `message`, `parent`, `children`, `time`, `author_name` AS `author`
				FROM `comment`
				WHERE `sid` = :sid
				AND `parent` IS NULL
				ORDER BY `time` DESC
			');
			
			// Bind the ID of the comment section and execute the statement
			$this->preparedStatement->execute( array(
				':sid' => $this->sid
			));
			}

			else { 
				// Get comments from database
			$this->preparedStatement = $handler->prepare('
				SELECT `cid`, `message`, `parent`, `children`, `time`, `author_name` AS `author`
				FROM `comment`
				WHERE `cid` = :cid
				AND `parent` IS NULL
				ORDER BY `time` DESC
			');
			
			// Bind the ID of the comment section and execute the statement
			$this->preparedStatement->execute( array(
				':cid' => $this->parentid
			));
			}
			
			
			// If table not empty
			if ($this->preparedStatement->rowCount() > 0) {
				// Add children to node
				while ( $child = $this->preparedStatement->fetchObject('TreeNode', array(false)) ) {
					$this->addChild($child);
				}
				
				$this->children = true;
			}
		}
		
	}
	
	public function getCid() {
		return $this->cid;
	}
	
	public function getMessage() {
		return $this->message;
	}
	
	public function getAuthor() {
		return $this->author;
	}
	
	public function getTime() {
		return $this->time;
	}
	
	public function getParent() {
		return $this->parent;
	}
	
	public function hasChildren() {
		if ( $this->children > 0 ) {
			return true;
		}
		else {
			return false;
		}
	}
	
	public function getChildren() {
		return $this->childrenList;
	}
	
	private function addChild($child) {
		$this->childrenList[$child->getCid()] = $child;
	}
}
