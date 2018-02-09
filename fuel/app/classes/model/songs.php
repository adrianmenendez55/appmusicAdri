<?php

class Model_Songs extends Orm\Model
{
    protected static $_table_name = 'songs';
    protected static $_primary_key = array('id');
    protected static $_properties = array(
        'id', // both validation & typing observers will ignore the PK
        'title' => array(
            'data_type' => 'varchar'   
        ),
        'artist' => array(
            'data_type' => 'varchar'   
        ),
        'url' => array(
            'data_type' => 'varchar'   
        ),
        'reproductions' => array(
            'data_type' => 'int'   
        )
    );
}