<?php
class Clasificacion_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function getByClasificacion($imc_calculado){
        return $this->db
            ->select('*')
            ->from('clasificacion_imc')
            ->where('valor_min <=',$imc_calculado)
            ->where('valor_max >=',$imc_calculado)
            ->get()->row();
    }
}