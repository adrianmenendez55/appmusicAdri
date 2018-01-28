<?php 

use Firebase\JWT\JWT;

class Controller_Users extends Controller_Base
{
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
}