<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crosssales extends MX_Controller {



	 public function index()
 	{
 		if($this->input->post()){
 				$this->cross_validation();
				redirect('crosssales');
 		}

 		$this->load->model('crosssales_model');

		$data['tables'] = Modules::run('categories/getCategories');
		$data['title'] = 'Παράλληλα προϊόντα';
 		$data['filters'] = Modules::run('crud/join', 'cross_sells_similar', 'categories', 'cross_sells_similar.category = categories.woo_category_id');
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
					$_POST['skus'] = str_replace(".", ",", $_POST['skus']);
					$skuArr = explode(',',$_POST['skus']);
					$skuArr2 = [];
					foreach ($skuArr as $sku) {
						$skuArr2[] = trim($sku);
					}
					$_POST['skus'] = implode(',',$skuArr2);
					$_POST['created_at'] = date("Y-m-d H:i:s");
					$this->db->insert('cross_sells_similar', $_POST);
					$insert_id = $this->db->insert_id();
					$affected_products = $this->pushCrosssales($_POST);

					$this->db->where('cross_sells_similar_id', $insert_id);
					$this->db->update('cross_sells_similar', ['affected_products' => $affected_products]);

					$FlashData['Message']= 'Το φίλτρο προστέθηκε. <strong>'.$affected_products.' '.($affected_products > 1 ? 'Προϊόντα ενημερώθηκαν' : 'Προϊόν ενημερώθηκε').'  στο site.</strong>';
					$FlashData['type'] = 'success';
			}

			if( ! isset($FlashData))	{
					$FlashData['Message']= validation_errors('<p class=""> - ', '</p>');
					$FlashData['type'] = 'danger';
				}

				$this->session->set_flashdata('flash_message', $FlashData);
				return true;
	}

	function delete($id){
		$this->db->where('cross_sells_similar_id', $id);
		$this->db->delete('cross_sells_similar');
		if($this->db->affected_rows() > 0){
			$FlashData['Message']= '<strong>Το φίλτρο αφαιρέθηκε.</strong>';
			$FlashData['type'] = 'success';
		}else{
			$FlashData['Message']= '<strong>Το φίλτρο δεν βρέθηκε.</strong>';
			$FlashData['type'] = 'danger';
		}

		$this->session->set_flashdata('flash_message', $FlashData);
		redirect('crosssales');
	}


	function pushCrosssales($post = null){
		// $post=[
		// 	'filter' => 'MICROSOFT Office Home and Business 2016 Win Greek Medialess P2 (T5D-02896)',
		// 	'skus' => '1324839,1324842,1324911',
		// 	'cat' => 146
		// ];
		//Step 1. Find all filtered products from WP db
		$wpdb = $this->load->database('wordpress', TRUE);
		//$wpdb->select('wp_posts.*, post_meta.*, cross.meta_value as crosses');
		$wpdb->select('sku.meta_value as sku, sku.post_id as post_id, cross.meta_id as meta_id, cross.meta_value as cross_ids');
		$wpdb->like('wp_posts.post_title', $post['filter'], 'both');
		$wpdb->where('wp_posts.post_type', 'product');
		$wpdb->where('sku.meta_key', '_sku');
	  $wpdb->where('category.term_taxonomy_id', $post['category']);
		$wpdb->join('wp_postmeta as sku', 'wp_posts.ID = sku.post_id');
		$wpdb->join('wp_postmeta as cross', 'wp_posts.ID = cross.post_id and cross.meta_key = "_crosssell_ids"', 'left');
	  $wpdb->join('wp_term_relationships as category', 'wp_posts.ID = category.object_id');
		$prods = $wpdb->get('wp_posts')->result();


		//Step 2. Find ID's of submitted skus's

		$wpdb->select('post_id');
		$wpdb->where('meta_key', '_sku');
		$wpdb->where_in('meta_value', explode(',',$post['skus']));
		$crosIds = $wpdb->get('wp_postmeta')->result();
		$crossArr = [];
		foreach ($crosIds as $crossid) {
			$crossArr[] = $crossid->post_id;
			}

			$crossSer = serialize($crossArr);

		//Prevent PHP eroors for George by Alex
		if( ! $prods)
			return;

		//Ster 3. Update/Insert crosssells
		foreach ($prods as $prod) {
			if( isset($prod->cross_ids) && $prod->cross_ids != '' ){
				$data = ['meta_value' => $crossSer];
				$wpdb->where('meta_id', $prod->meta_id);
				$wpdb->update('wp_postmeta', $data);
			}else{
				$data = [
					'meta_value' => $crossSer,
					'post_id'=> $prod->post_id,
					'meta_key' => '_crosssell_ids'
				];
				$wpdb->insert('wp_postmeta', $data);
			}

		}

		return count($prods);

	}



}




?>
