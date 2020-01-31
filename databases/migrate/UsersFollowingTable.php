<?php
namespace databases\migrate;

use modules\uloleorm\migrate\Migrate;

class UsersFollowingTable extends Migrate {
    public function database() {
        $this->create('users_following', function($table) {
            $table->int("id")->ai();
            $table->int("userid");
            $table->int("following");
            $table->timestamp("created")->currentTimestamp();
        });
    }
}
