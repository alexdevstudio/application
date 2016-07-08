
<div class="sections col-xs-12">
	<section class="content-header">
		<h1>Επεξεργασία προϊόντος</h1>  
		<br>	   
    </section> 

    <div class="col-xs-12 col-md-2">

   
 <?php 

$sku = $item->row()->sku;
$pn = $item->row()->product_number;

$image = Modules::run("images/getFirstImage",$sku,true);

 ?>

 <div class="col-xs-12 bottom-margin  image-fix">
 <?= $image; ?>
</div>
 <div class="col-xs-12 ">
 
 <div class="">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h4 style='font-weight:bold;'><?= $sku; ?></h4>

              <p>SKU</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            
          </div> 
        </div>

        <div class="">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h4 style='font-weight:bold;'><?= $pn; ?></h4>

              <p>Product Number</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            
          </div>
        

 </div>
</div>

 <div class="col-xs-12 ">
 <form method="post" action=''>
<label>Τελική τιμή στο Eshop</label>
<?php
$price = '';
$availability = '';

$instock = '';
$outstock = '';

if($itemLive){
	$price = $itemLive->row()->price_tax;

	$av = $itemLive->row()->availability;

	if($av=='Άμεσα Διαθέσιμο'){
		$instock = 'selected';
	}elseif($av=='Κατόπιν παραγγελίας σε 1 εργάσιμη'){
		$outstock = 'selected';
	}
}



?>
<input class='form-control' type="hidden" name='product_number' value='<?= $pn; ?>'>
<input class='form-control' type="hidden" name='category' value='<?= $category; ?>'>
<input class='form-control' type="hidden" name='delete_flag' value='0'>
<input class='form-control' type="hidden" name='status' value='add'>

<input class='form-control' name='price_tax' value='<?= $price; ?>'>

<div class="form-group">
	                  	
	                  	<label>Διαθεσιμότητα</label>
	                  	
	              		  <select class='form-control' name="availability" id="availability">
	              		  	<option value="">----</option>
	              		  	<option value="1" <?= $instock; ?>>Διαθέσιμο στο κατάστημα</option>
	              		  	<option value="0" <?= $outstock; ?>>Μη διαθέσιμο στο κατάστημα / Διαθέσιμο στον προμηθευτή</option>
	              		  </select>

	                  	
          
	        </div>

	<button type="submit" class="btn btn-block btn-warning">Προσθήκη στο Eshop</button>

</form>
<br/>
</div>
		<!-- Delete Form -->
		<div class="col-xs-12 ">
	 		<form method="post" action="">
	 			<input class='form-control' type="hidden" name='product_number' value='<?= $pn; ?>'>
	 			<input class='form-control' type="hidden" name='category' value='<?= $category; ?>'>
	 			<input class='form-control' type="hidden" name='status' value='delete'>

	 			<button type="submit" class="btn btn-block btn-danger">Aφαίρεση από STOCK</button>
	 		</form>
		</div>
    </div>

		<div class=" col-xs-12 col-md-10">
		<form  method='post' action="">

   <?php
	$items = $item->result_array();
	foreach ($items[0] as $key => $value) {
		
		//print_r($item);
		if($key=='product_number' || $key=='id' || $key=='sku' || $key=='new_item'){
			continue;
		}
		?>

		<div class="col-xs-12 col-md-5">

		<div class="col-xs-12 col-md-4">
		<label><?= ucfirst(str_replace('_', ' ', $key));  ?></label>
		</div>
		<div class="col-xs-12 col-md-8">
			<input class='form-control' type="hidden" name='new_item' value='0'>
			<input class='form-control' type="hidden" name='status' value='update'>
			<?php 

			if($key == 'description'){

?>
<textarea  name="<?= $key; ?>" class="form-control edit-form-etd" value="" >
<?php if(isset($_POST[$key])){ echo $_POST[$key]; }else{echo $value; } ?>
</textarea>
</div>
<?php
}else{
			?>
			<input type="text" name="<?= $key; ?>" class="form-control edit-form-etd" value="<?php if(isset($_POST[$key])){ echo $_POST[$key]; }else{echo $value; } ?>" /></div>
		
		
		<?php
		}
		?>

		</div>

		<?php
	}
	?>
	<button type="submit" class="btn btn-block btn-success">Ενημέρωση</button>
		</form>
		</div>
</div>