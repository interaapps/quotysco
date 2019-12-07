<?php
namespace databases;

use modules\uloleorm\Table;
class BlogUsersTable extends Table {

    public $id, // INSERT YOUR ROWS IN HERE 
           $blogid,
           $userid,
           $rank,
           $table;
    
    public function database() {
        $this->_table_name_ = "blog_users";
        $this->__database__ = "main";
    }

}
