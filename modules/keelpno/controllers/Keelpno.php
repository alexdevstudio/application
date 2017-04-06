<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Keelpno extends MX_Controller {



	function __construct(){
		parent::__construct();

	} 

	public function index(){

		$data['title'] = 'ΔΤΕ';
		


			$this->load->view('keelpno', $data);
	}

	public function problematicWeight(){
		$this->load->model('problematic_model');
		return $this->problematic_model->weight();
	}

	



}




?> 