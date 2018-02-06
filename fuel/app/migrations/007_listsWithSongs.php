<?php 

namespace Fuel\Migrations;

class ListsWithSongs
{

    function up()
    {
        \DBUtil::create_table('listsWithSongs', array(
            'id_list' => array('type' => 'int', 'constraint' => 11),
            'id_song' => array('type' => 'int', 'constraint' => 11),
        ), array('id_list', 'id_song'), false, 'InnoDB', 'utf8_unicode_ci',
		    array(
		        array(
		            'constraint' => 'claveAjenaListasContienenCancionesAListas',
		            'key' => 'id_list',
		            'reference' => array(
		                'table' => 'lists',
		                'column' => 'id'
		            ),
		            'on_update' => 'CASCADE',
		            'on_delete' => 'RESTRICT'
		        ),
		        array(
		            'constraint' => 'claveAjenaListasContienenCancionesACanciones',
		            'key' => 'id_song',
		            'reference' => array(
		                'table' => 'songs',
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
       \DBUtil::drop_table('listsWithSongs');
    }
}