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
	public function indexFb()
	{
		$this->load->view('home');
	}

	/**
	 * Request Page for this controller.
	 */
	public function index()
	{
		$this->load->view('request',['nombre_completo' => 'Usuario']);
	}
}
