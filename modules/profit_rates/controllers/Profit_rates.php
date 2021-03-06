<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profit_rates extends MX_Controller {

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

		$rates_table = $this->getRatesTable();

		$data['title'] = 'Ποσοστά Κέρδους ανα κατηγορία';

		$data['rates_table'] = $rates_table;

		$this->load->view('templates/header',$data);
		$this->load->view('profit_rates', $data);
		$this->load->view('templates/footer',$data);
	}
/*
	public function getRates($category)
	{	

		$this->load->model('profit_rates_model');
		return $this->profit_rates_model->getRates($category);
		
		
	}
*/
	private function getRatesTable()
	{
		$profit_table = Modules::run("crud/get",'profit', '',array('category','ASC'));

		return $profit_table;
	}

	public function getCategoryRate($category)
	{
		$where = array('category'=>$category);

		$profit_category = Modules::run("crud/get",'profit',$where);
		if($profit_category){
			return $profit_category->row()->rate;
		}
		return false;
	}

	public function updateRate()
	{
		$rate = trim($_POST['rate']);
		if($rate=='0' || $rate==''){
			$rate='';
		}else{
			$rate = $rate/100;
		}


		$category = $_POST['category'];
		$parent = Modules::run('crud/get', 'profit', array('category'=>$category));

		if($parent->row()->parent == ''){ //if does not have parent
			if($rate==''){
				$rate = '0.06';
			}
		}

		
		$data=array(
			'category'=>$category,
			'rate'=>$rate
			);

		$where = array('category'=>$category);
		
		if(Modules::run("crud/update",'profit',$where, $data))
		{
			$response = array(
				'result'=>'success',
				'rate'=>$rate
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