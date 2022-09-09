<?php
class User_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function get($conditions){
        return $this->db
            ->get_where('user',$conditions)
            ->result();
    }

    public function insert($data){
        $this->db->insert('user', array(
            'nombres' => $data['nombres'],
            'apellidos' => $data['apellidos'],
            'email' => $data['email'],
            'clave' => $data['clave'],
            'fecha_registro' => date('Y-m-d H:i:s',now())
        ));
    }
}