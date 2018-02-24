<?php

use Firebase\JWT\JWT;

class Controller_Songs extends Controller_Base
{
	private $key = "sujdn53h3be62hbsy2bs27J5NFJ5K4EDLs2h23d";

	public function post_addSongs()
    {
        try 
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

            if ( empty($_POST['title']) || empty($_POST['artist']) || empty($_POST['url']) ) 
            {
                return $this->respuesta(400, 'Existen campos vacíos', []);
            }

            if (filter_var($_POST['url'], FILTER_VALIDATE_URL) == false)
            {
                return $this->respuesta(400, 'URL no válida', []);
            }

            $title = $_POST['title'];
            $artist = $_POST['artist'];
            $url = $_POST['url'];

            if($this->isSongCreated($title, $url))
            {
                return $this->respuesta(400, 'Esta canción ya existe', []);
            }
            else
            {   
                $songs = new Model_Songs();
                $songs->title = $title;
                $songs->artist = $artist;
                $songs->url = $url;
                $songs->reproductions = 0;
                $songs->save();
                return $this->respuesta(200, 'Canción creada', ['song' => $songs]);
            }
        } 
        catch (Exception $e) 
        {
            return $this->respuesta(500, $e->getMessage(), []);
        }
    }

    public function isSongCreated($title, $url)
    {
        $songs = Model_Songs::find('all', array(
            'where' => array(
                array('title', $title),
                array('url', $url)
            )
        ));

        if($songs != null)
        {
            return true;
        }
        else 
        {
            return false;
        }
    }

    public function get_songs()
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

        $songs = Model_Songs::find('all');
        return $this->response(Arr::reindex($songs));
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
        
        $songs = Model_Songs::find($_POST['id']);
        
        if($songs != null)
        {
            $songs->delete();
            return $this->respuesta(200, 'Canción borrada', []);

        }
        else
        {
            return $this->respuesta(400, 'La canción no existe', []);
        }
    }

    public function post_editSong()
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
            return $this->respuesta(400, 'Escribe el id de la canción a editar', []);
        }
        else
        {
            if (filter_var($_POST['url'], FILTER_VALIDATE_URL) == false)
            {
                return $this->respuesta(400, 'URL no válida', []);
            }
            
            if ( empty($_POST['title']) || empty($_POST['artist']) || empty($_POST['url']) ) 
            {
                return $this->respuesta(400, 'Existen campos vacíos', []);
            }
            else
            {
                $songs = Model_Songs::find($_POST['id']);
                $songs->title = $_POST['title'];
                $songs->artist = $_POST['artist'];
                $songs->url = $_POST['url'];
                $songs->save();

                return $this->respuesta(200, 'Canción editada con éxito', ['Song' => $songs]);
            }
        }
    }

    public function post_playSong()
    {
        try
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

            if (empty($_POST['id'])) 
            {
                return $this->respuesta(400, 'Escribe el id de la canción a editar', []);
            }
            else
            {
                if (!isset($_POST['id'])) 
                {
                    return $this->respuesta(400, 'Esta canción no existe', []);
                }
                else
                {
                    $songs = Model_Songs::find($_POST['id']);
                    $songs->reproductions += 1;
                    $songs->save();

                    return $this->respuesta(200, 'Reproduciendo canción...', ['reproductions' => $songs->reproductions]);
                }
            }
        }
        catch (Exception $e) 
        {
            return $this->respuesta(500, $e->getMessage(), []);
        }
    }
}