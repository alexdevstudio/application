<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Duplicates_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


    //Get product numbers by two skus
    function insert($skuArr){
      //Check if this combination exists in table 'duplicates'


      $pnArr = [];
      $exist = true;

      foreach ($skuArr as $sku) {

        $where = ['sku.id' => $sku];
        $this->db->where($where);
        $this->db->join('live', 'live.product_number = sku.product_number' );
        $product = $this->db->get('sku');
        if( ! $product->result())
          $exist = false;
        else
          $pnArr[] = $product->row()->product_number;
    }//13180812 1318081

      if( $exist ){
        //insert new dulicate
        $data = [
          'sku_in' => $_POST['sku_in'],
          'sku_out' => $_POST['sku_out'],
          'created_at' => date("Y-m-d H:i:s")
        ];
        $this->db->insert('duplicates', $data);

        //Set the supplier for sku_out as "out"
        $data = [
          'supplier' => 'out',
          'delete_flag' => '100',
          'status' => 'trash'
        ];
        $this->db->where('product_number', $pnArr[1]);
        $this->db->update('live', $data);

        //For auto update the WP with update_wp
        Modules::run('extract/allImport',$product->row()->category,'one',0,$sku);
        $FlashData['Message']= 'Επιτυχής καταχώρηση';
        $FlashData['type'] = 'success';
        $this->session->set_flashdata('flash_message', $FlashData);

        return true;
      }

      return;
    }

    function getDuplicates(){
      $this->db->join('sku', 'sku.id = duplicates.sku_in' );
      $sku['sku_in'] = $this->db->get('duplicates');

      $this->db->join('sku', 'sku.id = duplicates.sku_out' );
      $sku['sku_out'] = $this->db->get('duplicates');

      return $sku;
    }

    function delete($sku_in, $sku_out){
      $where = [
        'sku_in' => $sku_in,
        'sku_out' => $sku_out
      ];
      $this->db->delete('duplicates', $where);

      //Remove product from Live, to allow the XML to update it

        //1. Get the rpoduct number
        $where = ['id' => $sku_out];
        $pn = $this->db->get_where('sku', $where)->row()->product_number;

        //2. Delete from Live
        $where = ['product_number' => $pn];
        $this->db->where($where);
        $this->db->delete('live');

      return true;

    }

    function checkDuplicates(){
      $products = $this->db->get('duplicates');
      if( ! $products->result())
        return;


      foreach ($products->result() as $product) {
        $where = ['live.status' => 'trash', 'sku.id' => $product->sku_in];
        $this->db->where($where);
        $this->db->join('live', 'live.product_number = sku.product_number');
        $checkProduct = $this->db->get('sku');

        if($checkProduct->result())
          $this->delete($product->sku_in, $product->sku_out);
      }
    }

}
