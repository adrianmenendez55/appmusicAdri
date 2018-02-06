<?php 

namespace Fuel\Migrations;

class Privacity
{

    function up()
    {
        \DBUtil::create_table('privacity', array(
            'id' => array('type' => 'int', 'constraint' => 11, 'auto_increment' => true),
            'profile' => array('type' => 'int', 'constraint' => 1),
            'friends' => array('type' => 'int', 'constraint' => 1),
            'lists' => array('type' => 'int', 'constraint' => 1),
            'notifications' => array('type' => 'int', 'constraint' => 1),
            'location' => array('type' => 'int', 'constraint' => 1),
            'id_user' => array('type' => 'int', 'constraint' => 11),
        ), array('id'), false, 'InnoDB', 'utf8_unicode_ci',
		    array(
		        array(
		            'constraint' => 'claveAjenaPrivacidadAUsuarios',
		            'key' => 'id_user',
		            'reference' => array(
		                'table' => 'users',
		                'column' => 'id'
		            ),
		            'on_update' => 'CASCADE',
		            'on_delete' => 'RESTRICT'
		        )
		    )
		);
    }

    function down()
    {
       \DBUtil::drop_table('privacity');
    }
}