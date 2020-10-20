<?php
/**
 * WP Migration Interface
 */

namespace migrations;


interface WP_Migration_Interface
{
    public function up();
    public function down();

}