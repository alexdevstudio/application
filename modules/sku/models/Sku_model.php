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

            $data1 = array('sku' => $this->db->insert_id(), 'new'=>true);
           return $data1;
            
        }else{
            $sku = $query->row()->id;
             $data1 = array('sku' => $sku, 'new'=>false);
            return $data1;

        }


    }



  

}