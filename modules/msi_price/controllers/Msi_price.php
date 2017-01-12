<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Msi_price extends MX_Controller {

	
	

	public function index()//$s stands for supplier
	{	

		//Insert from laptops Tables to Msi price table

		$allLaptops = $this->getMsi();
		$this->insertNewLaptops($allLaptops);

		//Get all laptops from Msi_price table
		$laptops = $this->getMsiPrice();

		$data['title'] = 'MSI Laptops - Προτεινόμενες Τιμές';

		$data['laptops'] = $laptops;

		$this->load->view('templates/header',$data);
		$this->load->view('msi_price', $data);
		$this->load->view('templates/footer',$data);
	}


	private function getMsi()
	{

		$msi = Modules::run("crud/get",'laptops',array('brand'=>'MSI'));

		return $msi;
	}


	private function insertNewLaptops($allLaptops)
	{

		$this->load->model('msi_price_model');
		$this->msi_price_model->insertNewLaptops($allLaptops);
		return true;
	}


	private function getMsiPrice()
	{

		$table = 'laptops';
		$join_table = 'msi_price';
		$join = 'laptops.sku = msi_price.sku';
		$msi = Modules::run("crud/join",$table, $join_table, $join);

		return $msi;
	}

	public function updatePrice()
	{

		$sku = $_POST['sku'];
		
		$price = $_POST['price'];

		

		if(trim($price)=='')
			$price = '0.00';

		$data=array(
			'sku'=>$sku,
			'price'=>$price
			);

		$where = array('sku'=>$sku);
		
		if(Modules::run("crud/update",'msi_price',$where, $data))
		{
			$response = array(
				'result'=>'success',
				'price'=>$price
				);

			
		}else{
			$response = array(
				'result'=>'false'
				);

		}

		echo json_encode($response);

	}


}




?> 