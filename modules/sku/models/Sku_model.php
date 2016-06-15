<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Sku_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    public function checkSku($data){

    	$query = $this->db->get_where('sku', $data);

        if($query->num_rows()<1){

            $this->db->insert('sku', $data);
            return $this->db->insert_id();
            
        }

        return false;

    }



  

}