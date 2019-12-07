<?php
namespace databases\migrate;

use modules\uloleorm\migrate\Migrate;

class BlogUsersTable extends Migrate {
    public function database() {
        $this->create('blog_users', function($table) {
            $table->int("id")->ai();
            $table->int("blogid");
            $table->int("userid");
            $table->enum("rank", ["OWNER", "WRITER", "WATCHER"]);
            $table->timestamp("created")->currentTimestamp();
        });
    }
}
