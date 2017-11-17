<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rest extends MX_Controller {

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

	}

	public function featuredImage($sku, $sec_key=null){

		if(!$sec_key)
			die('dead end');

			$images = Modules::run('crud/get', 'images', array('item_sku'=>$sku,'default'=>1), array('id', 'ASC'), 1);
			if(!$images)
			$images = Modules::run('crud/get', 'images', array('item_sku'=>$sku), array('id', 'ASC'), 1);
		/*foreach ($images->result() as $image) {
			$name = $images->image_src;
		}*/


		if($images){
			$name = $images->row()->image_src.'.jpg';
			$name = '<img  src="https://etd.gr/xml/images/'.$sku.'/'.$name.'" >';
		}else{
			$name = '<img src="https://etd.gr/wp-content/plugins/woocommerce/assets/images/placeholder.png" >';
		}

		echo $name;
	}

	public function productImages($sku, $sec_key=null){
		if(!$sec_key)
			die('dead end');
		/*if(! $this->input->is_ajax_request()) {
		    redirect('https://etd.gr');
		}*/
		//echo $_SESSION['myKey'];
		$images = Modules::run('crud/get', 'images', array('item_sku'=>$sku),  array('default', 'DESC'));
		if($images){
			$result	= "<div id='product-large-images-light-box' class='product-large-images'>";
			$thumbs = '';
			foreach ($images->result() as $image) {
				$result .= '
						    <div>
							    <a href="https://etd.gr/xml/images/'.$sku.'/'.$image->image_src.'.jpg">
							    	<img data-lazy="https://etd.gr/xml/images/'.$sku.'/'.$image->image_src.'.jpg" data-id="">
							    </a>
							</div>';

				$thumbs .= '<div>
						    	<img data-lazy="https://etd.gr/xml/images/'.$sku.'/'.$image->image_src.'.jpg">
							</div>';
			}

			$result .='</div>';
			$result .= '<div class="product-large-images-thumbs">'.$thumbs.'</div>';

		}else{

			 $result = '<img src="https://etd.gr/wp-content/plugins/woocommerce/assets/images/placeholder.png" >';

		}

		echo  $result;
	}





}




?>
