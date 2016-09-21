<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Profit_rates_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    public function getRates($category){

    	$query = $this->db->get_where('profit', $category);

        $cat_rate = $query->row()->rate;
        return $cat_rate;

    }
}