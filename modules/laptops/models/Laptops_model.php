<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Laptops_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    public function insertItem($c,  $categoryData){

    	$this->db->insert($c, $categoryData);

    }

   

}