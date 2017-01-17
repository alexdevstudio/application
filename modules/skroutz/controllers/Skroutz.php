<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Skroutz extends MX_Controller {

	

	public function index()//$s stands for supplier
	{	
			
		$this->load->model('skroutz_model');
		$items = $this->skroutz_model->getItems();
		foreach ($items->result_array() as $keys => $values) {

			

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
			'id'=> $id,
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

	public function toggleSkroutzUrl($url,$sku){

		if($url==''){

			Modules::run('crud/delete','skroutz_urls',array('sku'=>$sku));

		}else{

			if($item = Modules::run('crud/get','skroutz_urls',array('sku'=>$sku))){

				Modules::run('crud/update','skroutz_urls',array('sku'=>$sku), array('url'=>$url));
			}else{
				
				$data= array(
					'url'=>$url,
					'sku'=>$sku,
					'last_update'=>date("Y-m-d H+2:i:s"),
					'added_to_db'=>date("Y-m-d H+2:i:s")
					);
				Modules::run('crud/insert','skroutz_urls', $data);

			}
		}
	}





}




?> 