<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Edit extends MX_Controller {

	public function index($category, $sku)
	{ 


		$item = Modules::run('crud/get',$category, array('sku'=>$sku));
		$skroutzUrl = Modules::run('crud/get','skroutz_urls', array('sku'=>$sku));
		$cross_sells = Modules::run('crud/get','cross_sells', array('sku'=>$sku));
		


		if(!$skroutzUrl){
			$skroutzUrl = '';
		}else{
			$skroutzUrl = $skroutzUrl->row()->url;
		}

		$installments = Modules::run('crud/get','installments', array('sku'=>$sku));
		if(!$installments){
				$installments='';
		}else{
			$installments = $installments->row()->installments_count;
		}

		
		$update ='';
		
		if($post = $this->input->post()){

			if(isset($post['status'])){

				if($post['status']=='delete')
				{
					$post['status'] = 'trash';
					$post['delete_flag'] = 100;
					$post['price_tax'] = NULL;
					$post['sale_price'] = NULL;
					$post['availability'] = 0;
					$where = array('product_number'=>$post['product_number'], 'category'=>$post['category']);
					$exists = Modules::run('crud/get','live',$where);
					if($exists)
					{
						//$update = Modules::run('crud/delete','live',$where);
						$update = Modules::run('crud/update','live', $where, $post);
					}
					else
						echo "Το προϊόν δεν βρέθηκε στο STOCK.";


					Modules::run('crud/delete','installments',array('sku'=>$sku));
				}
				else if($post['status']=='add')
				{
					unset ($post['status']);

					$av = Modules::run("live/getAvailability",$post['availability'],'edit');

					if (!$av)
						$av = 'Αναμονή παραλαβής';

					$post['availability']=$av;
					$post['status']='publish';
					$post['delete_flag']= 0;

					if($post['upcoming_date']=='' || $av!='Αναμονή παραλαβής'){
						unset($post['upcoming_date']);
					}else{
						$post['upcoming_date'] = date("Y-m-d",strtotime($post['upcoming_date']));
					}

					if($post['sale_price']==''){
						$post['sale_price'] = NULL;
					}

					if($post['price_tax']==''){
						$post['price_tax'] = NULL;
					}

					if($post['shipping']==''){
						$post['shipping'] = NULL;
					}

					$where = array('product_number'=>$item->row()->product_number);

					//First check if item exists

					$exists = Modules::run("crud/get","live",$where);

					$installments_q = Modules::run('crud/delete','installments',array('sku'=>$sku));
					$installments_count = $post['installments'];
					
					if($installments_count=='' || $installments_count=='0'){
						$installments_count = 12;
					}

					Modules::run('crud/insert','installments',array('sku'=>$sku,'installments_count'=>$installments_count));
					unset($post['installments']);

					$where_in_etd_prices = array('sku'=>$sku);
					$exist_in_etd_prices = Modules::run("crud/get","etd_prices",$where_in_etd_prices);
					
					if($exist_in_etd_prices)
					{
						if($post['price_tax'] == 0 || $post['price_tax'] == NULL )
						{
							$update_etd_prices = Modules::run('crud/delete','etd_prices',$where_in_etd_prices);
						}
						else
						{
							$update_etd_prices = Modules::run('crud/update','etd_prices',$where_in_etd_prices,array('price_tax'=>$post['price_tax'],'date_last_edited'=>date('Y-m-d H:i:s')));
						}
					}
					else if($post['price_tax'] != 0 && $post['price_tax'] != NULL )
					{
						$update_etd_prices = Modules::run('crud/insert','etd_prices',array('sku'=>$sku,'price_tax'=>$post['price_tax'],'date_last_edited'=>date('Y-m-d H:i:s')));
					}

					unset ($post['price_tax']);


					if($exists){
						$update = Modules::run('crud/update','live',$where,$post);
						//updateWp($product, $table);
					}else{

						$update = Modules::run('crud/insert','live', $post);

					}

					//For auto update the WP with update_wp
					Modules::run('extract/allImport',$category,'one',$sku);

					unset($post);
					header("Refresh:0");
				}
				else if($post['status']=='update')
				{
					unset ($post['status']);

					$skroutz_url = $post['skroutz_url'];
					unset ($post['skroutz_url']);

					//Insert or delete Skroutz URL from DB;
					Modules::run('skroutz/toggleSkroutzUrl', $skroutz_url, $sku);
					
					$where = array('sku'=>$sku);

					$vweight = trim($post['volumetric_weight']);
					
					if($vweight!='' && ($category=='monitors' || $category=='desktops' || $category=='tv')){
						

						$post['shipping_class'] = Modules::run('categories/shippingByWeight', $vweight);

					}else{

							if($category!='printers' && $category != 'multifunction_printers'){
								
							


								if($post['shipping_class']==''){
									$post['shipping_class'] = Modules::run('categories/makeShippingClass',$post,$category);
								}



								if($post['shipping_class']!='' && $post['volumetric_weight']==''){
									$post['volumetric_weight'] = Modules::run('categories/getWeight',$post['shipping_class']);
								}


							}else{
								
								if($post['volumetric_weight']==''){
									$post['volumetric_weight'] = Modules::run('categories/volumeWeight', $post['dimensions']);
								}

								$post['shipping_class'] = Modules::run('categories/makeShippingClass',$post,$category);

									
							}

					}

				}else if($post['status']=='related'){
					unset($post['status']);
					$products = str_replace(" ", "", $post['cross_sells_products']);

					if(($cross_sells && $cross_sells->row()->products!=$products) || $products==''){
						$where=array('sku'=>$sku);
						Modules::run('crud/delete','cross_sells', $where);
						
					}
					
					if($products!='' && (!$cross_sells || $cross_sells->row()->products!=$products)){
						
						$data = array(
							'sku'=>$sku,
							'products' => $products
						);

					    Modules::run('crud/insert','cross_sells', $data);

					}

					$update = true;

						
				}

				if($update){
					echo "<h2>Updated</h2>";
					unset($post);
					header("Refresh:0");
				}
			}


		}

		if($item){
			 	//Check if item is Live
			$data['price_tax'] = Modules::run('crud/get','etd_prices', array('sku'=>$sku));
			$data['itemLive'] = Modules::run('crud/get','live', array('product_number'=>$item->row()->product_number));
			$data['category'] = $category;
			$data['title'] = 'Επεξεργασία προϊόντος';
			$data['item'] = $item;
			$data['skroutzUrl'] = $skroutzUrl;
			$data['cross_sells'] = $cross_sells;


			$skroutzPrice = Modules::run('skroutz/getBestPrice',$sku);

			if($skroutzPrice){
				$skroutzPrice = $skroutzPrice->result_array();
				$data['skroutzPrice'] = $skroutzPrice[0];
			}else{
				$data['skroutzPrice'] = false;
			}

			$data['installments'] = $installments;

			$this->load->view('templates/header',$data);
			$this->load->view('edit', $data);
			$this->load->view('templates/footer',$data);
		}
		else
		{
			echo 'Error';
		}
	}


}

?>