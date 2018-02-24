<?php

class Model_Users extends Orm\Model
{
    protected static $_table_name = 'users';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id', // both validation & typing observers will ignore the PK
        'username' => array(
            'data_type' => 'varchar'   
        ),
        'password' => array(
            'data_type' => 'varchar'   
        ),
        'email' => array(
            'data_type' => 'varchar'   
        ),
        'id_device' => array(
            'data_type' => 'varchar'   
        ),
        'photo' => array(
            'data_type' => 'varchar'   
        ),
        'description' => array(
            'data_type' => 'varchar'   
        ),
        'birthday' => array(
            'data_type' => 'varchar'   
        ),
        'coord_x' => array(
            'data_type' => 'decimal'   
        ),
        'coord_y' => array(
            'data_type' => 'decimal'   
        ),
        'city' => array(
            'data_type' => 'varchar'   
        ),
        'id_rol' => array(
            'data_type' => 'int'   
        )
    );

    /*protected static $_has_many = array(
        'following' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Following',
            'key_to' => 'id_user_follower',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );

    protected static $_has_many = array(
        'following' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Following',
            'key_to' => 'id_user_followed',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );*/
}