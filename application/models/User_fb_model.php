<?php
class User_fb_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function get($conditions){
        return $this->db
            ->get_where('user_fb',$conditions)
            ->result();
    }

    public function insert($data){
        $this->db->insert('user_fb', array(
            'id_user' => $data['id_user'],
            'fecha_registro' => date('Y-m-d H:i:s',now())
        ));
    }

    public function update($data){
        $this->db
            ->where('id', $data['id'])
            ->update('user', array(
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'email' => $data['email'],
                'clave' => $data['clave']
            ));
    }
    
    public function list(){
        return $this->db
            ->select('*')
            ->from('user')
            ->order_by('id')
            ->get()
            ->result();
    }
}