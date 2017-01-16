<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Skroutz extends MX_Controller {

	

	public function index()//$s stands for supplier
	{	
			
		$this->load->model('skroutz_model');
		$items = $this->skroutz_model->getItems();
		foreach ($items->result_array() as $keys => $values) {

			foreach ($values as $key => $value) {
				
				if($key=='url'){
				// Create DOM from URL or file
				$html = file_get_html($value);

				// Find all images

				$shopData = array();
echo "<pre>";
				foreach($html->find('.js-product-card') as $element){
				    
				   /* $shopData['shopLogo'] = $element->find('.js-lazy')->src;
				    $shopData['shopTitle'] = $element->find('.shop')->plaintext;
*/
				    echo $element->find('img[src]',0);
				    echo $element->find('.shop-details a[title]',0)->plaintext;
				    echo str_replace(',', '.', str_replace(' €', '', $element->find('a.product-link',0)->innertext));
				    
				    if($extra_cost = $element->find('.extra-cost span',1)->innertext){

				    	if($extra_cost=='Αντικαταβολή'){
				   
				   			$antikatavoli =  str_replace(',', '.', str_replace(' €', '', $element->find('.extra-cost em',0)->innertext));
				   			$shopData['antikatavoli'] =  str_replace('+ ', '', $antikatavoli);

				    	}else{
				    		$shipping =  str_replace(',', '.', str_replace(' €', '', $element->find('.extra-cost em',0)->innertext));
				   			$shopData['shipping'] =  str_replace('+ ', '', $shipping);

				   			$antikatavoli =  str_replace(',', '.', str_replace(' €', '', $element->find('.extra-cost em',1)->innertext));
				   			$shopData['antikatavoli'] =  str_replace('+ ', '', $antikatavoli);
				    	}

					}

					print_r($shopData);

				   // echo str_replace(',', '.', str_replace(' €', '', $element->find('.extra-cost em',1)->innertext));
				   //echo str_replace(',', '.', str_replace(' €', '', $element->find('a.product-link',0)->innertext));

				    

				    exit();

				}
				

				//print_r($shopData);
				
				}

			}
			
		}
		
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