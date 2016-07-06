<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Edit extends MX_Controller {

	public function index($category, $sku)
	{ 


		$item = Modules::run('crud/get',$category, array('sku'=>$sku));
		$update ='';
		if($post = $this->input->post()){

			if(isset($post['status'])){

				if($post['status']=='delete')
				{
					unset ($post['status']);
					$where = array('product_number'=>$post['product_number'], 'category'=>$post['category']);
					$exists = Modules::run('crud/get','live',$where);
					if($exists)
					{
						//$update = Modules::run('crud/delete','live',$where);
						$update = Modules::run('crud/update','live', $where, array('status'=>'trash'));
					}
					else
						echo "Το προϊόν δεν βρέθηκε στο STOCK.";
				}
				else if($post['status']=='add')
				{

					unset ($post['status']);
					$av = Modules::run("live/getAvailability",$post['availability'],'etd');

					if (!$av)
						$av = 'Αναμονή παραλαβής';

					$post['availability']=$av;
					$post['status']='publish';
					$post['supplier']='etd';
					$post['delete_flag']= 0;


					$where = array('product_number'=>$item->row()->product_number);

					//First check if item exists

					$exists = Modules::run("crud/get","live",$where);

					if($exists){
						$update = Modules::run('crud/update','live',$where,$post);
					}else{

						$update = Modules::run('crud/insert','live', $post);
						if($update){
							echo $post['status'];
						}
						
					}
				}
				else if($post['status']=='update')
				{
					unset ($post['status']);
					$where = array('sku'=>$sku);
					$update = Modules::run('crud/update',$category,$where,$post);
				}

				if($update){
					echo "<h2>Updated</h2>";
				}
			}
		}

		if($item){
			 	//Check if item is Live

			$data['itemLive'] = Modules::run('crud/get','live', array('product_number'=>$item->row()->product_number));
			$data['category'] = $category;
			$data['title'] = 'Επεξεργασία προϊόντος';
			$data['item'] = $item;

			$this->load->view('templates/header',$data);
			$this->load->view('edit', $data);
			$this->load->view('templates/footer',$data);
		}
		else
		{
			echo 'Error';
		}
	}
}

?>