<?php
namespace databases;

use modules\uloleorm\Table;
class TestTable extends Table {

    public $username, 
           $password;

    public function database() {
        $this->_table_name_ = "User";
        // The __database__ default value is "main"
        $this->__database__ = "main";
    }

}