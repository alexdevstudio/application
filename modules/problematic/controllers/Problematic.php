<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Problematic extends MX_Controller {



	function __construct(){
		parent::__construct();

	} 

	public function index(){

		$data['title'] = 'Προβληματικά Προϊόντα';
		$data['errors'] = $this->problematicWeight();


			$this->load->view('templates/header',$data);
			$this->load->view('problematic', $data);
			$this->load->view('templates/footer',$data);
	}

	public function problematicWeight(){
		$this->load->model('problematic_model');
		return $this->problematic_model->weight();
	}

	



}




?> 