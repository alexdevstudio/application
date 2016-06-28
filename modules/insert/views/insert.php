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
	                  	<br /><textarea id="description" name="description" class="form-control" rows="3" placeholder="Περιγραφή..."></textarea>
          
	        </div>

	</div>
	<div class="row"></div>
	<div class="col-sm-4 col-md-4">

			<div class="form-group">
	                  	
	                  	<label>3. Συμπληρώστε 5 URL φωτογραφειών </label>

	                  		  <input type="text" class="form-control" value="" placeholder="Φώτο 1" id="image1" name="image1"/>
	                  	<br /><input type="text" class="form-control" value="" placeholder="Φώτο 2" id="image2" name="image2"/>
	                  	<br /><input type="text" class="form-control" value="" placeholder="Φώτο 3" id="image3" name="image3"/>
	                  	<br /><input type="text" class="form-control" value="" placeholder="Φώτο 4" id="image4" name="image4"/>
	                  	<br /><input type="text" class="form-control" value="" placeholder="Φώτο 5" id="image5" name="image5"/>

          
	        </div>

	</div>
<div class="row"></div>
		<div class="col-sm-4 col-md-4">

			<div class="form-group">
	                  	
	                  	<label>4. Διαθεσιμότητα</label>
	                  	
	                  		  <select class='form-control' name="av" id="av">
	                  		  	<option value="">----</option>
	                  		  	<option value="1">Διαθέσιμο στο κατάστημα</option>
	                  		  	<option value="0">Μη διαθέσιμο στο κατάστημα / Διαθέσιμο στον προμηθευτή</option>
	                  		  </select>

	                  	
          
	        </div>

	</div>
	<div class="row"></div>
	<div class="col-sm-4 col-md-4">

			<div class="form-group">
	                  	
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


				var url = $('#insert').attr('action');

				$('#submit').on('click',function(event){

					var cat = $('#categories').val();
					var brand = $('#brand').val();
					var product_number = $('#product_number').val();
					var title = $('#title').val();
					var description = $('#description').val();
					var image1 = $('#image1').val();
					var image2 = $('#image2').val();
					var image3 = $('#image3').val();
					var image4 = $('#image4').val();
					var image5 = $('#image5').val();
					var av = $('#av').val();
					
					if($.trim(title)=='' || $.trim(cat)=='' || 
						$.trim(description)=='' || $.trim(product_number)=='' ||
						$.trim(brand)==''  || $.trim(image1)==''  || 
						$.trim(image2)==''  || $.trim(image3)==''  || 
						$.trim(image4)==''  || $.trim(image5)==''  || $.trim(av)=='' )
					{
						$('#error').html('<i class="icon fa fa-ban"></i> TEst:Κάποια πεδία είναι κενά').removeClass('callout-success').addClass('callout-danger');
						return false;
					}

							var formData = new FormData();
							
							formData.append('cat', cat);
							formData.append('product_number', product_number);
							formData.append('title', title);
							formData.append('brand', brand);
							formData.append('description', description);
							formData.append('image1', image1);
							formData.append('image2', image2);
							formData.append('image3', image3);
							formData.append('image4', image4);
							formData.append('image5', image5);
							formData.append('av', av);

							$.ajax({

								url: url,
								type: 'post',
								data: formData,
								processData:false,
								contentType:false,
								success: function(data){
									if($.trim(data)=='ok'){
										$('#error').html('<i class="icon fa fa-check"></i> Το προϊόν καταχωρήθηκε').removeClass('callout-danger').addClass('callout-success');
										resetFields();
									}else{
						$('#error').html('<i class="icon fa fa-ban"> False:'+data).removeClass('callout-success').addClass('callout-danger');
										
									}
								}
							});


			});

		});


		function resetFields(){
			//$('input').val('');

			alert('ok recieved');
			
		}
			
		</script>