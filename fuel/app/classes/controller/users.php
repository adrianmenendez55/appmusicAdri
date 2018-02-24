<?php 

use Firebase\JWT\JWT;

class Controller_Users extends Controller_Base
{
    private $key = "sujdn53h3be62hbsy2bs27J5NFJ5K4EDLs2h23d";

    public function post_create()
    {
        try 
        {
            if ( empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['repeatPass']) ) 
            {
                return $this->respuesta(400, 'Hay campos vacíos', []);
            }

            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL ) == false)
            {
                return $this->respuesta(400, 'La dirección de correo no es válida', []);
            }

            if(($_POST['password']) != ($_POST['repeatPass']))
            {
                return $this->respuesta(400, 'Las contraseñas no coinciden', []);
            }

            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            if($this->isUserCreated($username, $email))
            {
                return $this->respuesta(400, 'El nombre y/o el email ya existen', []);
            }
            else
            {   
                $users = new Model_Users();
                $users->username = $username;
                $users->email = $email;
                $users->password = $password;
                $users->id_rol = 2;
                $users->id_device = random_int(0, 5000000);
                $users->coord_x = random_int(0, 5000000) / 10;
                $users->coord_y = random_int(0, 5000000) / 10;
                $users->save();

                $privacityUser = new Model_Privacity();
                $privacityUser->profile = 1;
                $privacityUser->friends = 0;
                $privacityUser->lists = 1;
                $privacityUser->notifications = 1;
                $privacityUser->location = 0;
                $privacityUser->id_user = $users->id;
                $privacityUser->save();

                return $this->respuesta(200, 'Usuario creado', ['user' => $users]);

            }
        } 
        catch (Exception $e) 
        {
                return $this->respuesta(500, $e->getMessage(), []);
        }
    }

    public function isUserCreated($username, $email)
    {
        $users = Model_Users::find('all', array(
            'where' => array(
                array('username', $username),
                array('email', $email)
            )
        ));

        if($users != null){
            return true;
        }
        else 
        {
            return false;
        }
    }

    public function get_login()
    {
        try
        {
            if ( empty($_GET['username']) || empty($_GET['password']) ) 
            {
                return $this->respuesta(400, 'Hay campos vacíos', []);
            }

            $input = $_GET;
            $users = Model_Users::find('all', array(
                'where' => array(
                    array('username', $input['username']),array('password', $input['password'])
                )
            ));

            if (!empty($users))
            {
                foreach ($users as $key => $value)
                {
                    $id = $users[$key]->id;
                    $username = $users[$key]->username;
                    $password = $users[$key]->password;
                    $id_rol = $users[$key]->id_rol;
                    $id_device = $users[$key]->id_device;
                    $coord_x = $users[$key]->coord_x;
                    $coord_y = $users[$key]->coord_y;
                }
            }
            else
            {
                return $this->respuesta(400, 'Error de autenticación', []);
            }

            if ($username == $input['username'] and $password == $input['password'])
            {
                $datatoken = array(
                    "id" => $id,
                    "username" => $username,
                    "password" => $password,
                    "id_rol" => $id_rol,
                    "id_device" => $id_device,
                    "coord_x"=> $coord_x,
                    "coord_y"=> $coord_y
                );
                $Token = JWT::encode($datatoken, $this->key);
                
                return $this->respuesta(200, 'Login Correcto', ['token' => $Token, 'username' => $username, 'id_device' => $id_device, 'coord_x' => $coord_x, 'coord_y' => $coord_y]);
            }
        }
        catch (Exception $e) 
        {
            return $this->respuesta(500, $e->getMessage(), []);
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
        
        $users = Model_Users::find($_POST['id']);

        if ($users != null)
        {
            $users->delete();

            return $this->respuesta(200, 'Usuario borrado', []);
        }
        else
        {
            return $this->respuesta(400, 'El usuario no existe', []);
        }
    }

    public function get_users()
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

        $users = Model_Users::find('all');
        return $this->response(Arr::reindex($users));
    }

    public function post_emailValidate()
    {
        try 
        {
            if ( empty($_POST['email'])) 
            {
               return $this->respuesta(400, 'Email no introducido', []); 
            }

            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL ) == false)
            {
                return $this->respuesta(400, 'La dirección de correo no es válida', []);
            }

            // Validación de e-mail
            $input = $_POST;
            $users = Model_Users::find('all', array(
                'where' => array(
                    array('email', $input['email'])
                )
            ));

            if (!empty($users))
            {
                foreach ($users as $key => $value)
                {
                    $id = $users[$key]->id;
                    $username = $users[$key]->username;
                    $email = $users[$key]->email;
                }
            }
            else
            {
                return $this->respuesta(400, 'El email no existe', []);
            }

            if ($email == $input['email'])
            {
                $tokendata = array(
                    "id" => $id,
                    "username" => $username,
                    "email" => $email
                );

                $token = JWT::encode($tokendata, $this->key);

                return $this->respuesta(200, 'Email validado', ['token' => $token]);
            }
        }
        catch (Exception $e)
        {
            return $this->respuesta(500, $e->getMessage(), []);
        }
    }

    public function post_changePass()
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

            if (empty($_POST['newPass']) || empty($_POST['repeatPass'])) 
            {
                return $this->respuesta(400, 'Existen campos vacíos', []);
            }

            if(($_POST['newPass']) == ($_POST['repeatPass']))
            {
                $input = $_POST;
                $user = Model_Users::find($dataJwtUser->id);
                $user->password = $input['newPass'];
                $user->save();
                
                return $this->respuesta(200, 'Contraseña cambiada', []);             
            }   
            else
            {
                return $this->respuesta(400, 'Las contraseñas no coinciden', []);
            }
        }
        catch (Exception $e)
        {
            return $this->respuesta(500, $e->getMessage(), []);
        }
    }

    public function post_createAdmin()
    {
        try 
        {
            if ( empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['repeatPass']) ) 
            {
                return $this->respuesta(400, 'Existen campos vacíos', []);
            }

            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL ) == false)
            {
                return $this->respuesta(400, 'La dirección de correo no es válida', []);
            }

            if(($_POST['password']) != ($_POST['repeatPass']))
            {
                return $this->respuesta(400, 'Las contraseñas no coinciden', []);
            }

            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            if($this->isAdminCreated())
            {
                return $this->respuesta(400, 'El usuario administrador ya existe', []);
            }
            else
            {   
                $users = new Model_Users();
                $users->username = $username;
                $users->email = $email;
                $users->password = $password;
                $users->id_rol = 1;
                $users->save();

                $privacityUser = new Model_Privacity();
                $privacityUser->profile = 1;
                $privacityUser->friends = 0;
                $privacityUser->lists = 1;
                $privacityUser->notifications = 1;
                $privacityUser->location = 0;
                $privacityUser->id_user = $users->id;
                $privacityUser->save();

                return $this->respuesta(200, 'Administrador creado', ['user' => $users]);

            }
        } 
        catch (Exception $e) 
        {
            return $this->respuesta(500, $e->getMessage(), []);
        }
    }

    public function isAdminCreated()
    {
        $users = Model_Users::find('first', array(
            'where' => array(
                array('id_rol', 1)
            )
        ));

        if($users != null)
        {
            return true;
        }
        else 
        {
            return false;
        }
    }

    public function post_follow()
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

        if (empty($_POST['id_user_toFollow'])) 
        {
            return $this->respuesta(400, 'Existen campos vacíos', []);
        }
        else
        {
            $id_user_followed = $_POST['id_user_toFollow'];

            $following = Model_Following::find('all', array(
                'where' => array(
                    array('id_user_follower', $id_user),
                    array('id_user_followed', $id_user_followed)
                )
            ));

            if(empty($following))
            {
                $dataJwtUser = JWT::decode($token, $this->key, array('HS256'));
                $id_user = $dataJwtUser->id;

                $following = New Model_Following();
                $following->id_user_follower = $id_user;
                $following->id_user_followed = $id_user_followed;
                $following->save();

                return $this->respuesta(200, 'Sigues a este usuario', ['follows' => $id_user_followed]);
            }
            else
            {
                return $this->respuesta(400, 'Ya sigues a este usuario', []);
            }
        }
    }

    public function post_unfollow()
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

        if (empty($_POST['id_user_unfollow'])) 
        {
            return $this->respuesta(400, 'Existen campos vacíos', []);
        }
        else
        {
            $id_user_unfollow = $_POST['id_user_unfollow'];

            $following = Model_Following::find('first', array(
                'where' => array(
                    array('id_user_follower', $id_user),
                    array('id_user_followed', $id_user_unfollow)    
                )
            ));

            if($following != null)
            {
                $following->delete();

                return $this->respuesta(200, 'Has dejado de seguir a este usuario', ['unfollows' => $id_user_unfollow]);
            }
            else
            {
                return $this->respuesta(400, 'No sigues a este usuario', []);
            }
        }
    }

    public function get_follows()
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

        $following = Model_Following::find('all');
        return $this->response(Arr::reindex($following));
    }

    public function isUserFollowed($id_user_toFollow)
    {
        $following = Model_Following::find('first', array(
            'where' => array(
                array('id_user_followed', $id_user_toFollow)
            )
        ));

        if($following != null)
        {
            return true;
        }
        else 
        {
            return false;
        }
    }

    /*public function post_editPrivacity()
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

        if (empty($_POST['profile']) || empty($_POST['friends']) || empty($_POST['lists']) || empty($_POST['notifications']) || empty($_POST['location'])) 
        {
            return $this->respuesta(400, 'Existen campos vacíos', []);
        }
        else
        {
            $privacityUser = Model_Privacity::find($id_user);
            $privacityUser->id = $id_user;
            $privacityUser->profile = $_POST['profile'];
            $privacityUser->friends = $_POST['friends'];
            $privacityUser->lists = $_POST['lists'];
            $privacityUser->notifications = $_POST['notifications'];
            $privacityUser->location = $_POST['location'];
            $privacityUser->id_user = $id_user;
            $privacityUser->save();
                
            return $this->respuesta(200, 'Preferencias de privacidad cambiadas', []);             
        }   
    }*/

    public function post_editProfile()
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

            if (empty($_POST['photo']) || empty($_POST['description']) || empty($_POST['birthday']) || empty($_POST['city'])) 
            {
                return $this->respuesta(400, 'Existen campos vacíos', []);
            }
            else
            {
                $edit = Model_Users::find($dataJwtUser->id);
                $edit->photo = $_POST['photo'];
                $edit->description = $_POST['description'];
                $edit->birthday = $_POST['birthday'];
                $edit->city = $_POST['city'];

                $edit->save();
                
                return $this->respuesta(200, 'Cambios en el perfil guardados', []);
            }
        }
        catch (Exception $e)
        {
            return $this->respuesta(500, $e->getMessage(), []);
        } 
    }


    /*public function uploadImage()
    {
        try
        {
            // Custom configuration for this upload
            $config = array(
                'path' => DOCROOT . 'assets/img',
                'randomize' => true,
                'ext_whitelist' => array('img', 'jpg', 'jpeg', 'gif', 'png'),
            );
            // process the uploaded files in $_FILES
            Upload::process($config);
            // if there are any valid files
            if (Upload::is_valid())
            {
                // save them according to the config
                Upload::save();
                foreach(Upload::get_files() as $file)
                {
                    $users = Model_Users::find($dataJwtUser->id);
                    $users->photo = 'http://' . $_SERVER['SERVER_NAME'] . '/appmusicAdrian/public/assets/img/' . $file['saved_as'];
                    $users->save();
                }
            }
            return $this->response(array(
                'code' => 200,
                'message' => 'Datos actualizados',
                'data' => [$users]
            ));
            // and process any errors

            foreach (Upload::get_errors() as $file)
            {
                return $this->response(array(
                    'code' => 500,
                    'message' => 'No se ha podido subir la imagen',
                    'data' => []
                ));
            }        
        }
        catch (Exception $e)
        {
           return $this->respuesta(500, $e->getMessage(), []); 
        }
    }*/
}