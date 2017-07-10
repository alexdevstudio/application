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

				if(isset($dir[3])){
					$image1 = md5(file_get_contents('images/'.$sku->id.'/'.$dir[2]));
					$image2 = md5(file_get_contents('images/'.$sku->id.'/'.$dir[3]));

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
	



}




?> 