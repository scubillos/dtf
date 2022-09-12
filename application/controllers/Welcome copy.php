<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public $FB_APP_ID = '402076080424611';
    public $FB_APP_SECRET = '92518fb951d1fd6cb15859c4cfe81ade';
	public $FB_LOGIN_URL = '/';
	
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
        $this->load->library('FacebookSDK');
        $this->load->library('session');
	}

	/**
	 * Index Page for this controller.
	 */
	public function index()
	{
		if ($this->session->has_userdata('id')) {
			$this->load->view('imc_home',['nombre_completo' => $this->session->userdata('nombre_completo')]);
		} else {			
			$fb = new Facebook\Facebook([
				'app_id' => $this->FB_APP_ID,
				'app_secret' => $this->FB_APP_SECRET,
				'default_graph_version' => 'v3.2',
			]);
			  
			$helper = $fb->getRedirectLoginHelper();
			$permissions = ['email']; // Optional permissions
			$facebookUrl = $helper->getLoginUrl(base_url().'Oauth/Callback', $permissions);

			$this->load->view('home',['facebookUrl' => $facebookUrl]);
		}
	}
}
