<?php
namespace databases;

use modules\uloleorm\Table;
class PostsTable extends Table {

    public $id,
           $blogid,
           $userid,
           $title,
           $link,
           $contents,
           $image,
           $type,
           $created;
    
    public function database() {
        $this->_table_name_ = "posts";
        $this->__database__ = "main";
    }

}
