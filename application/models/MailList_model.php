<?php
class MailList_model extends CI_Model{
    
    public function __construct(){
        $this->load->database();
    }
    
    public function getmaillist(){
        $query = $this->db->get('mailing_list');
        return $query->result_array();
    }
    public function insert($row){
        $this->db->insert('mailing_list',$row);
        return $this->db->insert_id();
    }
}


?>