<?php
namespace databases;

use modules\uloleorm\Table;
class BlogsTable extends Table {

    public $id,
           $name,
           $picture,
           $description,
           $homepage,
           $type,
           $created;
    
    public function database() {
        $this->_table_name_ = "blogs";
        $this->__database__ = "main";
    }

}
