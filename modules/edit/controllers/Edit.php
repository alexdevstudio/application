<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Edit extends MX_Controller {

	public function index($category, $sku)
	{ 


		$item = Modules::run('crud/get',$category, array('sku'=>$sku));
		$skroutzUrl = Modules::run('crud/get','skroutz_urls', array('sku'=>$sku));
		$cross_sells = Modules::run('crud/get','cross_sells', array('sku'=>$sku));
		$images = Modules::run('crud/get','images', array('item_sku'=>$sku));
		


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

				if($post['status']=='deleteAllImages'){

					Modules::run('crud/delete', 'images', array('item_sku'=>$sku));
					$dirPath = 'images/'.$sku;
					if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
				        $dirPath .= '/';
				    }
				    $files = glob($dirPath . '*', GLOB_MARK);
				    foreach ($files as $file) {
				        if (is_dir($file)) {
				            self::deleteDir($file);
				        } else {
				            unlink($file);
				        }
				    }
				    rmdir($dirPath);
					 

				}else if($post['status']=='images')
				{
		

					if($post['imageUrl']!=''){
						$imageArray = Modules::run('images/getExternalImagesFromUrl', $post['imageUrl']);
							
							/*print_r($imageArray);
							exit();*/

						if(!empty($imageArray)){
							/*print_r($item->row());
							echo '<br>/';
							print_r($imageArray);*/
							$imageItem['brand'] = $item->row()->brand;
							$imageItem['product_number'] = $item->row()->product_number;
							
							Modules::run('live/AddProductImages', $imageItem	, $imageArray, 'etd', $sku);
							$FlashData['Message']= '<strong>Οι Φωτογραφίες ενημερώθηκαν</strong>';
                            $FlashData['type'] = 'success';
						}else{
					    	$FlashData['Message']= '<strong>Ελέγξτε το URL</strong>';
                            $FlashData['type'] = 'danger';
						}

					}else{
						$FlashData['Message']= '<strong>Kάτι πήγε λάθος!</strong> Το URL δεν μπορεί να είναι άδειο.';
                        $FlashData['type'] = 'danger';

					}

					if(isset($FlashData)){
	                    $this->session->set_flashdata('flash_message', $FlashData);
					}


				}else if($post['status']=='delete')
				{
					$post['status'] = 'trash';
					$post['delete_flag'] = 100;
					/*$post['price_tax'] = NULL;
					$post['sale_price'] = NULL;*/
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

					//Remove from etd_prices table
					$exists_in_etd_prices = Modules::run('crud/get','etd_prices',array('sku'=>$sku));
					if ($exists_in_etd_prices)
					{
						Modules::run('crud/delete','etd_prices',array('sku'=>$sku));	
					}

					//Remove from Installments Table
					Modules::run('crud/delete','installments',array('sku'=>$sku));

					//For auto update the WP with update_wp
					Modules::run('extract/allImport',$category,'one',0,$sku);

					//Disable active url from skroutz_irls table to avoid further price update
					
					$this->skroutzParsingDeactivate($sku);

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
					
					if($installments_count!='0' && $installments_count != ''){
						Modules::run('crud/insert','installments',array('sku'=>$sku,'installments_count'=>$installments_count));
					}

					
					unset($post['installments']);


					$where_in_etd_prices = array('sku'=>$sku);
					$exist_in_etd_prices = Modules::run("crud/get","etd_prices",$where_in_etd_prices);
					
					if($exist_in_etd_prices)
					{
						if(($post['price_tax'] == 0 || $post['price_tax'] == '') && ($post['sale_price'] == 0 || $post['sale_price'] == '') && ($post['shipping'] == ''))
						{
							Modules::run('crud/delete','etd_prices', $where_in_etd_prices);
						}
						else
						{
							Modules::run('crud/update','etd_prices', $where_in_etd_prices,array('price_tax'=>$post['price_tax'], 'sale_price'=>$post['sale_price'], 'shipping'=>$post['shipping'], 'date_last_edited'=>date('Y-m-d H:i:s')));
						}
					}
					else if($post['price_tax'] != '' || $post['shipping'] != '' || $post['sale_price'] != '')
					{
						Modules::run('crud/insert',
							'etd_prices',
							 array('sku'=>$sku,
							 	'price_tax'=>$post['price_tax'],
							 	 'sale_price'=>$post['sale_price'],
							 	  'shipping'=>$post['shipping'],
							 	   'date_last_edited'=>date('Y-m-d H:i:s')));
					}
					

					unset ($post['price_tax']);
					unset ($post['shipping']);
					unset ($post['sale_price']);


					if($exists){
						$update = Modules::run('crud/update','live',$where,$post);
						//updateWp($product, $table);
					}else{

						$update = Modules::run('crud/insert','live', $post);

					}

					//For auto update the WP with update_wp
					Modules::run('extract/allImport',$category,'one',0,$sku);

					//Toggle skroutz parsing if the URL was set from before
					if($post['supplier']=='etd'){
						$this->skroutzParsingDeactivate($sku, true);
					}else{
						$this->skroutzParsingDeactivate($sku);
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
					
					if($vweight!='' /*&& ($category=='monitors' || $category=='desktops' || $category=='tv')*/){
						

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

					$this->createHtmlDescription($sku,$category, $post);
					
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

						
				}else if($post['status']=='total_removal'){
					unset($post['status']);
					$DeletionMessage ='Το προϊόν της κατηγορίας:'.$post['category'].' με p.n.:'.$post['product_number'].' Διαγράφηκε από τους πίνακες ';

					$where = array('product_number'=>$post['product_number']);
					$Deletion_from_category = Modules::run('crud/delete',$post['category'], $where); //delete from category table
					$where = array('product_number'=>$post['product_number'], 'category'=>$post['category']);
					$Deletion_from_sku = Modules::run('crud/delete','sku', $where); //delete from sku table
					$Deletion_from_live = Modules::run('crud/delete','live', $where); //delete from live table

					$where = array('sku'=>$post['sku']);
					$Deletion_from_installments = Modules::run('crud/delete','installments', $where); //delete from installments table
					$Deletion_from_etd_prices = Modules::run('crud/delete','etd_prices', $where); //delete from etd_prices table

					if($Deletion_from_category != false)
						$DeletionMessage .=$post['category'];

					if($Deletion_from_sku != false)
						$DeletionMessage .=' Sku';

					if($Deletion_from_live != false)
						$DeletionMessage .=' Live';

					if($Deletion_from_installments != false)
						$DeletionMessage .=' Installments';

					if($Deletion_from_etd_prices != false)
						$DeletionMessage .=' Etd_prices';

					echo "<h2>ΔΙΑΓΡΑΦΗΚΕ ΤΟ ΠΡΟΙΟΝ</h2>";
					unset($post);
					header("Refresh:0");


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
			$data['etd_prices'] = Modules::run('crud/get','etd_prices', array('sku'=>$sku));
			$data['itemLive'] = Modules::run('crud/get','live', array('product_number'=>$item->row()->product_number));
			$data['category'] = $category;
			$data['title'] = 'Επεξεργασία προϊόντος';
			$data['item'] = $item;
			$data['skroutzUrl'] = $skroutzUrl;
			$data['cross_sells'] = $cross_sells;
			$data['images'] = $images;


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
	private function createHtmlDescription($sku,$category,$post){
					$auto_description = '';
					foreach ($post as $key => $value) {
						
						//1. if SKU specific description available
						$where = array('sku'=>$sku,'category'=>$category,'char'=>$key, 'char_spec'=>$value);
						$res = Modules::run('crud/get', 'char_blocks_specific', $where);

						if(!$res){

							//2. if BRAND specific description available
							$where = array('brand'=>$post['brand'],'category'=>$category,'char'=>$key, 'char_spec'=>$value);
							$res = Modules::run('crud/get', 'char_blocks_specific', $where);

							if(!$res){
								//3. if basic  description available

							$where = array('category'=>$category,'char'=>$key, 'char_spec'=>$value);
							$res = Modules::run('crud/get', 'char_blocks_basic', $where);



							}


						}
						if(!$res)
							continue;
						/*	echo $key.":<br />";
						print_r($res->result());
						exit();*/
						if($res){

							$text = $res->row()->description;
							preg_match_all("/\[([^\]]*)\]/", $text, $matches);
							if(!empty($matches[1])){
								$chars = $this->getChar($sku, $category);
								/*echo "<pre>";
								print_r( $matches[1]);
								exit();*/
								foreach ($matches[1] as $value) {
									
									$text = preg_replace('/\['.$value.']/', $chars->row()->$value, $text);

								}
								
							}

							$text_color = $res->row()->text_color;
							if(($res->row()->background_color == '#fff' || $res->row()->background_color == '#ffffff') && ($res->row()->text_color == '#000' || $res->row()->text_color == '#000000')){
								$text_color = '#444';
							}
							$auto_description .= "<div class='row custom_description_block' style='background:".$res->row()->background_color."'>
									<div class='col-sm-6 col-xs-12 custom_description_block_text' style='margin-top: 4%;' >
										<h3 class='custom_description_block_title' style='text-align:center;color:".$res->row()->text_color."'>".$res->row()->title."</h3>
										<p class='custom_description_block_descr' style='text-align:center;color:".$text_color."'>".$text."</p>
									</div>
									<div class='col-sm-6 col-xs-12 custom_description_block_img' style=''>
									<img src='https://etd.gr/xml/images/descriptions/".$res->row()->image."' /></div>
							</div>";
							/*<div class='col-sm-6 col-xs-12 custom_description_block_img' style='background-position: center center;min-height:350px;background-image:url(".base_url()."images/descriptions/".$res->row()->image.")'></div>*/
						}
					}
					if($auto_description!=''){
						$data = array(
						        'sku' => $sku,
						        'html'  => $auto_description,
						        'edited' => date('Y-m-d H:i:s')
						        );

						$this->db->replace('descriptions_html', $data);
					} else{
						echo 'nothing to show';
					}
	}

	private function getChar($sku, $category){
		return Modules::run('crud/get',$category,array('sku'=>$sku));
	}

	public function deleteImg($sku, $src){



			Modules::run('crud/delete','images',array('item_sku'=>$sku, 'image_src'=>$src));
			$path = 'images/'.$sku.'/'.$src.'.jpg';
			unlink($path);
		    exit ('ok'); 
		
	}

	public function skroutzParsingDeactivate($sku, $activate=null){
		if(!$activate){
			Modules::run('crud/update', 'skroutz_urls', array('sku'=>$sku), array('active'=>0));
		}else{
			Modules::run('crud/update', 'skroutz_urls', array('sku'=>$sku), array('active'=>1));
		}
		return true;

	}

}

?>