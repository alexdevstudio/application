<?php
$tables = Modules::run('categories/categoriesArray');

?>
<div class="sections col-xs-12">
<section class="content-header">
      <h1>
      Εισαγωγή νέου προϊόντος
      </h1>  
      <br>	   
    </section> 
<form id='insert' method="POST" action="<?= base_url(); ?>insert/doInsert">

<div class="col-sm-6 col-md-3">

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
	<div class="col-sm-6 col-md-3">

			<div class="form-group">
	                  	
	                  	<label>2. Συμπληρώστε όλα τα πεδία</label>
	                  	<input type="text" class="form-control" value="" placeholder="Product number" id="product_number" name="product_number"/>
	                  	<br /><input type="text" class="form-control" value="" placeholder="Κατασκευαστής" id="brand" name="brand"/>
	                  	<br /><input type="text" class="form-control" value="" placeholder="Τίτλος" id="title" name="title"/>
	                  	<br /><textarea id="description" name="description" class="form-control" rows="3" placeholder="Περιγραφή..."></textarea>
          
	        </div>

	</div>
	<div class="col-sm-6 col-md-3">

			<div class="form-group">
	                  	
	                  	<label>3. Συμπληρώστε 5 URL φωτογραφειών </label>

	                  		  <input type="text" class="form-control" value="" placeholder="Φώτο 1" id="image1" name="image1"/>
	                  	<br /><input type="text" class="form-control" value="" placeholder="Φώτο 2" id="image2" name="image2"/>
	                  	<br /><input type="text" class="form-control" value="" placeholder="Φώτο 3" id="image3" name="image3"/>
	                  	<br /><input type="text" class="form-control" value="" placeholder="Φώτο 4" id="image4" name="image4"/>
	                  	<br /><input type="text" class="form-control" value="" placeholder="Φώτο 5" id="image5" name="image5"/>

          
	        </div>

	</div>
	<div class="col-sm-6 col-md-3">

			<div class="form-group">
	                  	
	                  	<label>4. Διαθεσιμότητα</label>
	                  	
	              		  <select class='form-control' name="av" id="av">
	              		  	<option value="">----</option>
	              		  	<option value="1">Διαθέσιμο στο κατάστημα</option>
	              		  	<option value="0">Μη διαθέσιμο στο κατάστημα / Διαθέσιμο στον προμηθευτή</option>
	              		  </select>

	                  	
          
	        </div>
	        <div style="display:none;" class="form-group">
	                  	
	                  	<label>5. Τιμή χονδρικής Χωρίς ΦΠΑ (με τελεία για διαχωριστικό των δεκαδικών.)</label>
	                  	<input type="text" class="form-control" value="" placeholder="ΠΧ 125.35" id="price" name="price"/>
	                  	
	        </div>

	</div>
	
		
	<div class="row"></div>
	<div class="col-sm-6 col-md-3">

			<div class="form-group">
	                  	<?= base_url(); ?>
	                  <br /><input type="button" class="btn btn-success btn-md" value="Εισαγωγή"  id="submit" />
          
	        </div>

	</div>
	</form>
		<div class="row"></div>

	<div class="col-sm-6 col-md-3">
	<div id='error' class='callout' ></div>
	</div>
	
