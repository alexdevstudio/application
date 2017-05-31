<br>
<br>
<?php
    //flash messages
    if($this->session->flashdata('flash_message')){

      echo '<div class="alert alert-'. $this->session->flashdata('flash_message')['type'].'">';
      echo '<a class="close" data-dismiss="alert">&times</a>';
      echo $this->session->flashdata('flash_message')['Message'];   
      echo '</div>'; 
    }
?>
<form  method="POST" enctype="multipart/form-data" action="<?= base_url(); ?>descriptions/add">
<div class="errors">
	<?php echo validation_errors(); ?>
</div>
<div class="col-sm-6 col-md-3">

			<div class="form-group">
	                  	
	                  	<label>1. Επιλέγξτε κατηγορία *</label>
	                  	<?php

	                  		foreach ($categories as $value) {
	                  			$new_category[$value] = $value;
	                  		}

	                  		echo form_dropdown('category', $new_category, $chars_data[0]['category'], 'class="form-control" id="categories" onchange="getChars(\'categories\')"');
	                  	?>
		                  
	        </div>
	       
	      

	</div>
	<div class="col-sm-6 col-md-3">

			<div class="form-group">
	                  	
	                  	<label>2. Τύπος Χαρακτηριστικού *</label>
	                  	<?php
	                  	echo $chars_data[0]['char'];
	                  		echo form_dropdown('char', '', $chars_data[0]['char'], 'class="form-control" id="chars" onchange="getChars(\'chars\')"');
	                  	?>
	        </div>

	</div>
	<div class="col-sm-6 col-md-3">

		 <div class="form-group">
	                  	
	                  	<label>3. Χαρακτηριστικό *</label>
	                  	<?php
	                  	echo $chars_data[0]['char_spec'];
	                  		echo form_dropdown('char_spec', '', $chars_data[0]['char_spec'], 'class="form-control" id="type" onchange="checkIfExists()"');
	                  	?>
		                  <p id="exist_error" style="display:none; color:red;"></p>
	        </div>

	</div>
	<div class="col-sm-12 col-md-9">
	<label>Τίτλος *</label>
		<div class="form-group">
			<input type="text" class="form-control " value="<?= $chars_data[0]['title']; ?>" name="title" id="title">
		</div>
		</div>
	<div class="col-sm-12 col-md-6">
	<label>Περιγραφή *</label>
		<div class="form-group">
			<textarea type="" rows="12" class="form-control " name="description" id="description"><?= $chars_data[0]['description']; ?></textarea>
		</div>
	</div>
	<div class="col-sm-12 col-md-3">
	<label>Φωτογραφία *</label>
		<div class="form-group">
			<input type="file" name="image">
		</div>
		<label>Χρώμα Πλαισίου *</label>
		<div class="form-group">
			<input type="text" class="form-control " value="<?= $chars_data[0]['background_color']; ?>" name="background_color" id="background_color">
		</div>
<label>Χρώμα Κειμένου </label>
		<div class="form-group">
			<input type="text" class="form-control " value="<?= $chars_data[0]['text_color']; ?>" name="text_color" id="text_color">
		</div>
<label>Προτεραιότητα </label>
		<div class="form-group">
			<?php
			print_r($chars_data[0]['important']);
			$importance = array('primary' =>'primary','secondary' =>'secondary');
			echo form_dropdown('important', $importance, $chars_data[0]['important'], 'class="form-control" id="important" ');
			?>

		</div>
	</div>
	
	<div style="clear:left" class="col-sm-6 col-md-3">

			<div class="form-group">
	                  <button type="submit" class="btn btn-success btn-md" >Εισαγωγή</button> 
	        </div>

	</div>
	</form>



	<script>
	 $(document).ready( function() {
	 	getChars('categories');
	 	getChars('chars');
	 });

	function checkIfExists(){
		$('#exist_error').hide();
		var a = $('#categories').val();
		var b = $('#chars').val();
		var c = $('#type').val();
		var url = "<?= base_url()?>descriptions/ifExistsBasic/"+a+"/"+b+"/"+"/"+c;

		$.post(url, function(data){
			if(data=='error'){
				$('#exist_error').html('Αυτή καταχώριση υπάρχη ήδη. <a href="<?= base_url() ?>descriptions/editBasic/'+data+'">Επεξεργασία</a>');
			}

		});
	}

	function getChars(a){

		var b = $('#'+a).val();
		var url = "<?= base_url()?>descriptions/getChars/"+a+"/"+b;
		
		$.post(url, function(data){
			//$("#xmltitle").html(a); 
			if(a == 'categories'){
				$('#chars').html(data);
			}    

			if(a == 'chars'){
				$('#type').html(data);
			}   
	    });
	}
					
	</script>