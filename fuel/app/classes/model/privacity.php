<?php

class Model_Privacity extends Orm\Model
{
	protected static $_table_name = 'privacity';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id', // both validation & typing observers will ignore the PK
        'profile' => array(
            'data_type' => 'int'   
        ),
        'friends' => array(
            'data_type' => 'int'   
        ),
        'lists' => array(
            'data_type' => 'int'   
        ),
        'notifications' => array(
            'data_type' => 'int'   
        ),
        'location' => array(
            'data_type' => 'int'   
        ),
        'id_user' => array(
            'data_type' => 'int'   
        )
    );
}