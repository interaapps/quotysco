<?php
namespace databases\migrate;

use modules\uloleorm\migrate\Migrate;

class PostsTable extends Migrate {
    public function database() {
        $this->create('posts', function($table) {
            $table->int("id")->ai();
            $table->int("blogid");
            $table->int("userid");
            
            $table->string("title");
            $table->string("link");
            $table->string("contents");

            $table->string("image");

            $table->enum("type", ["POST"]);
            
            $table->timestamp("created")->currentTimestamp();
        });
    }
}
