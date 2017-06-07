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
	<form  method="POST" enctype="multipart/form-data" action="<?= base_url(); ?>descriptions/add/<?= $table?>">
		<div class="errors">
			<?php echo validation_errors(); ?>
		</div>
		<div class='row'>
			<div class="col-sm-6 col-md-3">
				<div class="form-group">
		            <label>1. Επιλέγξτε κατηγορία *</label>
			        <select onchange="getChars('categories')" id="categories" name="category" class="form-control">
			        	<option  value="">----</option>
								
	                    <?php
						if($this->input->post()){
							$category = $this->input->post('category');
							?>
									<option value="<?= $category; ?>" <?=  set_select('category', $category, TRUE); ?>><?= $category; ?></option>
							<?php
							
						}
							foreach($categories as $category){
						
						?>

							<option  onclick=""  value="<?= $category; ?>"><?= ucfirst($category); ?></option>

						<?php

							}
	                  	?>
			                   
			        </select>
		        </div>
			</div>
			<div class="col-sm-6 col-md-3">
				<div class="form-group">
				    <label>2. Τύπος Χαρακτηριστικού *</label>
					<select onchange="getChars('chars')" id="chars" name="char" class="form-control">
						<option value="">----</option>
						<?php
						if($this->input->post()){
							$char = $this->input->post('char');
							?>
									<option value="<?= $char; ?>" <?=  set_select('char', $char, TRUE); ?>><?= $char; ?></option>
							<?php
							
						}
						?>
				    </select>
				</div>
			</div>
			<div class="col-sm-6 col-md-3">
				<div class="form-group">
				    <label>3. Χαρακτηριστικό *</label>
					<select onchange="checkIfExists()"  id="type"  name="char_spec" class="form-control">
	                  	<option value="">----</option>
	                  	<?php
						if($this->input->post()){
							$char_spec = $this->input->post('char_spec');
							?>
									<option value="<?= $char_spec; ?>" <?=  set_select('char_spec', $char_spec, TRUE); ?>><?= $char_spec; ?></option>
							<?php
							
						}
						?>
					</select>
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
										<option value="<?= $char_spec; ?>" <?=  set_select('char_spec', $char_spec, TRUE); ?>><?= $char_spec; ?></option>
								
								
							}*/
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
										<option value="<?= $char_spec; ?>" <?=  set_select('char_spec', $char_spec, TRUE); ?>><?= $char_spec; ?></option>
							
							}*/
							?>
					    </select>
				    </div>
				</div>
			</div>
			<?php
			}
			?>
		<div class="row">
			<div class="col-sm-12 col-md-9">
				<label>Τίτλος *</label>
				<div class="form-group">
					<input type="text" class="form-control " value="<?= set_value('title'); ?>" name="title" id="title">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12 col-md-6">
				<label>Περιγραφή *</label>
				<div class="form-group">
					<textarea type="" rows="12" class="form-control " name="description" id="description"><?= set_value('description'); ?></textarea>
				</div>
			</div>
			<div class="col-sm-12 col-md-3">
				<label>Φωτογραφία *</label>
				<div class="form-group">
					<input type="file" name="image">
				</div>
				<label>Χρώμα Πλαισίου *</label>
				<div id="bg-color" class="form-group">
					<input type="text" class="form-control " value="<?= set_value('background_color','#ffffff'); ?>" name="background_color" id="background_color">
				</div>
				<script>
				    $(function() {
				        $('#background_color').colorpicker();
				    });
				</script>
				<label>Χρώμα Κειμένου </label>
				<div id="txt-color" class="form-group">
					<input type="text" class="form-control " value="<?= set_value('text_color','#00000'); ?>" name="text_color" id="text_color">
				</div>
				<script>
				    $(function() {
				        $('#text_color').colorpicker();
				    });
				</script>
				<label>Προτεραιότητα </label>
				<div class="form-group">
					<select name="important" class="form-control" id="important">
						<option <?=  set_select('important','primary' , TRUE); ?> value="primary">Primary</option>
						<option <?=  set_select('important', 'secondary'); ?> value="secondary">Secondary</option>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div style="clear:left" class="col-sm-6 col-md-3">
				<div class="form-group">
		            <button type="submit" class="btn btn-success btn-md" >Εισαγωγή</button> 
		        </div>
			</div>
		</div>
	</form>
</div>


<script>

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