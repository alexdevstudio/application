<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Images_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    public function parseImage($data){

    	$this->load->library('image_lib');

    	$src = $data['src'];
		$sku = $data['sku'];
		$brand = $data['brand'];
		$pn = $data['part_number'];
		$tail = $data['tail'];

		if (!file_exists('images')) {
		    mkdir('images', 0777, true);
		}


		if (!file_exists('images/'.$sku)) {
		    mkdir('images/'.$sku, 0777, true);
		}

			$newFileName = $brand.'_'.$pn.''.$tail;
			
			$newFileName = $brand.'_'.$pn.''.$tail;
			
		    $target_path = './images/'.$sku.'/'.$newFileName.'.jpg';
			
			

		   /* $config_manip = array(
		        'image_library' => 'ImageMagick',
		        'source_image' => $target_path,
		        'new_image' => $target_path,
		        'maintain_ratio' => TRUE,
		        'create_thumb' => TRUE,
		        'thumb_marker' => '',
		        'allowed_types' => 'jpg|jpeg|gif|png',
		        'width' => 600,
		        'height' => 600
		    );*/
		    
		   // $this->image_lib->initialize($config_manip);

		    if (copy($src, $target_path)) {

		    	echo $target_path.' : '.$src.'<br/>';
		       
		    
		    // clear //
		  //  $this->image_lib->clear();

		    $data = array(
		    	'item_sku' => $sku,
		    	'image_src' => $newFileName
		    	);

		    $this->db->insert('images', $data);

		   

		    return true;

		    }
		    
		     return false;
		  

    }

   

}