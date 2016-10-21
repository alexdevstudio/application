<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Crud_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }


  public function update($table, $where, $data){
		
    $this->dataValidation($table, $data);
		$this->db->where($where);
		$this->db->set($data);
		return $this->db->update($table);

  }

   public function updateWp($table, $where, $data){
    $wpdb = $this->load->database('wordpress', TRUE);
    $wpdb->where($where);
    $wpdb->set($data);
    return $wpdb->update($table);

  }


  public function delete($table, $where){
        
        $this->db->where($where);
        return $this->db->delete($table);
  }

  

  public function get($category, $where=null){

        if($where)
        {
        $this->db->where($where);
        }
          $item = $this->db->get($category);

          if($item->num_rows()<1){
            return false;
          }
          else
          {
            return $item;
          }
      }

      public function getWp($category, $where=null){

      $wpdb = $this->load->database('wordpress', TRUE);

      if($where)
      {
      $wpdb->where($where);
      }
        $item = $wpdb->get($category);

        if($item->num_rows()<1){
          return false;
        }
        else
        {
          return $item;
        }



    }
    public function insert($table, $data){

        $this->dataValidation($table, $data);
        $item = $this->db->insert($table, $data);

        if($this->db->affected_rows()>0){
          return $item;
        }
        else
        {
          return false;
        }

      


    }
public function insertWp($table, $data){
      $wpdb = $this->load->database('wordpress', TRUE);

        $item = $wpdb->insert($table, $data);

        if($wpdb->affected_rows()>0){
          return $item;
        }
        else
        {
          return false;
        }

      


    }

    public function join($table, $join_table, $join)
    {
       
      $this->db->select('*');
      $this->db->from($table);
      $this->db->join($join_table, $join);
     
      $result = $this->db->get();

      return $result;
    }

    private function dataValidation($table, $data)
    {
      foreach ($data as $key => $value) {
          $variable= strtolower($value);
          if($value=='yes' || $value=='nai' || $value=='ναι' || $value=='ναί')
            $data[$key]='ΝΑΙ';
          elseif($value=='no' || $value=='νο' || $value=='οχι' || $value=='όχι')
            $data[$key]='ΟΧΙ';

          if (($table=='laptops' || $table=='desktops' || $table=='smartphones' || $table=='tablets') && $key=='screen_resolution')
            $data[$key]= str_replace(' x ', 'x', $value);

         // if()
        }
        return $data;
    }

}