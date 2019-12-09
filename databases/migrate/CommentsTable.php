<?php
namespace databases\migrate;

use modules\uloleorm\migrate\Migrate;

class CommentsTable extends Migrate {
    public function database() {
        $this->create('comments', function($table) {
            $table->int("id")->ai();
            $table->int("userid");
            $table->int("postid");
            $table->text("contents");
            $table->timestamp("created")->currentTimestamp();
        });
    }
}
