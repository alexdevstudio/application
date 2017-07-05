<?php

defined('BASEPATH') OR exit('No direct script access allowed');

 
class Skroutz_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    public function getItems(){
    	$where=array('active'=>1);
    	return Modules::run('crud/get', 'skroutz_urls',$where);
    }
 
    public function getBestPrice($sku){
			
		$skroutzPrice = Modules::run('crud/get','skroutz_prices', array('sku'=>$sku),array('id','DESC'),1);

		if($skroutzPrice){
			return $skroutzPrice;
		}else{
			return false;
		}

    }

     public function parsing(){

     	$items = $this->getItems();
		
		foreach ($items->result_array() as $keys => $values) {

			// If produc is not live then skip this loop
			if($this->notLive($values['sku']))
				continue;
			

			$id = $values['id'];
			$sku = $values['sku'];
			$url = $values['url'];

			
				// Create DOM from URL or file
				$html = file_get_html($url);

				// Find all images

				$shopData = array();
				$i = 1;

				foreach($html->find('.js-product-card') as $element){

				    
				   /* $shopData['shopLogo'] = $element->find('.js-lazy')->src;
				    $shopData['shopTitle'] = $element->find('.shop')->plaintext;
*/
				    if(!isset($element->find('img',0)->src) || !isset($element->find('a.product-link',0)->innertext) || !isset($element->find('.shop-details a[title]',0)->plaintext))
				    	continue;
				    $shopData['shopLogo'] = $element->find('img',0)->src;
				    $shopData['shopTitle'] = $element->find('.shop-details a[title]',0)->plaintext;
				    $shopData['shopPrice'] =  $this->skroutzPrizeSanitize ( $element->find('a.product-link',0)->innertext);

				    /* echo '<br />'.$shopData['shopTitle'].'=';
				    echo $element->find('.extra-cost',1)->plaintext;
				    
				    if(isset($element->find('.extra-cost em',1)->innertext) ){

				    	$extra_cost = $element->find('.extra-cost span',1)->innertext;

				    	if($extra_cost=='Αντικαταβολή'){
				   
				   			$shopData['antikatavoli'] =  $this->skroutzPrizeSanitize($element->find('.extra-cost em',0)->innertext);

				    	}else{
				   			$shopData['shipping'] =  $this->skroutzPrizeSanitize($element->find('.extra-cost em',0)->innertext);

				   			$shopData['antikatavoli'] = $this->skroutzPrizeSanitize($element->find('.extra-cost em',1)->innertext);
				    	}

					}else{

				   			$shopData['shipping'] =  0;
				   			$shopData['antikatavoli'] =  $this->skroutzPrizeSanitize($element->find('.extra-cost em',0)->innertext);
					}*/

					

				   // echo str_replace(',', '.', str_replace(' €', '', $element->find('.extra-cost em',1)->innertext));
				   //echo str_replace(',', '.', str_replace(' €', '', $element->find('a.product-link',0)->innertext));

					if($i==1){
						$shopData1st = $shopData;
					}

					$i++;
				}
				

				

			

			$data = array(
			'skroutz_urls_id'=> $id,
			'sku' => $sku,
			'best_price' => json_encode($shopData1st),
			'data' => json_encode($shopData),
			'last_update' => date("Y-m-d H:i:s")
			);


		Modules::run('crud/insert','skroutz_prices', $data);
			
		}// foreach ($items->result_array() as $keys => $values) {

     }
    
 private function skroutzPrizeSanitize($price){

		//Trim

		$price = trim($price);

		//Remove € sign

		$price = str_replace(' €', '', $price);

		//Remove +

		$price = str_replace('+ ', '', $price);

		//Remove dots separators from thousand

		$price = str_replace('.', '', $price);

		//Replace dot with comma

		$price = str_replace(',', '.', $price);

		return $price;

	} 

	private function notLive($sku){
		$pn = Modules::run('crud/get','sku',array('id'=>$sku))->row()->product_number;

		if(Modules::run('crud/get', 'live', array('product_number'=>$pn)))
			return;
		else
			return  true;
	}

	

}