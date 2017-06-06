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
<div class = 'container-fluid'>
	<form  method="POST" enctype="multipart/form-data" action="<?= base_url(); ?>descriptions/update/<?= $table.'/'. $id;?>">
		<div class="errors">
			<?php echo validation_errors(); ?>
		</div>
		<div class="row">
			<div class="col-sm-6 col-md-3">
				<div class="form-group">
	                <label>1. Επιλέγξτε κατηγορία *</label>
	                  	<?php
	                  		foreach ($categories as $value) {
	                  			$new_category[$value] = $value;
	                  		}
							$category_value = ($this->input->post('category')!='') ? $this->input->post('category') : $chars_data[0]['category'];

	                  		echo form_dropdown('category', $new_category, $category_value, 'class="form-control" id="categories" onchange="getChars(\'categories\')"');
	                  	?>  
	        	</div>
			</div>
			<div class="col-sm-6 col-md-3">
				<div class="form-group">
	                <label>2. Τύπος Χαρακτηριστικού *</label>
	                  	<?php
	                  		$char_value = ($this->input->post('char')!='') ? $this->input->post('char') : $chars_data[0]['char'];
	                  		if($this->input->post('char')!='')
	                  		{
	                  			$category_char = Modules::run('descriptions/getCategoryChars',$category_value, 'list_chars');
	                  		}

	                  		echo form_dropdown('char', $category_char, $char_value, 'class="form-control" id="chars" onchange="getChars(\'chars\')"');
	                  	?>
	        	</div>
			</div>
			<div class="col-sm-6 col-md-3">
		 		<div class="form-group">
	                  	<label>3. Χαρακτηριστικό *</label>
	                  	<?php
	                  		$char_spec_value = ($this->input->post('char_spec')!='') ? $this->input->post('char_spec') : $chars_data[0]['char_spec'];
	                  		if($this->input->post('char_spec')!='')
	                  		{
	                  			$category_char_spec = array();
	                  			$category_char_spec_array = Modules::run('descriptions/getCategoryChars',$category_value, $char_value);
	                  			foreach ($category_char_spec_array as $value) {
	                  				if($value != '')
	                  					$category_char_spec[$value]=$value;
	                  			}
	                  		}

	                  		echo form_dropdown('char_spec', $category_char_spec, $char_spec_value, 'class="form-control" id="type" onchange="checkIfExists()"');
	                  	?>
		                  <p id="exist_error" style="display:none; color:red;"></p>
	        	</div>
			</div>
		</div>

		<?php
		if($table == 'specific')
		{
		?>
			<div class='row'>
				<div class="col-sm-6 col-md-3">
					<div class="form-group">
				        <label>4. SKU *</label>
					    <select onchange="checkIfExists()"  id="sku"  name="sku" class="form-control">
		                  	<option value="">----</option>
		                  	<?php/*
							if($this->input->post()){
								$char_spec = $this->input->post('char_spec');
								?>
										<option value="<?= $char_spec; ?>" <?=  set_select('char_spec', $char_spec, TRUE); ?>><?= $char_spec; ?></option>*/
								<?php
								
							}
							?>
					    </select>
				    </div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="form-group">
				        <label>5. BRAND *</label>
					    <select onchange="checkIfExists()"  id="brand"  name="brand" class="form-control">
		                  	<option value="">----</option>
		                  	<?php/*
							if($this->input->post()){
								$char_spec = $this->input->post('char_spec');
								?>
										<option value="<?= $char_spec; ?>" <?=  set_select('char_spec', $char_spec, TRUE); ?>><?= $char_spec; ?></option>*/
								<?php
								
							}
							?>
					    </select>
				    </div>
				</div>
			</div>
			<?php
			}
			?>
		<div class ="row">
			<div class="col-sm-12 col-md-9">
				<label>Τίτλος *</label>
				<div class="form-group">
					<?php
					$title_value = ($this->input->post('title')!='') ? $this->input->post('title') : $chars_data[0]['title'];
					?>
					<input type="text" class="form-control " value="<?= $title_value; ?>" name="title" id="title">
				</div>
			</div>
		</div>
		<div class ="row">
			<div class="col-sm-12 col-md-6">
				<label>Περιγραφή *</label>
				<div class="form-group">
					<?php
						$description_value = ($this->input->post('description')!='') ? $this->input->post('description') : $chars_data[0]['description'];
					?>
						<textarea type="" rows="12" class="form-control " name="description" id="description"><?= $description_value; ?></textarea>
				</div>
			</div>

			<div class="col-sm-12 col-md-3">
				<label>Φωτογραφία *</label>
				<div>
					<img src="<?= base_url() ?>/images/descriptions/<?= $chars_data[0]['image'] ?>" height="150" width="150">
				</div>
				<div class="form-group">
					<input type="file" name="image">
				</div>

				<label>Χρώμα Πλαισίου *</label>
				<div class="form-group">
					<?php
					$background_color_value = ($this->input->post('background_color')!='') ? $this->input->post('background_color') : $chars_data[0]['background_color'];
					?>
					<input type="text" class="form-control " value="<?= $background_color_value; ?>" name="background_color" id="background_color">
				</div>

				<label>Χρώμα Κειμένου </label>
				<div class="form-group">
					<?php
					$text_color_value = ($this->input->post('text_color')!='') ? $this->input->post('text_color') : $chars_data[0]['text_color'];
					?>
					<input type="text" class="form-control " value="<?= $text_color_value; ?>" name="text_color" id="text_color">
				</div>
				<label>Προτεραιότητα </label>
				<div class="form-group">
					<?php
					$importance = array('primary' =>'primary','secondary' =>'secondary');
					$important_value = ($this->input->post('important')!='') ? $this->input->post('important') : $chars_data[0]['important'];

					echo form_dropdown('important', $importance, $important_value, 'class="form-control" id="important" ');
					?>
				</div>
			</div>
		</div>
		<div class ="row">
			<div style="clear:left" class="col-sm-6 col-md-3">
				<div class="form-group">
	                <button type="submit" class="btn btn-success btn-md" >Ενημέρωση</button> 
	        	</div>
			</div>
		</div>
	</form>
</div>

<script>
	 /*$(document).ready( function() {
	 	getChars('categories');
	 	getChars('chars');
	 });*/

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
				$('#type').html('');
			}    

			if(a == 'chars'){
				$('#type').html(data);
			}   
	    });
	}			
</script>