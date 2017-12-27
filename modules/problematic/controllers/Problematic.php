<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Problematic extends MX_Controller {



	function __construct(){
		parent::__construct();

	} 

	public function index($type=null, $category=null){
		$data['title'] = 'Προβληματικά Προϊόντα';
		$data['errors'] = $this->problematic($type, $category);


			$this->load->view('templates/header',$data);
			$this->load->view('problematic', $data);
			$this->load->view('templates/footer',$data);
	}

	private function problematic($type=null,  $tables=null){

		if(!$type){
			return $this->problematicWeight($tables);
		}else if($type=='images'){
			
			return $this->noImages($tables);
		}
	}

	private function problematicWeight(){
		$this->load->model('problematic_model');

		return $this->problematic_model->weight();
	}

	private function noImages($tables=null){
		
		$this->load->model('problematic_model');

		return $this->problematic_model->noImages($tables);
	}


	public function same(){
		$data['title'] = 'Ίδιες Φωτογραφίες';
		$data['errors'] = $this->compare();


			$this->load->view('templates/header',$data);
			$this->load->view('same', $data);
			$this->load->view('templates/footer',$data);
	}


	private function compare(){

		$skus = Modules::run('crud/get','sku');
		$images = array();
		$count = 0;
		foreach ($skus->result() as $sku) {
			if($sku->category=='ip_pbx'){
				continue;
			}
			if(file_exists('images/'.$sku->id)){

				$dir = scandir('images/'.$sku->id);

				if(isset($dir[4])){
					$image1 = md5(file_get_contents('images/'.$sku->id.'/'.$dir[2]));
					$image2 = md5(file_get_contents('images/'.$sku->id.'/'.$dir[4]));

					if($image1 == $image2){
						$images[] = array('category'=>$sku->category, 'sku'=>$sku->id, 'dir'=>scandir('images/'.$sku->id));
						$count++;
					}
				}

				
			}

			if($count>19)
				break;
		}


		return $images;


	}

	//nomodel was created to find all products from WEstnet without characteristics
	/*public function nomodel(){
		$laptops = Modules::run('crud/get', 'live',array('category'=>'laptops'));
		foreach ($laptops->result() as $laptop) {
			$item = Modules::run('crud/get', 'laptops', array('product_number' => $laptop->product_number));

			if($item->row()->model==''){ 
				Modules::run('crud/update','live', array('product_number' => $laptop->product_number), array('status'=>'trash'));

				Modules::run('crud/update','laptops', array('product_number' => $laptop->product_number), array('new_item'=>1));
			} 

		}
	}*/
	
	//Change all shipping classes that are less then 2kg volumetric weight to 0.2kg = 10646 shipping class ID
	public function shippingchange(){
		$cats = Modules::run('categories/fullCategoriesArray');
		foreach ($cats as $cat) {
			$tmp= Modules::run('crud/get',$cat,array('volumetric_weight <' => 1));
			if($tmp ){
				foreach ($tmp->result() as $item) {

					Modules::run('crud/update', $cat, array('sku'=>$item->sku), array('shipping_class'=>10646));
				}
			}
		}
		
	}



}




?> 