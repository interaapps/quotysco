<?php
namespace databases;

use modules\uloleorm\Table;
class CommentsTable extends Table {

    public $id, 
           $userid,
           $postid,
           $contents,
           $created;
    
    public function database() {
        $this->_table_name_ = "comments";
        $this->__database__ = "main";
    }

}
