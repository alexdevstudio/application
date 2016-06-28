<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Insert extends MX_Controller {

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
		$data['title'] = 'Εισαγωγή Προϊόντος';
		$this->load->view('templates/header' ,$data);
		$this->load->view('insert');
		$this->load->view('templates/footer');
	}

	public function doInsert(){

		if(!$_POST['cat']){
			echo 'no post';

		}else{

			$cat = trim(strip_tags($_POST['cat']));
			$product_number = trim(strip_tags($_POST['product_number']));
			$title = trim(strip_tags($_POST['title']));
			$brand = trim(strip_tags($_POST['brand']));
			$description = trim(strip_tags($_POST['description']));

			$data = array(
				'product_number'=> $product_number,
				'title' => $title,
				'brand' => $brand,
				'description' => $description
				);

			echo 'ok';
		}

	}





}




?> 