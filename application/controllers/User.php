<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
    {
		parent::__construct();
		$this->load->helper('url');
        $this->load->library('session');
	}

	public function logout()
    {
        $status = 200;
        $this->session->sess_destroy();
		$this->output->set_status_header($status);
		redirect(base_url());
    }
}
