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

	public function upload_logicom_xml()
	{
		$config['upload_path'] = './files/suppliers/';
		$config['allowed_types'] = 'xml';
		$config['max_size']	= '10000';
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('upload', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
			$path = './files/suppliers/'.$data['upload_data']['file_name'];
			$this->load->model('live_model');
			$this->live_model->import_logicom($path);
		}
	}
}




?>