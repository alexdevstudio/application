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
	

	public function index($s)//$s stands for supplier
	{	
			
	}

	public function productImages($sku){

		$images = Modules::run('crud/get', 'images', array('item_sku'=>$sku));
		if($images){
			$result	= "<div id='product-large-images-light-box' class='product-large-images'>";
			foreach ($images->result() as $image) {
				$result .= '
    <div>
	    <a href="http://etd.gr/xml/images/'.$sku.'/'.$image->image_src.'.jpg">
	    	<img data-lazy="http://etd.gr/xml/images/'.$sku.'/'.$image->image_src.'.jpg" data-id="">
	    </a>
	</div>';
			}

			$result .='</div>';
			$result .= '<div class="product-large-images-thumbs">
	<div>
    	<img data-lazy="http://etd.gr/xml/images/'.$sku.'/'.$image->image_src.'.jpg">
	</div>
		<div><img data-lazy="http://etd.gr/xml/images/'.$sku.'/'.$image->image_src.'.jpg"/></div>
	</div>';

		}else{
			
			 $result = '<img src="https://etd.gr/wp-content/plugins/woocommerce/assets/images/placeholder.png" >';
		
		}

		echo $result;
	}





}




?> 