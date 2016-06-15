<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Live extends MX_Controller {

	

	public function index($s=null)//$s stands for supplier
	{	
		if($s){
			$this->load->model('live_model');
		}

		switch ($s) {
			case 'oktabit':
				$this->live_model->oktabit();
			break;
			case 'logicom':
				$this->live_model->logicom();
			break;
			
			default:
				die('Δεν υπάρχει ο προμηθευτής');
				break;
		}
	}
}




?>