<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Skroutz extends MX_Controller {



	function __construct(){
		parent::__construct();
		$this->load->model('skroutz_model');
	} 



	public function index($disabled=null){

		$data['disabled'] = $disabled;	
		$data['title'] = 'Skroutz Monitor';

			$this->load->view('templates/header',$data);
			$this->load->view('skroutz', $data);
			$this->load->view('templates/footer',$data);
	}

	public function getBestPrice($sku){

		return $this->skroutz_model->getBestPrice($sku);
	}

	public function parsing(){	
			
		 $this->skroutz_model->parsing();

	
	}

	public function toggleSkroutzUrl($url,$sku){

		if($url==''){

			Modules::run('crud/delete','skroutz_urls',array('sku'=>$sku));

		}else{

			if($item = Modules::run('crud/get','skroutz_urls',array('sku'=>$sku))){

				Modules::run('crud/update','skroutz_urls',array('sku'=>$sku), array('url'=>$url));
			}else{
				
				$data= array(
					'url'=>$url,
					'sku'=>$sku,
					'last_update'=>date("Y-m-d H:i:s"),
					'added_to_db'=>date("Y-m-d H:i:s")
					);
				Modules::run('crud/insert','skroutz_urls', $data);

			}
		}
	}





}




?> 