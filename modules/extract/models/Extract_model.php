<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Extract_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    public function xml($table){

    		
    		$xml = new DomDocument("1.0","ISO-8859-7");

    		$products = $xml->createElement('products');
    		$products = $xml->appendChild($products);

            //$this->db->where("new_item", "1");
    		$query = $this->db->query("SELECT * FROM $table WHERE new_item=1 ");
            $i=0;
            $prod = $query->result_array();
            foreach($prod as $columns){

    			
    			$product = $xml->createElement('product');
    			$product = $products->appendChild($product);

    			foreach($columns as $key => $value){
    				if($key!='id' && $key!='description' ){
    				$item = $xml->createElement($key, htmlentities($value));
    				$item = $product->appendChild($item);	
					}
    			}


    		}

            //print_r($query->result_array());

			$xml->FormatOutput = true;
			$string_value = $xml->saveXML(); 

			if (!file_exists('files')) {
		    mkdir('files', 0777, true);
			}

            $file = "./files/".$table.".xml";

            if (file_exists($file)) { unlink ($file); }

			if($xml->save($file)){
				return true;
			}
				return false;


		}

    }