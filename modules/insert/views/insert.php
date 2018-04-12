<?php   $tables = Modules::run('categories/fullCategoriesArray'); ?>
<br>
<div class="sections col-sm-12 col-md-9">

<form id='insert' method="POST" action="<?= base_url(); ?>insert/doInsert">
<div class="box box-primary">
  <div class="box-header">
Εισαγωγή νέου προϊόντος
  </div>
  <div class="box-body">
    <div class="col-sm-12 col-md-6">

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
    	        <!-- Added for mouse Keyboard import -->
    	        <div id="cat_type" style="display: none;" class="form-group">
    	        	<label>Επιλέγξτε τύπο</label>
    					<select id="type" name="type" class="form-control">
    					  <option  value="Keyboard">Keyboard</option>
    					  <option  value="Mouse">Mouse</option>
    					  <option  value="Set mouse / keyboard">Set mouse / keyboard</option>
    					</select>
    	        </div>
    	       <script>
    	       $('#categories').on('change', function () {
    		        var type =$(this).val();
    		        if(type == 'keyboard_mouse'){
    		        	$('#cat_type').show();
    		        }
    		        else{cat_type
    		        	$('#cat_type').hide();
    		        }
    			});
    			</script>
    			<!-- End of Added for mouse Keyboard import -->

    	</div>
    	<div class="col-sm-12 col-md-6">

    			<div class="form-group">

    	                  	<label>2. Συμπληρώστε όλα τα πεδία</label>
    	                  	<input type="text" class="form-control" value="" placeholder="Product number" id="product_number" name="product_number"/>
    	                  	<br /><input type="text" class="form-control" value="" placeholder="Κατασκευαστής" id="brand" name="brand"/>
    	                  	<br /><input type="text" class="form-control" value="" placeholder="Τίτλος" id="title" name="title"/>
    	                  	<br /><textarea id="description" name="description" class="form-control" rows="3" placeholder="Περιγραφή..."></textarea>

    	        </div>

    	</div>

  </div>
  <div class="box-footer">
    <div class="col-md-12">

    <div class="form-group">
                 <input type="button" class="btn btn-success btn-md pull-right" value="Εισαγωγή"  id="submit" />
        </div>
      </div>
  </div>
</div>

	</form>
		<div class="row"></div>

	<div class="col-sm-6 col-md-3">
	<div id='error' class='callout' ></div>
	</div>
