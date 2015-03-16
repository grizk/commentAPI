<?php

class CommentSection {
	private $sid = null; // Comment Section ID
	private $tree = null;
	private $status = true;
	private $display = '';
	private $limit = null;
	private $level =null;
	private $parent = null;

	public function __construct ( $sid, $limit, $level, $parent = 0 ) {
		
		if ( ($this->sid = (int) $sid) <= 0 ) {
			$this->sid = null;
			throw new InvalidArgumentException('Section ID must be a positive integer');
		}

		if ( ($this->limit = (int) $limit) <= 0 ) {
			$this->limit = null;
			//throw new InvalidArgumentException('Limit must be a positive integer');
		}

		if ( ($this->level = (int) $level) <= 0 ) {
			$this->level = null;
			//throw new InvalidArgumentException('Level must be a positive integer');
		}

		if ( ($this->parent = (int) $parent) <= 0 ) {
			$this->parent = null;
			//throw new InvalidArgumentException('Level must be a positive integer');
		}
		
		try {
			if($this->parent === null) { 
				$this->tree = new TreeNode($this->sid, true); // We create a peudo-node that will fetch all node with null parent
			}
			else {
				$this->tree = new TreeNode($this->sid, true, $this->parent); // We create a peudo-node that will fetch all node with null parent
			}
		}
		catch (Exception $e) {
			$this->status = false;
			echo $e;
		}
		
		$this->createDisplay();
	}
	
	public function doComments() {
		echo json_encode($this->display);
	}
	
	private function createDisplay () {
		//$this->display .= '<div class="comment-section">';

		if ( $this->status === false ) {
			$this->display .= 
				'An error has been occurred'; // If database error
		}
		else if ( $this->tree->hasChildren() === false ) { // If no comment exist yet
			$this->display = 
						array (
					'status_code' => 5,
					'message_id' => 'No Results Found'
				);
		}
		else {
			
				
			
			// Generate comment markup and return
			$this->display = $this->traverseTree($this->tree->getChildren(), $this->limit, $this->level, 0, 0); // We don't want to display the pseudo-node so we pass its children
			
			
		}
		
		
	}
	
	private function traverseTree($tree ,$limit, $level, $cur_lim, $cur_lvl) {

		$display = array();
		

		foreach($tree as $twig) {
			$comment = new stdClass();

			if($cur_lim >= $limit && $limit != null) {
				$comment->remainingComments = $cur_lim - $limit + 1;
			}

			else {
				$comment->id = $twig->getCid();
				$comment->message = $twig->getMessage();

				if ($twig->hasChildren()) { 
				$cur_lim ++;
				$cur_lvl ++;
				if($cur_lvl > $level && $level != null) {
					$comment->remainingChildren = $cur_lvl - $level;
				}
				else {
					$comment->children = $this->traverseTree($twig->getChildren(), $this->limit, $this->level, $cur_lim, $cur_lvl);

				}
			}
								
			}
			
			array_push($display, $comment);
			$cur_lim ++;
			//echo "push to array ";
			
			//$display['id'] = $twig->getCid();
			//$display['message'] = $twig->getMessage();
			
			
			
		}

		return $display;

		
	}
	
}
?>
