<?php
namespace databases;

use modules\uloleorm\Table;
class UsersFollowingTable extends Table {

    public $id,
           $userid,
           $following,
           $created;
    
    public function database() {
        $this->_table_name_ = "users_following";
        $this->__database__ = "main";
    }

}
