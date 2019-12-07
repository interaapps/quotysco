<?php
namespace databases\migrate;

use modules\uloleorm\migrate\Migrate;

class BlogsTable extends Migrate {
    public function database() {
        $this->create('blogs', function($table) {
            $table->int("id")->ai();
            $table->string("name");
            $table->string("picture");
            $table->string("description");
            $table->string("homepage");
            $table->enum("type", [
                "GROUP",
                "USER"
            ]);
            $table->timestamp("created")->currentTimestamp();
        });
    }
}
