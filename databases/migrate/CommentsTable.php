<?php
namespace databases\migrate;

use modules\uloleorm\migrate\Migrate;

class CommentsTable extends Migrate {
    public function database() {
        $this->create('comments', function($table) {
            $table->int("id")->ai();
            $table->int("userid");
            $table->string("text");
            $table->int("action");
            $table->enum("type", ["COMMENT","RESPONSE"]);
            $table->timestamp("created")->currentTimestamp();
        });
    }
}
