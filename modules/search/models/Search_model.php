<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Search_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    public function getItemFromSku($q){

    	
		$q = trim(strip_tags($q));

		$query = $this->db->query("SELECT * FROM sku WHERE id='{$q}' OR product_number='{$q}' LIMIT 1");

        $product = false;

		if($query->num_rows()<1){
			return false;
		}else{

            $sku = $query->row()->id;
            $category = $query->row()->category;
            
            $product = array();

            $product['category']=$category;
            $product['sku']=$sku;

            
            $item = $this->getItemByCategory($product);

            $product['item']=$item;

		}

        return $product;

    }




    public function getItemByCategory($productArray){

        $table = $productArray['category'];
        $sku = $productArray['sku'];

        $this->db->where('sku', $sku);
        $item = $this->db->get($table);

        if($item->num_rows()<1){

            return false;

        }else{

            return $item;

        }



    }


    


   

}