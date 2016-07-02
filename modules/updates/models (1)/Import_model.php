<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Model_import extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    private function xml($url){

    	$xml=simplexml_load_file($url) or die("Error: Cannot create object");

    	return $xml;

    }

    public function oktabit(){

    	
	if($xml = $this->xml("price.xml")){
			
			$images = array();
			

			$this->db->where('supplier','oktabit');
			$this->db->delete('live');

		}else{
			die("XML from Oktabit can not be loaded.");
		}



		foreach($xml->children() as $product) {

			//Rename categories for ETD.gr



			$cat = $product->category;


			switch ($cat) {
				case 'Mobile Computers':
					if (strpos($product->subcategory, 'Notebooks up to'))
					{
						$c = 'laptops';
					}
					break;
				
				default:
					$c = $cat;
					break;
			}




			$net_price = str_replace(",", ".", $product->timi);
			$recycle_tax = str_replace(",", ".", $product->anakykl);
			$pn = (string) trim($product->part_no);
			$pn = ($pn == '') ? (string) trim($product->code): $pn;


			//1. Live

			if($this->checkLiveProduct($pn, $net_price)){



			$live = array(
				'category'=>(string) trim($c) ,
				'product_number'=>$pn ,
				'net_price'=>(string) trim($net_price) ,
				'availability'=>(string) trim($product->availability) ,
				'recycle_tax'=>(string) trim($recycle_tax) ,
				'supplier' =>(string) 'oktabit'
				);

			

			/*if(in_array((string)$product->subcategory, $uniqueCategories )){
			//echo 'DEN UPARXEI----------';
			}else{
				echo '<strong>'.$product->category.':</strong> '.$product->subcategory.'<br>';
			}
$uniqueCategories[]=$product->subcategory;
*/
			

			$this->db->insert('live', $live);
			unset($live);

			}




			//2. New products for charateristics tables

				//Get or generate new SKU

			$skuArray = array(
				'category'=> $c,
				'product_number' => $pn
				);

				//if SKU is new then insert into category
			if($sku = $this->getSku($skuArray)){

				$category = array(

				'brand'=> (string) trim($product->brand),
				'sku'=> $sku,
				'product_number'=> $pn,
				'title'=> (string) trim($product->titlos),


				);

				$pnArray = array('product_number' => $pn);

				$this->insertProductCategory($c, $pnArray, $category);

			}


			


    		//3. Images
			
		}
		


		
    }

    private function getSku($data){

    	$query = $this->db->get_where('sku', $data);

    	if($query->num_rows()<1){

    		$this->db->insert('sku', $data);
    		return $this->db->insert_id();
    		
    	}

    	return false;
    }


    private function checkLiveProduct($pn, $price){



    	$query = $this->db->get_where('live', array('product_number' => $pn), 1, 0);

    	

    	if($query->num_rows()>0){

    		foreach ($query->result() as $row)
				{
						$price = (float) $price;
				        
				        if($row->net_price > $price){
				        	$this->db->where('id',$row->id);
				        	$this->db->delete('live');
				        	return true;
				        }else{
				        	return false;
				        }


				}

    	}else{
    		return true;
    	}


	
	}//private function checkLiveProduct($pn, $price){

	private function insertProductCategory($table, $pn, $data){

		echo $table.'<br />';

		if ($this->db->table_exists($table))
		{


	 
			$query = $this->db->get_where($table, $pn);

	    	if($query->num_rows()<1){

	    		$this->db->insert($table, $data);
	    		return true;
	    		
	    	}

	    	return false;

		} 	
		return false;
    }//private function insertProductCategory(){

  

}