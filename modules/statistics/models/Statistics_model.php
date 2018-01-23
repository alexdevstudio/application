<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics_model extends CI_Model {
 
    public function __construct()
    {
    	parent::__construct();
    }

    public function getSupplierProducts($supplier)
    {
        $this->db->distinct();
        $this->db->select('category');
        $this->db->from('live');

        $this->db->where('status', 'publish');
        $this->db->where('supplier', $supplier);

        $this->db->order_by('live.category', 'asc');

        $query = $this->db->get();
        $all_categories = $query->result();

        $category_products = array();
        $Total_products = 0;

        foreach ($all_categories as $object) {

            $this->db->select('live.*');
            $this->db->select($object->category.'.*');

            $this->db->from($object->category);

            $this->db->where('live.status', 'publish');
            $this->db->where('live.supplier', $supplier);

            $this->db->join('live', 'live.product_number = '.$object->category.'.product_number', 'left');

            $this->db->order_by($object->category.'.brand', 'asc');
            $this->db->order_by($object->category.'.title', 'asc');

            $query = $this->db->get();
            
            $category_products [$object->category.' ('.$query->num_rows().')'] = $query->result();
            $Total_products += $query->num_rows();
        }

        $category_products['TotalProducts'] = $Total_products;

        return $category_products;

    }
}