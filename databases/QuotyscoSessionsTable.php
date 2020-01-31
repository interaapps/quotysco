<?php
namespace databases;
use modules\uloleorm\Table;
class QuotyscoSessionsTable extends Table {
    public $id, 
           $session_id,
           $userid,
           $user_key;
    
    public function database() {
        $this->_table_name_ = "quotysco_sessions";
        $this->__database__ = "main";
    }
}