<?php

class Model_Following extends Orm\Model
{
	protected static $_table_name = 'friends';
    protected static $_primary_key = array('id_user_follower','id_user_followed');
    protected static $_properties = array(
        'id_user_follower' => array(
            'data_type' => 'int'   
        ),
        'id_user_followed' => array(
            'data_type' => 'int'   
        )
    );
}