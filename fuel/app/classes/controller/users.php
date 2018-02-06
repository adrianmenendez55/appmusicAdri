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
                $users->save();
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
                    "password" => $password
                );
                $Token = JWT::encode($datatoken, $this->key);
                
                return $this->respuesta(200, 'Login Correcto', ['token' => $Token, 'username' => $username]);
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
        
        $user = Model_Users::find($_POST['id']);
        $username = $user->username;
        $user->delete();
        $json = $this->response(array(
            'code' => 200,
            'message' => 'usuario borrado',
            'data' => $username
        ));
        return $json;
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
}