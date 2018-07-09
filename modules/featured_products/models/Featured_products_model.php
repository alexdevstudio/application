<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Featured_products_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    public function get_FeaturedProducts()
    {
		$this->db->select('*');
        $this->db->from('featured_products');

        $this->db->order_by('id', 'asc');

		$query = $this->db->get();
		
        return $query->result();
	}


}