<?php
class IMC_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function get($conditions){
        return $this->db
            ->select('iu.estatura,iu.peso,iu.imc_calculado,cl.tipo,cl.clasificacion,iu.fecha_registro')
            ->from('imc_user as iu')
            ->where($conditions)
            ->join('clasificacion_imc as cl','cl.id = iu.id_clasificacion')
            ->order_by('iu.id','desc')
            ->get()->result();
    }

    public function insert($data){
        $this->db->insert('imc_user', array(
            'id_user' => $data['id_user'],
            'estatura' => $data['estatura'],
            'peso' => $data['peso'],
            'imc_calculado' => $data['imc_calculado'],
            'id_clasificacion' => $data['id_clasificacion'],
            'fecha_registro' => date('Y-m-d H:i:s',now())
        ));
    }
    
    public function list(){
        return $this->db
            ->select('*')
            ->from('imc_user')
            ->order_by('id')
            ->get()
            ->result();
    }
}