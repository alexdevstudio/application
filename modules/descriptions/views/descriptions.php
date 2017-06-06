
<div class="col-md-12 table-data">
		<hr>
		<div class='nav-tabs-custom'>
		<ul class="nav nav-tabs">
              <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Βασικά</a></li>
              <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">Στοχευμένα</a></li>
              
            </ul>
		<div class="tab-content">
            
		<div class="tab-pane active" id="tab_1">
		<div class="">
		<div class="box-header"><h3 class="box-title">Βασικά χαρακτηριστικά <a class="btn btn-success btn-xs" href="<?= base_url().'descriptions/add' ?>">+ Προσθήκη</a></h3></div>
<?php 

if($basic_templates){
	?>
	
		  
		
          	<table id="basic" class="table table-striped table-bordered table-hover dataTable datagrid">
	            <thead>
	              <tr class="header">
	                <!--<th>#</th>-->
	                <th>Κατηγορία</th>
	                <th>Τύπος Χαρακτηριστικού</th>
	                <th>Χαρακτηριστικό</th>
	                <th>Τίτλος</th>
	                <th>Περιγραφή</th>
	                <th>Φωτογραφία</th>
	                <th>Χρώμα Πλαισίου</th>
	                <th>Χρώμα Κειμένου</th>
	                <th>Προτεραιότητα</th>
	              </tr>
	            </thead>
	            <tbody>
	            <?php

	            foreach ($basic_templates->result_array() as $item) {


	            	echo '<tr id="' . $item['id'] . '">';
	            	//echo '<tr class="clickable-row" data-href="'.base_url().'descriptions/update/'.$item['id'].'">';
		            	echo '<td>'.$item['category'].'</td>';
		                echo '<td>'.$item['char'].'</td>';
		                echo '<td>'.$item['char_spec'].'</td>';
		                echo '<td>'.$item['title'].'</td>';
		                echo '<td>'.$item['description'].'</td>';
		                echo '<td>'.$item['image'].'</td>';
		                echo '<td>'.$item['background_color'].'</td>';
		                echo '<td>'.$item['text_color'].'</td>';
		                echo '<td>'.$item['important'].'</td>';
	                echo '</tr>';
	            }

	            ?>
	         	</tbody>
	      	</table>
		
	<?php
}else{
		echo '<p>Χωρίς βασικά χαρακτηριστικά.</p>';
}
?>
		</div>
	</div>
	<div class="tab-pane active" id="tab_2">
		<div class="">
		<div class="box-header"><h3 class="box-title">Στοχευμένα χαρακτηριστικά <a class="btn btn-success btn-xs" href="<?= base_url().'descriptions/add/speci+ fic' ?>">+ Προσθήκη</a></h3></div>
<?php
if($specific_templates){
	?>
	<div>
	<h3> Συγκεκριμένα χαρακτηριστικά </h3>
	<div class="col-md-12">
		<div class="table-data">
          	<table id="specific" class="table table-hover table-condensed datagrid">
	            <thead>
	              <tr class="header">
	                <!--<th>#</th>-->
	                <th>Κατηγορία</th>
	                <th>Μάρκα</th>
	                <th>SKU</th>
	                <th>Τύπος Χαρακτηριστικού</th>
	                <th>Χαρακτηριστικό</th>
	                <th>Τίτλος</th>
	                <th>Περιγραφή</th>
	                <th>Φωτογραφία</th>
	                <th>Χρώμα Πλαισίου</th>
	                <th>Χρώμα Κειμένου</th>
	                <th>Προτεραιότητα</th>
	              </tr>
	            </thead>
	            <tbody>
	            <?php

	            foreach ($specific_templates->result_array() as $item) {

	            	echo '<tr id="' . $item['id'] . '">';
	            	//echo '<tr class="clickable-row" data-href="'.base_url().'descriptions/update/'.$item['id'].'">';
		            	echo '<td>'.$item['category'].'</td>';
		            	echo '<td>'.$item['brand'].'</td>';
		            	echo '<td>'.$item['sku'].'</td>';
		                echo '<td>'.$item['char'].'</td>';
		                echo '<td>'.$item['char_spec'].'</td>';
		                echo '<td>'.$item['title'].'</td>';
		                echo '<td>'.$item['description'].'</td>';
		                echo '<td>'.$item['image'].'</td>';
		                echo '<td>'.$item['background_color'].'</td>';
		                echo '<td>'.$item['text_color'].'</td>';
		                echo '<td>'.$item['important'].'</td>';
	                echo '</tr>';
	            }

	            ?>
	         	</tbody>
	      	</table>
		</div>
		</div>
	</div>
	<?php
}
else{
		echo '<p>Χωρίς συγκεκριμένα χαρακτηριστικά.</p>';
}

 ?>
		 </div>
		 </div>
  </div>
  </div>
  </div>


 <script>
  $(".datagrid").delegate('tbody tr', 'click', function(e) {

    if (e.target.tagName == 'TD') {
      window.location = ' <?php echo base_url(); ?>' + 'descriptions/update/' + $(this).closest('table').attr('id') +'/'+ $(this).closest('tr').attr('id');
    } 
  });
</script>