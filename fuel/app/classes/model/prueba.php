<?php

class Model_Prueba extends Orm\Model
{
	protected static $_table_name = 'listsWithSongs';
    protected static $_primary_key = array('id_list','id_song');
    protected static $_properties = array(
        'id_list' => array(
            'data_type' => 'int'   
        ),
        'id_song' => array(
            'data_type' => 'int'   
        )
    );
}