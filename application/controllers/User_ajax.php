<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_ajax extends CI_Controller {

    private $request;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->load->helper('email');
        $this->load->library('session');
        $this->load->model('User_model');
        $this->request = json_decode(file_get_contents('php://input'));
    }

    public function login()
    {
        $msg = [];
        $status = 200;
        $email = $this->request->email;
        $password = $this->request->password;

        if (!is_null($email) && !is_null($password)) {

            if (valid_email($email)) {            
                $user = $this->User_model->get(['email' => $email]);
                if (!empty($user)) {
                    $user = $user[0];
                    if (password_verify($password, $user->clave)) {
                        $status = 200;
                        // Codigo para sesion en codeigniter
                        $userdata = [
                            'id' => $user->id,
                            'nombre_completo' => strtoupper($user->nombres.' '.$user->apellidos),
                            'email' => $user->email
                        ];
                        $this->session->set_userdata($userdata);
                        $msg[] = 'Bienvenid@ '.$this->session->userdata('nombre_completo');
                    } else {
                        $status = 401;
                        $msg[] = 'Clave incorrecta';
                    }
                } else {
                    $status = 401;
                    $msg[] = 'No existe ningún usuario asociado al email';
                }
            } else {
                $status = 401;
                $msg[] = 'El email no es una dirección de correo válida';
            }
        } else {
            $status = 401;
            $msg[] = 'El email y la clave son obligatorios';
        }
        
        $this->output->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['msg' => $msg], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function registro()
    {
        $msg = [];
        $status = 200;
        $clave = $this->request->clave;
        $confirma_clave = $this->request->confirma_clave;
        $email = $this->request->email;
        $existe = $this->User_model->get(['email' => $email]);
        
        if (empty($existe)) {
            if ($clave == $confirma_clave) {
                if (valid_email($email)) {
                    $this->User_model->insert([
                        'nombres' => $this->request->nombres,
                        'apellidos' => $this->request->apellidos,
                        'email' => $email,
                        'clave' => password_hash($clave, PASSWORD_DEFAULT),
                    ]);
                    $status = 200;
                    $msg[] = 'Su usuario ha sido creado con éxito';
                } else {
                    $status = 400;
                    $msg[] = 'El email no es una dirección de correo válida';
                }
            } else {
                $status = 400;
                $msg[] = 'Las claves no coinciden';
            }
        } else {
            $status = 400;
            $msg[] = 'Esta cuenta de <b>email</b> ya se encuentra registrada';
        }
        
        $this->output->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['msg' => $msg], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
