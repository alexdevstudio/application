<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Front_page_products extends MX_Controller {

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
	

	public function index()
	{	
		$this->load->model('Front_page_products_model');

		$data['title'] = 'Προϊόντα στην Αρχική Σελίδα';
		$data['Front_page_products'] = $this->Front_page_products_model->get_FrontPageProducts();

		$this->load->view('templates/header',$data);
		$this->load->view('front_page_products');
		$this->load->view('templates/footer');
	}

	public function delete($sku)
	{	
		Modules::run('crud/delete', 'front_page_products', array('sku'=>$sku));

		redirect( base_url('/front_page_products/'), 'auto');
		
	}

	public function insert($product)
	{
		$where = array('meta_value'=>$product['sku'],"meta_key"=>"_sku");
		$product['$woo_id'] = Modules::run("crud/getWp","wp_postmeta", $where);

		if ($product['$woo_id'] !== false)
		{
			Modules::run('crud/insert', 'front_page_products', $product);
			return true;
		}
		else
			return false;
	}

}
?>