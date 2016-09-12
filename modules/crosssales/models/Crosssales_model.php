<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Crosssales_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    //Logic
    //1. Check if the specific SKU has already Cross Sale Products added manually
    //2. If not then Check the general rule by category
    

    public function auto_laptop($sku, $brand, $size, $price){
    	
    	if($this->manual_inserted($sku)){
    		return false;
    	}

    	//Get 4 products: Bag, External HDD, Mouse, AntiVirus

    	//1. Pick up a bag based on: Availability, Size, Brand, Price.

        $instock_bags = array();
        $outofstock_bags = array();
        $same_brand = array();
        $dif_brand = array();
        $same_size = array();

    	$bags = Modules::run('crud/get', 'live', array('category'=>'carrying_cases'));

    	$bags = $bags->result_array();

        

    	foreach ($bags as $bag) {



    		$pn = $bag['product_number'];

    		$the_bag = Modules::run('crud/get', 'carrying_cases', array('product_number'=>$bag['product_number']));
    		$row = $the_bag->row();
    		$bag_sku = $row->sku;


    		//a. Availabilty
    		if($bag['availability']=='Άμεσα Διαθέσιμο'){
    			$instock_bags[] = $bag_sku;
			}else{
				$outofstock_bags[] = $bag_sku;

    		}

    		$total_bags = array($instock_bags, $outofstock_bags);

            
    		//b. Brand

    		if($brand == $row->brand){
    			$same_brand[] =  $bag_sku;
    		}else{
    			$dif_brand[] =  $bag_sku;
    		}

    		$total_brands = array($same_brand, $dif_brand);

    		//c. Size
            $size = explode('.', $size);
    		$size = $size[0];

    		if (strpos($row->size, $size) !== false) {
			    $same_size[] = $bag_sku;
			}else{
                $dif_size[] = $bag_sku;
            }



			$perfect_match = array_intersect($instock_bags, $same_brand, $same_size);
			$good_match = array_intersect($total_bags, $same_brand);
			$normal_match = $same_size;



			if(!empty($perfect_match)){
				foreach (array_rand($perfect_match) as $value) {
					$cross_bag = $value;
					break;
				}
			}elseif(!empty($good_match)){
				foreach (array_rand($good_match) as $value) {
					$cross_bag = $value;
					break;
				}
			}elseif(!empty($normal_match)){
				foreach (array_rand($normal_match) as $value) {
					$cross_bag = $value;
					break;
				}
			}else{
				$cross_bag = '';
			}

    	}

        if(isset($same_size[0])){
            return $same_size[0];
        }
        return false;
    	
    }

    function manual_inserted($sku){
    	//create a table for manual insertion of Cross Sales
    	return false;
    }
    
}