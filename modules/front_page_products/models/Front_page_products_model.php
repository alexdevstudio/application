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

        $this->db->order_by('category', 'asc');

		$query = $this->db->get();
		
        return $query->result();
    }
    
    public function get_with_images()
    {
        // SELECT front_page_products.*,
        //     (SELECT  images.image_src
        //     FROM images
        //     WHERE images.item_sku = front_page_products.sku
        //     ORDER BY images.default DESC
        //     LIMIT 1) as image
        // FROM front_page_products
        $this->db->select('front_page_products.*');
        $this->db->select('(SELECT  images.image_src FROM images WHERE images.item_sku = front_page_products.sku ORDER BY images.default DESC LIMIT 1) as image', FALSE);
        $this->db->order_by('category', 'desc');
        $this->db->from('front_page_products');

		$query = $this->db->get();
		
        return $query->result();
    }
}