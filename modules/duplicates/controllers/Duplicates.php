<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Duplicates extends MX_Controller {


	public function index()
	{
		if($post = $this->input->post()){
				$this->add();
		}
		$this->load->model('duplicates_model');

		$data['title'] = 'Διπλά Προϊόντα';
		$data['duplicates'] = $this->duplicates_model->getDuplicates();
		$this->load->view('templates/header', $data);
		$this->load->view('index');
		$this->load->view('templates/footer');
	}

	private function add(){

		$post = $this->input->post();

		if( ! Modules::run('crud/get', 'duplicates', $post) )
	  {
			$this->load->model('duplicates_model');
			return $this->duplicates_model->insert($post);
		}

		return false;

	}

	public function delete($sku_in, $sku_out){
		$this->load->model('duplicates_model');
		$this->duplicates_model->delete($sku_in, $sku_out);

		//Also need to delete sku_out from live to allow the update
		redirect('duplicates');
	}

	public function check(){
		$this->load->model('duplicates_model');
		$this->duplicates_model->checkDuplicates();
	}





}




?>
