<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Imc_ajax extends CI_Controller {

    private $request;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->load->library('session');
        $this->load->model('IMC_model');
        $this->load->model('Clasificacion_model');
        $this->request = json_decode(file_get_contents('php://input'));
    }

    public function index()
    {
        $data = [];
        $status = 200;
        $imc = $this->IMC_model->get(['id_user' => $this->session->userdata('id')]);
        $data = $imc;
        $this->output->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['data' => $data], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function calcular()
    {
        $msg = [];
        $status = 200;
        $id_user = $this->session->userdata('id');
        $peso = (FLOAT)$this->request->peso;
        $estatura = (FLOAT)$this->request->estatura;

        $estatura_mt = $estatura/100;
        $imc_calculado = $peso/pow($estatura_mt,2);
        $imc_calculado = round($imc_calculado,2);
        
        $clasificacion = $this->Clasificacion_model->getByClasificacion($imc_calculado);
        if (!is_null($clasificacion)) {

            $data = [
                'id_user' => $id_user,
                'peso' => $peso,
                'estatura' => $estatura,
                'imc_calculado' => $imc_calculado,
                'id_clasificacion' => $clasificacion->id
            ];
            $this->IMC_model->insert($data);
            $msg[] = 'Registro guardado exitosamente';
        } else {
            $status = 400;
            $msg[] = 'No se logró realizar el calculo, por favor revise los datos e intente nuevamente';
        }
        
        $this->output->set_status_header($status)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode(['msg' => $msg], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
