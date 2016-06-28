<?php
$tables = Modules::run('categories/categoriesArray');

?>
<div class="sections col-xs-12">
<section class="content-header">
      <h1>
      Ενημέρωση αποθήκης
      </h1>  
      <br>	   
    </section> 
<form id='insert' method="POST" action="<?= base_url(); ?>insert/doInsert">

<div class="col-sm-4 col-md-4">

			<div class="form-group">
	                  	
	                  	<label>1. Επιλέγξτε κατηγορία</label>
		                  <select id="categories" name="categories" class="form-control">
		                  <option  value="">----</option>

		                    <?php

			
								foreach($tables as $table){
							?>

								<option  value="<?= $table; ?>"><?= ucfirst($table); ?></option>

							<?php

								}
		                  	?>
		                   
		                  </select>
	        </div>
	</div>
	<div class="row"></div>

	<div class="col-sm-4 col-md-4">

			<div class="form-group">
	                  	
	                  	<label>2. Συμπληρώστε όλα τα πεδία</label>
	                  	<input type="text" class="form-control" value="" placeholder="Product number" id="product_number" name="product_number"/>
	                  	<br /><input type="text" class="form-control" value="" placeholder="Κατασκευαστής" id="brand" name="brand"/>
	                  	<br /><input type="text" class="form-control" value="" placeholder="Τίτλος" id="title" name="title"/>
	                  	<br /><textarea class="form-control" rows="3" placeholder="Περιγραφή..."></textarea>
	                  	<br /><input type="button" class="btn btn-success btn-md" value="Εισαγωγή"  id="submit" />
          
	        </div>

	</div>
	</form> 
		<div class="row"></div>

	<div class="col-sm-4 col-md-4">
	<div id='error' class='callout' ></div>
	</div>
	
<script type="text/javascript">

				
				$(function(){


				var uloadUri = $('#insert').attr('action');

				$('#submit').on('click',function(event){

					var cat = $('#categories').val();
					var product_number = $('#product_number').val();
					var title = $('#title').val();
					var description = $('#description').val();
					
					if($.trim(title)=='' || $.trim(cat)=='' || $.trim(product_number)=='' || $.trim(brand)=='' ){
						$('#error').html('<i class="icon fa fa-ban"></i> Κάποια πεδία είναι κενά').removeClass('callout-success').addClass('callout-danger');
						$.stop();
					}

							var formData = new FormData();
							
							formData.append('cat', cat);
							formData.append('product_number', product_number);
							formData.append('title', title);
							formData.append('brand', brand);
							formData.append('description', description);

							$.ajax({

								url: uloadUri,
								type: 'post',
								data: formData,
								processData:false,
								contentType:false,
								success: function(data){
									if($.trim(data)=='ok'){
										$('#error').html('<i class="icon fa fa-check"></i> Κάποια πεδία είναι κενά').removeClass('callout-danger').addClass('callout-success');
										resetFields();
									}else{
						$('#error').html('<i class="icon fa fa-ban"> '+data).removeClass('callout-success').addClass('callout-danger');
										
									}
								}
							});


			});

		});


function resetFields(){
	$('#categories').val('');
	$('#product_number').val('')
	$('#title').val('');
	$('#brand').val('');
''};
			
		</script>