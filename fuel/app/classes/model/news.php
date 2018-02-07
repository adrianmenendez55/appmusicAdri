<?php

class Model_News extends Orm\Model
{
    protected static $_table_name = 'news';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id', // both validation & typing observers will ignore the PK
        'title' => array(
            'data_type' => 'varchar'   
        ),
        'description' => array(
            'data_type' => 'varchar'   
        ),
        'id_user' => array(
            'data_type' => 'int'   
        )
    );
}