<?php

use Firebase\JWT\JWT;

class Controller_Lists extends Controller_Base
{
	private $key = "sujdn53h3be62hbsy2bs27J5NFJ5K4EDLs2h23d";

	public function post_create()
    {
    	try
    	{
    		$header = apache_request_headers();
            if (isset($header['Authorization'])) 
            {
                $token = $header['Authorization'];
                $dataJwtUser = JWT::decode($token, $this->key, array('HS256'));
                $id_user = $dataJwtUser->id;
            }
            else
            {
                return $this->respuesta(400, 'Usuario no logueado', []);
            }

            if ( empty($_POST['title'])) 
            {
                return $this->respuesta(400, 'Introduce un título para tu lista', []);
            }
            else
            {
            	$title = $_POST['title'];

            	if($this->isListCreated($title))
            	{
            		return $this->respuesta(400, 'Ya existe una lista con este título', []);
            	}
            	else
            	{
            		$lists = new Model_Lists();
                	$lists->title = $title;
                	$lists->editable = 1;
                	$lists->id_user = $id_user;
                	$lists->save();
            		return $this->respuesta(200, 'Lista creada', ['List' => $lists]);
            	}
            }
    	}
    	catch (Exception $e) 
        {
            return $this->respuesta(500, $e->getMessage(), []);
        }
    }

    public function isListCreated($title)
    {
        $lists = Model_Lists::find('all', array(
            'where' => array(
                array('title', $title)
            )
        ));

        if($lists != null)
        {
            return true;
        }
        else 
        {
            return false;
        }
    }

    public function get_lists()
    {
        $header = apache_request_headers();
        if (isset($header['Authorization'])) 
        {
            $token = $header['Authorization'];
            $dataJwtUser = JWT::decode($token, $this->key, array('HS256'));
        }
        else
        {
            return $this->respuesta(400, 'Usuario no logueado', []);
        }
        	$lists = Model_Lists::find('all');
        	return $this->response(Arr::reindex($lists));
    }

    public function post_delete()
    {
        $header = apache_request_headers();
        if (isset($header['Authorization'])) 
        {
            $token = $header['Authorization'];
            $dataJwtUser = JWT::decode($token, $this->key, array('HS256'));
        }
        else
        {
            return $this->respuesta(400, 'Usuario no logueado', []);
        }
        
        $lists = Model_Songs::find($_POST['id']);
        $lists = $lists->title;
        $lists->delete();

        return $this->respuesta(200, 'Lista borrada', []);
    }

    public function post_editList()
    {
        $header = apache_request_headers();
        if (isset($header['Authorization'])) 
        {
            $token = $header['Authorization'];
            $dataJwtUser = JWT::decode($token, $this->key, array('HS256'));
        }
        else
        {
            return $this->respuesta(400, 'Usuario no logueado', []);
        }

        if (!isset($_POST['id'])) 
        {
            return $this->respuesta(400, 'Escribe el id de la lista a editar', []);
        }
        else
        {
            if (empty($_POST['title'])) 
            {
                return $this->respuesta(400, 'Introduce un título nuevo', []);
            }

            else
            {
                $lists = Model_Lists::find($_POST['id']);
                $lists->title = $_POST['title'];
                $lists->save();

                return $this->respuesta(200, 'Lista editada con éxito', ['List' => $lists]);
            }
        }
    }

    public function post_addSong()
    {
        $header = apache_request_headers();
        if (isset($header['Authorization'])) 
        {
            $token = $header['Authorization'];
            $dataJwtUser = JWT::decode($token, $this->key, array('HS256'));
        }
        else
        {
            return $this->respuesta(400, 'Usuario no logueado', []);
        }

        if (empty($_POST['id_list']) || empty($_POST['id_song'])) 
        {
            return $this->respuesta(400, 'Existen campos vacíos', []);
        }
        else
        {
            $id_list = $_POST['id_list'];
            $id_song = $_POST['id_song'];

            $add = Model_Add::find('all', array(
                'where' => array(
                    array('id_list', $id_list),
                    array('id_song', $id_song)
                )
            ));

            if(!empty($add))
            {
                return $this->respuesta(400, 'La canción ya estaba añadida a la lista', []);
                
            }
            else
            {
                $dataJwtUser = JWT::decode($token, $this->key, array('HS256'));
                $id_user = $dataJwtUser->id;

                $list = Model_Lists::find('all', array(
                    'where' => array(
                        array('id', $id_list),
                        array('id_user', $id_user)
                    )
                ));

                if(isset($list))
                {
                    $add = New Model_Add();
                    $add->id_list = $id_list;
                    $add->id_song = $id_song;
                    $add->save();

                    return $this->respuesta(200, 'Canción añadida a la lista', ['listWithSongs' => $add]);
                }
                else
                {
                    return $this->respuesta(400, 'La lista no existe', []);
                }
            }
        }
    }
}