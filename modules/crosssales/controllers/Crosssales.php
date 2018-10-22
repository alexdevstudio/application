<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crosssales extends MX_Controller {



	 public function index()
 	{
 		if($this->input->post()){
 				$this->cross_validation();
 		}

 		$this->load->model('crosssales_model');

		$data['tables'] = Modules::run('categories/fullCategoriesArray');
		$data['title'] = 'Παράλληλα προϊόντα';
 		$data['filters'] = Modules::run('crud/get', 'cross_sells_similar');
 		$this->load->view('templates/header', $data);
 		$this->load->view('index');
 		$this->load->view('templates/footer');
 	}

	public function auto_laptop($sku, $brand, $size, $price){

    	$this->load->model('crosssales_model');

    	$result = $this->crosssales_model->auto_laptop($sku, $brand, $size, $price);
    	return $result;
  }

	private function cross_validation(){

		$this->form_validation->set_rules('category', 'Κατηηγορία', 'required');
		$this->form_validation->set_rules('filter', 'Τμήμα τίτλου', 'required');
		$this->form_validation->set_rules('skus', 'SKU προϊόντων', 'required');

		if ($this->form_validation->run())
			{
					$_POST['skus'] = str_replace(" ", "", $_POST['skus']);
					$this->db->insert('cross_sells_similar', $_POST);
					$this->pushCrosssales($_POST);
					$FlashData['Message']= '<strong>Το φίλτρο προστέθηκε.</strong>';
					$FlashData['type'] = 'success';
			}

			if( ! isset($FlashData))	{
					$FlashData['Message']= validation_errors('<p class=""> - ', '</p>');
					$FlashData['type'] = 'danger';
				}

				$this->session->set_flashdata('flash_message', $FlashData);
				return;
	}

	function delete($id){
		$this->db->where('id', $id);
		$this->db->delete('cross_sells_similar');
		if($this->db->affected_rows() > 0){
			$FlashData['Message']= '<strong>Το φίλτρο προστέθηκε.</strong>';
			$FlashData['type'] = 'success';
		}else{
			$FlashData['Message']= '<strong>Το φίλτρο δεν βρέθηκε.</strong>';
			$FlashData['type'] = 'danger';
		}

		$this->session->set_flashdata('flash_message', $FlashData);
		redirect('crosssales');
	}


	function pushCrosssales($arr){
		$wpdb = $this->load->database('wordpress', TRUE);
		$wpdb->select('wp_posts.*, post_meta.*, cross.*');
		$wpdb->like('wp_posts.post_title', $arr['filter'], 'both');
		$wpdb->where('wp_posts.post_type', 'product');
		$wpdb->where('post_meta.meta_key', '_sku');
		$wpdb->join('wp_postmeta as post_meta', 'wp_posts.ID = post_meta.post_id');
		$wpdb->join('wp_postmeta as cross', 'wp_posts.ID = cross.post_id');

		echo "<pre>";
		 print_r($wpdb->get('wp_posts')->result());
		echo "</pre>";
		exit();

	}



}




?>
