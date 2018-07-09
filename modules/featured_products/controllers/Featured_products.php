<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Featured_products extends MX_Controller {

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
		$this->load->model('Featured_products_model');

		$data['title'] = 'Προϊόντα σε Προσφορά';
		$data['Featured_products'] = $this->Featured_products_model->get_FeaturedProducts();

		$this->load->view('templates/header',$data);
		$this->load->view('featured_products');
		$this->load->view('templates/footer');
	}

	public function get()
	{	
		$products = Modules::run('crud/get', 'featured_products');

		if($products)
		{
			echo json_encode($products->result());
			return true;
		}
		echo json_encode(array());
		return false;
		
	}

	public function delete($sku, $view_page = false)
	{	
		Modules::run('crud/delete', 'featured_products', array('sku'=>$sku));

		if($view_page)
			redirect( base_url('/featured_products/'), 'auto');
		
	}

	public function insert($sku, $pn, $category)
	{
		$where = array('meta_value'=>$sku,"meta_key"=>"_sku");
		$woo_id = Modules::run("crud/getWp","wp_postmeta", $where);

		if($woo_id){
			$woo_id = $woo_id->result();
			$woo_id = $woo_id[0]->post_id;

			$product ['sku'] = $sku;
			$product ['product_number'] = $pn;
			$product ['woo_id'] = $woo_id;

			$product ['section'] = $category;
			if($category != 'laptops' && $category != 'desktops' && $category != 'servers' && $category != 'monitors' && $category != 'printers-mfp')
				$product ['section'] = 'other';

			Modules::run('crud/insert', 'featured_products', $product);
				echo 'true';

			return true;
		}

		echo 'false';
		return false;
	}

}
?>