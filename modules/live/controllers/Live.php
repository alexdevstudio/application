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
			case 'ddc':
				$this->live_model->ddc();
			break;
			case 'braintrust':
				$this->live_model->braintrust();
			break;
			
			default:
				die('Δεν υπάρχει ο προμηθευτής');
				break;
		}
	}

	public function updateLive($supplier){
		$this->load->model('live_model');
		$this->live_model->updateLive($supplier);
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

	public function upload_ddc_xml()
	{
		$config['upload_path'] = './files/suppliers/';
		$config['allowed_types'] = 'xml';
		$config['max_size']	= '10000';
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('upload_ddc_xml', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
			$path = './files/suppliers/'.$data['upload_data']['file_name'];
			$this->load->model('live_model');
			$this->live_model->import_ddc($path);
		}
	}

	public function upload_braintrust_xml()
	{
		$config['upload_path'] = './files/suppliers/';
		$config['allowed_types'] = 'xml';
		$config['max_size']	= '10000';
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('upload_braintrust_xml', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
			$path = './files/suppliers/'.$data['upload_data']['file_name'];
			$this->load->model('live_model');
			$this->live_model->import_braintrust($path);
		}
	}


	public function addProduct($a,$b,$c,$d){
		$this->load->model('live_model');
		if($this->live_model->addProduct($a,$b,$c,$d)){
			return true;
		}

	}
	public function checkLiveProduct($a,$b){
		$this->load->model('live_model');
		return $this->live_model->checkLiveProduct($a,$b);

	}

	public function getAvailability($availability, $supplier){
		$this->load->model('live_model');
		return $this->live_model->makeAvailability($availability, $supplier);
	}
}




?>