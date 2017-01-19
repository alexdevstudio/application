<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Edit extends MX_Controller {

	public function index($category, $sku)
	{ 


		$item = Modules::run('crud/get',$category, array('sku'=>$sku));
		$skroutzUrl = Modules::run('crud/get','skroutz_urls', array('sku'=>$sku));
		


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

					if($post['sale_price']==''){
						$post['sale_price'] = NULL;
					}

					if($post['price_tax']==''){
						$post['price_tax'] = NULL;
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



					if($exists){
						$update = Modules::run('crud/update','live',$where,$post);
					}else{

						$update = Modules::run('crud/insert','live', $post);

					}
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
					
					if($vweight!='' && ($category=='monitors' || $category=='desktops')){
						

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

				

					$update = Modules::run('crud/update',$category,$where,$post);
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

			$data['itemLive'] = Modules::run('crud/get','live', array('product_number'=>$item->row()->product_number));
			$data['category'] = $category;
			$data['title'] = 'Επεξεργασία προϊόντος';
			$data['item'] = $item;
			$data['skroutzUrl'] = $skroutzUrl;


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