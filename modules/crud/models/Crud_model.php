<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Crud_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


  public function update($table, $where, $data){
		
		$this->db->where($where);
		$this->db->set($data);
		return $this->db->update($table);

  }

  public function delete($table, $where){
        
        $this->db->where($where);
        return $this->db->delete($table);
  }

  public function get($category, $where){

    	$this->db->where($where);
        $item = $this->db->get($category);

        if($item->num_rows()<1){
        	return false;
        }
        else
        {
        	return $item;
        }

    	


    }

}