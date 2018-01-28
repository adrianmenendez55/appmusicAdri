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
                return $this->respuesta(500, $e->getMessage());
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
            return $this->respuesta(500, $e->getMessage());
        }
    }

    public function post_delete()
    {
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
        /*return $this->respuesta(500, 'trace');
        exit;*/
        $users = Model_Users::find('all');
        return $this->response(Arr::reindex($users));
    }       
}