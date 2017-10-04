<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Antikatavoles extends MX_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	

	public function index()//$s stands for supplier
	{	
		$data['title'] = "Κατηγορίες που εξαιρούνται από τη δωρεάν αντικαταβολή";
		$data['description'] = "Κατηγορίες που εξαιρούνται από τη δωρεάν αντικαταβολή";

		$data['categories'] = Modules::run('categories/fullCategoriesArray');





		$this->load->view('templates/header', $data );
		$this->load->view('antikatavoles');
		$this->load->view('templates/footer');
	}


	public function exclude(){

		$category_name = $this->input->post('category_name');
		Modules::run('crud/insert','charge_antikatavoli_cats', ['category_name' => $category_name]);
		$msg = "Η κατηγορία <strong>".strtoupper($category_name)."</strong> πλέον πάντα θα έχει χρέωση στην αντικαταβολή εκτός αν υπάρχει άλλο προϊόν με δωρεάν αντικαταβολή";
		$type = 'danger';
		$this->flash($msg, $type);
		redirect('antikatavoles','refresh');
	}

	public function include(){

		$category_name = $this->input->post('category_name');
		Modules::run('crud/delete','charge_antikatavoli_cats', ['category_name' => $category_name]);
		$msg = "Η κατηγορία <strong>".strtoupper($category_name)."</strong> θα μπορεί να έχει δωρεάν αντικταβολή αν τηρεί τις υπόλοιπες προϋποθέσεις";
		$type = 'success';
		$this->flash($msg, $type);
		redirect('antikatavoles','refresh');
	}

	private function flash($msg, $type){
		$FlashData['Message']= $msg;
                        $FlashData['type'] = $type;
		
		$this->session->set_flashdata('flash_message', $FlashData);
		
	}

}




?> 