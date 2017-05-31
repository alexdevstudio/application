<div>
<?php 

if($basic_templates){
	?>
	<div class='row'>
		<div class="col-md-12 table-data">
          	<table id="basic" class="table table-hover table-condensed datagrid">
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
		</div>
	</div>
	<?php
}else{
		echo '<p>No basic templates yet.</p>';
}

if($specific_templates){
	?>
	<div class='row'>
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
		echo '<p>No specific templates yet.</p>';
}
 ?>
 </div>

 <script>
  $(".datagrid").delegate('tbody tr', 'click', function(e) {

    if (e.target.tagName == 'TD') {
      window.location = ' <?php echo base_url(); ?>' + 'descriptions/update/' + $(this).closest('table').attr('id') +'/'+ $(this).closest('tr').attr('id');
    } 
  });
</script>