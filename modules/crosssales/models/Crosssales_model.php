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

    	$bags = Modules::run('crud/get', 'live', array('category'=>'carrying_cases','status'=>'publish'));

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

    		

            
    		//b. Brand

        		if($brand == $row->brand){
        			$same_brand[] =  $bag_sku;
        		}else{
        			$dif_brand[] =  $bag_sku;
        		}

        		

        		//c. Size
                $size = explode('.', $size);
        		$size = $size[0];

                if ($size != '')
                {
                    if (strpos($row->size, $size) !== false) {
                        $same_size[] = $bag_sku;
                    }/*else{
                        $dif_size[] = $bag_sku;
                    }*/
                }

        	}
            
            $total_bags = array($instock_bags, $outofstock_bags);
            $total_brands = array($same_brand, $dif_brand);
            
            $perfect_match = array_intersect($instock_bags, $same_brand, $same_size);
            $good_match = array_intersect($same_size, $same_brand);
            $normal_match = $same_size;

            


            if(!empty($perfect_match)){
                shuffle($perfect_match);
                foreach ($perfect_match as $value) {
                    $cross_bag = $value;
                    break;
                }
            }elseif(!empty($good_match)){
                
                shuffle($good_match);
                foreach ($good_match as $value) {
                    $cross_bag = $value;
                    break;
                }
            }elseif(!empty($normal_match)){
                shuffle($normal_match);
                foreach ($normal_match as $value) {
                    $cross_bag = $value;
                    break;
                }
            }else{
                $cross_bag = '';
            }
            
            //result bags
            $cross_items[] = $cross_bag;

            //2. Pick up External HDD Based on Availability & Brand

            $instock_hdds = array();
            $outofstock_hdds = array();
            $same_brand = array();
            $dif_brand = array();
            

            $hdds = Modules::run('crud/get', 'live', array('category'=>'external_hard_drives','status'=>'publish'));

            $hdds= $hdds->result_array();

            foreach ($hdds as $hdd) {

                $pn = $hdd['product_number'];
                
                $the_hdd = Modules::run('crud/get', 'external_hard_drives', array('product_number'=>$pn));
                
                if(!$the_hdd)
                    continue;
                
                $row = $the_hdd->row();
                $hdd_sku = $row->sku;

                if($hdd['availability']=='Άμεσα Διαθέσιμο'){
                    $instock_hdds[] = $hdd_sku;
                }else{
                    $outofstock_hdds[] = $hdd_sku;

                }

            
            //b. Brand

                if($brand == $row->brand){
                    $same_brand[] =  $hdd_sku;
                }else{
                    $dif_brand[] =  $hdd_sku;
                }

                
            }

            $total_hdds = array($instock_hdds, $outofstock_hdds);
            $total_brands = array($same_brand, $dif_brand);

            $perfect_match = array_intersect($instock_hdds, $same_brand);
            $good_match = $instock_hdds;
            $normal_match = $same_brand;
            $random_match = $outofstock_hdds;

            if(!empty($perfect_match)){
                shuffle($perfect_match);
                foreach ($perfect_match as $value) {
                    $cross_hdd = $value;
                    break;
                }
            }elseif(!empty($good_match)){
                
                shuffle($good_match);
                foreach ($good_match as $value) {
                    $cross_hdd = $value;
                    break;
                }
            }elseif(!empty($normal_match)){
                
                shuffle($normal_match);
                foreach ($normal_match as $value) {
                    $cross_hdd = $value;
                    break;
                }
            }elseif(!empty($random_match)){
                
                shuffle($random_match);
                foreach ($random_match as $value) {
                    $cross_hdd = $value;
                    break;
                }
            }else{
                $cross_hdd = '';
            }

            $cross_items[]=$cross_hdd;

            //3. Pick up Mouse Based on Availability & Wireless

            $instock_mise = array();
            $outofstock_mise = array();
            $same_brand = array();
            $dif_brand = array();
            

            $mise = Modules::run('crud/get', 'live', array('category'=>'keyboard_mouse','status'=>'publish'));

            $mise = $mise->result_array();

            foreach ($mise as $mouse) {

                $pn = $mouse['product_number'];
                
                $where = array(
                    'type'=>'Mouse',
                    'usage'=>'Mobile',
                    'connection'=>'wireless',
                    'product_number'=>$pn
                    );
                $the_mouse = Modules::run('crud/get', 'keyboard_mouse', $where);
                
                if(!$the_mouse)
                    continue;
               
                $row = $the_mouse->row();
                $mouse_sku = $row->sku;


                if($mouse['availability']=='Άμεσα Διαθέσιμο'){
                    $instock_mise[] = $mouse_sku;
                }else{
                    $outofstock_mise[] = $mouse_sku;

                }

            
            //b. Brand

                if($brand == $row->brand){
                    $same_brand[] =  $mouse_sku;
                }else{
                    $dif_brand[] =  $mouse_sku;
                }

                
            }

            $total_mise = array($instock_mise, $outofstock_mise);
            $total_brands = array($same_brand, $dif_brand);

            $perfect_match = $instock_mise;
            $good_match = $outofstock_mise;
           
            if(!empty($perfect_match)){
                shuffle($perfect_match);
                foreach ($perfect_match as $value) {
                    $cross_mouse = $value;
                    break;
                }
            }elseif(!empty($good_match)){
                
                shuffle($good_match);
                foreach ($good_match as $value) {
                    $cross_mouse = $value;
                    break;
                }
            }else{
                $cross_mouse = '';
            }

            $cross_items[]=$cross_mouse;

            //4. Pick up AntiVirus Based on Availability & Brand

            $instock_antivs = array();
            $outofstock_antivs = array();
            $same_brand = array();
            $dif_brand = array();
            
            $where  = array(
                'category'=>'software',
                    'status'=>'publish'
                    );

            $antivs = Modules::run('crud/get', 'live', $where);

            $antivs= $antivs->result_array();
                $a=0;
            foreach ($antivs as $antiv) {

                $pn = $antiv['product_number'];

                $where  = array(
                    'product_number'=>$pn
                    );
                $the_antiv = Modules::run('crud/get', 'software', $where);
                
                $row = $the_antiv->row();
                $antiv_sku = $row->sku;

                if($row->brand != 'INTEL'){
                    continue;
                }                

                if($antiv['availability']=='Άμεσα Διαθέσιμο'){
                    $instock_antivs[] = $antiv_sku;
                }else{
                    $outofstock_antivs[] = $antiv_sku;

                }
            
            //b. Brand

                if($brand == $row->brand){
                    $same_brand[] =  $antiv_sku;
                }else{
                    $dif_brand[] =  $antiv_sku;
                }
                
            }

            $total_antivs = array($instock_antivs, $outofstock_antivs);
            $total_brands = array($same_brand, $dif_brand);

            $perfect_match = $instock_antivs;
            $good_match = $outofstock_antivs;
            

            if(!empty($perfect_match)){
                shuffle($perfect_match);
                foreach ($perfect_match as $value) {
                    $cross_antiv = $value;
                    break;
                }
            }elseif(!empty($good_match)){
                
                shuffle($good_match);
                foreach ($good_match as $value) {
                    $cross_antiv = $value;
                    break;
                }
            }else{
                $cross_antiv = '';
            }

            $cross_items[]=$cross_antiv;

            $cross_items = implode(',', $cross_items);
            return $cross_items;
           
    }

    function manual_inserted($sku){
    	//create a table for manual insertion of Cross Sales
    	return false;
    }
    
}