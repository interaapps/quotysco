<?php
namespace databases;

use modules\uloleorm\Table;
class CommentsTable extends Table {

    public $row1, // INSERT YOUR ROWS IN HERE 
           $row2;
    
    public function database() {
        $this->_table_name_ = "comments";
        // The __database__ default value is "main"
        $this->__database__ = "main";
    }

}
