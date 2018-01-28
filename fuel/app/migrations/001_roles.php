<?php 

namespace Fuel\Migrations;

class Roles
{
    function up()
    {
        \DBUtil::create_table('roles', array(
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'tipo' => array('type' => 'varchar', 'constraint' => 100),
        ), array('id'));
        \DB::query("INSERT INTO roles (id,tipo) VALUES ('1','admin');")->execute();
        \DB::query("INSERT INTO roles (id,tipo) VALUES ('2','user');")->execute();
    }

    function down()
    {
       \DBUtil::drop_table('roles');
    }
}