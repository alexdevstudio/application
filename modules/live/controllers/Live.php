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
			case 'aci':
				$this->live_model->aci();
				break;
			case 'copiers':
				$this->live_model->copiers();
				break;
			case 'cpi':
				$this->live_model->cpi();
				break;
			case 'westnet':
				$this->live_model->westnet();
				break;
			case 'partnernet':
				$this->live_model->partnernet();
				break;
			case 'quest':
				$this->live_model->quest();
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

	public function upload_aci_xml()
	{
		$config['upload_path'] = './files/suppliers/';
		$config['allowed_types'] = 'xml';
		$config['max_size']	= '10000';
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('upload_aci_xml', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
			$path = './files/suppliers/'.$data['upload_data']['file_name'];
			$this->load->model('live_model');
			$this->live_model->import_aci($path);
		}
	}

	public function upload_copiers_xml()
	{
		$config['upload_path'] = './files/suppliers/';
		$config['allowed_types'] = 'xml';
		$config['max_size']	= '10000';
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('upload_copiers', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
			$path = './files/suppliers/'.$data['upload_data']['file_name'];
			$this->load->model('live_model');
			$this->live_model->import_copiers($path);
		}
	}

	public function upload_cpi_xml()
	{
		$config['upload_path'] = './files/suppliers/';
		$config['allowed_types'] = 'xml';
		$config['max_size']	= '10000';
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('upload_cpi_xml', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
			$path = './files/suppliers/'.$data['upload_data']['file_name'];
			$this->load->model('live_model');
			$this->live_model->import_cpi($path);
		}
	}

	public function upload_westnet_xml()
	{
		$config['upload_path'] = './files/suppliers/';
		$config['allowed_types'] = 'xml';
		$config['max_size']	= '10000';
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('upload_westnet_xml', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
			$path = './files/suppliers/'.$data['upload_data']['file_name'];
			$this->load->model('live_model');
			$this->live_model->import_westnet($path);
		}
	}

	public function upload_quest_xml()
	{
		$config['upload_path'] = './files/suppliers/';
		$config['allowed_types'] = 'xml';
		$config['max_size']	= '10000';
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('upload_quest_xml', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
			$path = './files/suppliers/'.$data['upload_data']['file_name'];
			$this->load->model('live_model');
			$this->live_model->import_quest($path);
		}
	}


	public function addProduct($a,$b,$c=null,$d){
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

	public function updateAvPraxis($availability,$sku){
/*http://etd.gr/xml/live/updateAvPraxis/insert/"sku"
http://etd.gr/xml/live/updateAvPraxis/delete/"sku"*/
	}

	 public function AddProductImages($product, $f, $supplier='etd', $sku){
	 	$this->load->model('live_model');
	 	$this->live_model->AddProductImages($product, $f, $supplier, $sku);
	 }
}

?>