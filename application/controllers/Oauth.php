<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Oauth extends CI_Controller {

    public $FB_APP_ID = '402076080424611';
    public $FB_APP_SECRET = '92518fb951d1fd6cb15859c4cfe81ade';
    public $FB_LOGIN_URL = '/';

	public function __construct()
    {
		parent::__construct();
        $this->load->database();
		$this->load->helper('url');
        $this->load->helper('date');
        $this->load->library('session');
        $this->load->library('FacebookSDK');
        $this->load->model('User_model');
        $this->load->model('User_fb_model');
	}

	public function index()
    {
        $fb = new Facebook\Facebook([
            'app_id' => $this->FB_APP_ID,
            'app_secret' => $this->FB_APP_SECRET,
            'default_graph_version' => 'v3.2',
        ]);
          
        $helper = $fb->getRedirectLoginHelper();
        
        $permissions = ['email']; // Optional permissions
        $loginUrl = $helper->getLoginUrl(base_url().'Oauth/Callback', $permissions);
        
        echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
    }

    public function callback()
    {
        $fb = new Facebook\Facebook([
            'app_id' => $this->FB_APP_ID,
            'app_secret' => $this->FB_APP_SECRET,
            'default_graph_version' => 'v3.2',
        ]);
          
        $helper = $fb->getRedirectLoginHelper();
        
        try {
            $accessToken = $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        
        if (!isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get('/me?fields=id,name,email', $accessToken->getValue());
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        
        $user_social = $response->getGraphUser();
        // Crear usuario si no existe la direccion de correo
        $user = $this->User_model->get(['email' => $user_social["email"]]);
        if (empty($user)) {
            $this->User_model->insert([
                'nombres' => $user_social["name"],
                'apellidos' => '',
                'email' => $user_social["email"],
                'clave' => $accessToken->getValue(),
            ]);
            $user = $this->User_model->get(['email' => $user_social["email"]]);
        }
        $user = $user[0];
        // Revisar tabla user_fb para conocer si ya se habia logueado anteriormente con Facebook
        $user_fb = $this->User_fb_model->get(['id_user' => $user->id]);
        if (empty($user_fb)) {
            // Si no ha ingresado anteriormente con Facebook se crea el registro
            $this->User_fb_model->insert([
                'id_user' => $user->id
            ]);
        }
        // Se permite el login

        $userdata = [
            'id' => $user->id,
            'nombre_completo' => strtoupper($user->nombres.' '.$user->apellidos),
            'email' => $user->email
        ];
        $this->session->set_userdata($userdata);

        redirect('/#');
    }
}
