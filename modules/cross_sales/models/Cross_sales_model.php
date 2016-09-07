<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Cross_sales_import extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    //Logic
    //1. Check if the specific SKU has already Cross Sale Products added manually
    //2. If not then Check the general rule by category
    

    function auto_laptop($sku, $brand, $size, $price){
    	
    	if($this->manual_inserted($sku)){
    		return false;
    	}

    	//Get 4 products: Bag, External HDD, Mouse, AntiVirus

    	//1. Pick up a bag based on: Availability, Size, Brand, Price.



    	$bags = Module::run('crud/get', 'live', array('category'=>'carrying_cases'));

    	$bags = $bags->result_array();
    	$found = false;
    	foreach ($bags as $bag) {
    		if($bag['availability']=='Άμεσα Διαθέσιμο'){
    			$instock_bags[] = $bag['product_number'];
			}else{
				$outofstock_bags[] = $bag['product_number'];
    		}

    		$total_bags = array($instock_bags, $outofstock_bags);

    		

    	}

    }

    function manual_inserted($sku){
    	//create a table for manual insertion of Cross Sales
    	return false;
    }
    
}