<?php

class Model_Lists extends Orm\Model
{
    protected static $_table_name = 'lists';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id', // both validation & typing observers will ignore the PK
        'title' => array(
            'data_type' => 'varchar'   
        ),
        'editable' => array(
            'data_type' => 'int'   
        ),
        'id_user' => array(
            'data_type' => 'int'   
        )
    );

    protected static $_many_many = array(
    'songs' => array(
            'key_from' => 'id',
            'key_through_from' => 'id_list',
            'table_through' => 'listsWithSongs',
            'key_through_to' => 'id_song',
            'model_to' => 'Model_Songs',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
}