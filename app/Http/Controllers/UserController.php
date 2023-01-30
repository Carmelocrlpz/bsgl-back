<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller {

    //
    public function register(Request $request) {
        //recoger dtaos de usuario por post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        if (!empty($params) && !empty($params_array)) {


            //limpiardatos
            $params_array = array_map('trim', $params_array);
            //validar datos
            $validate = \Validator::make($params_array, [
                        'name' => 'required|alpha',
                        'email' => 'required|email|unique:users', //comprobar si el uusaurio esta duplicado
                        'password' => 'required',
                        'password_confirmation' => 'required'
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado ',
                    'errors' => $validate->errors()
                );
            } else {
                //cifrar password
                //$pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost' => 4]);
                $pwd = hash('sha256', $params->password);

                //crear el usuario
                $user = new User();
                $user->name = $params_array['name'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->save();
                //$user->name = $params_array['name'];

                $data = array(
                    'status' => 'succes',
                    'code' => 200,
                    'message' => 'El usuario se ha creado '
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos'
            );
        }


        return response()->json($data, $data['code']);
    }

    public function login(Request $request) {
        $jwtAuth = new \JwtAuth();
        //recibir datos post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        //validar datos
        $validate = \Validator::make($params_array, [
                    'email' => 'required|email',
                    'password' => 'required'
        ]);

        if ($validate->fails()) {
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha podoodp identificar ',
                'errors' => $validate->errors()
            );
        } else {
            //cifrar password
            $pwd = hash('sha256', $params->password);
            //devover token o datos
            $signup = $jwtAuth->signup($params->email, $pwd);
            if (!empty($params->gettoken)) {
                $signup = $jwtAuth->signup($params->email, $pwd, true);
            }
        }

        return response()->json($signup, 200);
    }

    public function update(Request $request) {

        //comprobar uuario identificasdo

        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);
        //recoger datos por post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if ($checkToken && !empty($params_array)) {

            //sacar suuario idenificado
            $user = $jwtAuth->checkToken($token, true);

            //validar datos
            $validate = \Validator::make($params_array, [
                        'name' => 'required|alpha',
                        'email' => 'required|email|unique:users,' . $user->sub
            ]);
            //quitar campos que no se  quieren actualizar
            unset($params_array['id']);
            unset($params_array['email']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);
            //actualizar usuaroi en db
            $user_update = User::where('id', $user->sub)->update($params_array);
            //devolver array con resultado 
            $data = array(
                'code' => 200,
                'status' => 'success',
                'message' => 'Usuario actualizado'
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no esta identificado'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function detail($id) {
        $user = User::find($id);

        if (is_object($user)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user
            );
        }else{
                        $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El usuario no existe'
            );
        }
        return response()->json($data, $data['code']);
    }

}
