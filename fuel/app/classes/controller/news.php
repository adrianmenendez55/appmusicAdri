<?php

use Firebase\JWT\JWT;

class Controller_News extends Controller_Base
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

            if (empty($_POST['title']) || empty($_POST['description'])) 
            {
                return $this->respuesta(400, 'Existen campos vacíos', []);
            }
            else
            {
            	$title = $_POST['title'];
            	$description = $_POST['description'];

                if($this->isNewsCreated($title, $description))
                {
                    return $this->respuesta(400, 'Esta noticia ya existe', []);
                }
                else
                {
                    $news = new Model_News();
                    $news->title = $title;
                    $news->description = $description;
                    $news->id_user = $id_user;
                    $news->save();
                    return $this->respuesta(200, 'Noticia creada', ['news' => $news]);
                }
            }
    	}
    	catch (Exception $e) 
        {
            return $this->respuesta(500, $e->getMessage(), []);
        }
    }

    public function isNewsCreated($title, $description)
    {
        $news = Model_News::find('all', array(
            'where' => array(
                array('title', $title),
                array('description', $description)
            )
        ));

        if($news != null)
        {
            return true;
        }
        else 
        {
            return false;
        }
    }

    public function get_myNews()
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

        $news = Model_News::find('all', array(
            'where' => array(
                array('id_user', $id_user)
            )
        ));

        if($news != null)
        {
            return $this->respuesta(200, 'Noticias del usuario', ['user_news' => $news]);
        }
        else 
        {
            return $this->respuesta(200, 'Este usuario no ha escrito noticias', []);
        }
    }

    public function get_news()
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

        $news = Model_News::find('all');
        return $this->response(Arr::reindex($news));
    }

    public function post_editNews()
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
            return $this->respuesta(400, 'Escribe el id de la noticia a editar', []);
        }
        else
        {
            if (empty($_POST['title']) || empty($_POST['description'])) 
            {
                return $this->respuesta(400, 'Existen campos vacíos', []);
            }
            else
            {
                $news = Model_News::find($_POST['id']);
                $news->title = $_POST['title'];
                $news->description = $_POST['description'];
                $news->save();

                return $this->respuesta(200, 'Noticia editada con éxito', ['News' => $news]);
            }
        }
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
        
        $news = Model_News::find($_POST['id']);
        $news = $news->title;
        $news = $news->description;
        $news->delete();

        return $this->respuesta(200, 'Noticia borrada', []);
    }

    // get_nearNews
}