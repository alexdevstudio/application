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
    
    public function get_with_images()
    {
        // SELECT featured_products.*,
        //     (SELECT  images.image_src
        //     FROM images
        //     WHERE images.item_sku = featured_products.sku
        //     ORDER BY images.default DESC
        //     LIMIT 1) as image
        // FROM featured_products
        $this->db->select('featured_products.*');
        $this->db->select('(SELECT  images.image_src FROM images WHERE images.item_sku = featured_products.sku ORDER BY images.default DESC LIMIT 1) as image', FALSE);
        $this->db->from('featured_products');

		$query = $this->db->get();
		
        return $query->result();
    }
}