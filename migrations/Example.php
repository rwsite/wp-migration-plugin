<?php
/**
 * Example Wordpress Migration
 */

namespace migrations;

class Example extends WP_Migration
{
    public function up()
    {
        // update_option('migration_example', true,false);
    }

    public function down()
    {
        // delete_option('migration_example');
    }
}
