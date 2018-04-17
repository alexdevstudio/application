<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Images extends MX_Controller {



	public function getImage($data)
	{

		$src = $data['src'];


		if($this->imageUrlExists($src)){
			$this->load->model('images_model');
			return $this->images_model->parseImage($data);


		}
		return false;




	}

	public function imageUrlExists($url){

	$content = @file_get_contents($url);
	if ($content === false) { return false; } else {return true;}

	}

	public function getsrc($sku){
			echo $this->getFirstImage($sku);
	}
	public function getFirstImage($sku, $html=false){

		$filename = './images/'.$sku;

			if ( ! $this->is_dir_empty($filename)) {
			   $files = scandir ($filename);
			   $firstFile =$files[2];// because [0] = "." [1] = ".."
			   $src = $filename.'/'.$firstFile;
			   $src = "https://etd.gr/xml".ltrim($src,".");

			   if(!$html)
			   	return $src;



			   $output ="<img  src='$src' />";
			   return $output;

			} else {
			    $output ="<img  src='".base_url()."assets/images/no-image.png' />";
			   return $output;
			}


	}

	function is_dir_empty($dir) {
		if (!is_readable($dir)) return NULL; 
		return (count(scandir($dir)) == 2);
	  }

	public function getExternalImagesFromUrl($url){
		$this->load->model('images_model');
		return $this->images_model->getExternalImagesFromUrl($url);

    }


		public function upload($category, $sku){
			if(empty($_FILES))
					echo 'No File Selected';


			if (!file_exists('images')) {
			    mkdir('images', 0777, true);
			}


			if (!file_exists('images/'.$sku)) {
			    mkdir('images/'.$sku, 0777, true);
			}

			$product = Modules::run('crud/get',$category, ['sku'=>$sku]);
			$rand = rand(1,10000000);
			$file_name = $product->row()->brand.'-'.$product->row()->product_number.'-'.$rand;
			$file_name = str_replace(' ','_',$file_name);
			$file_name = str_replace('.','_',$file_name);
			$config["upload_path"] = "./images/".$sku;
			$config["allowed_types"] = "jpg";

			//get random number

			$config['file_name'] = $file_name;
			$this->load->library('upload', $config);
			if( ! $this->upload->do_upload("file")){
				echo "failed to upload file(s)";
				exit;
			}
			$fileData = $this->upload->data();
			//Get extension

			 $imageData = ['item_sku'=>$sku,'image_src'=>$file_name];
			 $this->db->insert('images',$imageData);
			 echo 'success';
	}

	function default(){
		if(!$this->input->post())
		return;

		$sku = $this->input->post('sku');
		$image_id = $this->input->post('id');
		$this->load->model('images_model');
		if($this->images_model->default($sku, $image_id)){
			echo 'success';
		}else
			echo 'error';

	}

}

?>
