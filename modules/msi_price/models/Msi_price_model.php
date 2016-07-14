<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Msi_price_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

 
   public function insertNewLaptops($allLaptops)
   {

      foreach ($allLaptops->result() as $lpt) 
      {
        $where = array('sku'=>$lpt->sku);
        $exists = Modules::run("crud/get",'msi_price',$where);

        if(!$exists)
        {
          Modules::run("crud/insert",'msi_price',$where);
        }
      }
 
      return true;

   }

 
   public function getMsiLaptops($table, $join_table, $join)
   {

      
    $laptops = Modules::run("crud/join", $table, $join_table, $join);
    return $laptops;

   }
  
 
}