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

	



}




?> 