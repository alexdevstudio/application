<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Front_page_products_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    public function get_FrontPageProducts()
    {
		$this->db->select('*');
        $this->db->from('front_page_products');

        $this->db->order_by('section', 'asc');

		$query = $this->db->get();
		
        return $query->result();
	}


}