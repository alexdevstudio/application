<html>
	<form action="" method="post">
		<select name="day" id="">
        <?php 
        $i=1;
        while ($i<32) {

          
          $value = sprintf('%02d', $i);
          $value = trim($value);
          ?>
  
        <option 
         <?php 
        if($this->input->post('day')){ 
         echo set_select('day', $value);
         }else{
          if($value == date('d')){
           echo 'selected';
            }
          } ?> value="<?= $value; ?>"><?= trim($value);  ?></option>

          <?php
          $i++;
        }
         ?>
        </select>
          <select name="month" id="">
        <?php 
        $i=1;
        while ($i<13) {
          $value = sprintf('%02d', $i);
          $value = trim($value);
          ?>
  
        <option 
         <?php 
        if($this->input->post('month')){ 
         echo set_select('month', $value);
         }else{
          if($value == date('m')){
           echo 'selected';
            }
          } ?> value="<?= $value; ?>"><?= trim($value);  ?>
          </option>

          <?php
           $i++;
        }
         ?>
        </select>
		
        <input type="hidden" name='year' value='<?php echo date("Y"); ?>'>

   <select name="technician" id="technician" >
     <option <?= set_select('technician', 'Άλεξ'); ?> value="Άλεξ" default>Άλεξ</option>
     <option  <?= set_select('technician', 'Γιώργος'); ?> value="Γιώργος">Γιώργος</option>
     <option  <?= set_select('technician', 'Τάκης'); ?> value="Τάκης">Τάκης</option>
     <option  <?= set_select('technician', 'Θανάσης'); ?> value="Θανάσης">Θανάσης</option>
   </select>
    <select name="category" id="category" >
     <option <?= set_select('category', 'Πληροφορική'); ?> value="Πληροφορική" default>Πληροφορική</option>
     <option  <?= set_select('category', 'Τηλεφωνία'); ?> value="Τηλεφωνία">Τηλεφωνία</option>
     <option  <?= set_select('category', 'VOIP'); ?> value="VOIP">VOIP</option>
   </select>
   <?php  
    $ticket="";
   if(isset($ticket_nr)){
        $ticket = $ticket_nr + 1;
    } ?>
<input type="number" name="ticket_nr" value="<?= $ticket++; ?>" placeholder='Αριθμός ΔΤΕ'>

   <button type="submit">Create</button>
	</form>
   <hr> 
 <h3> Τελευταία Ημερήσια Δελτία</h3>

  <?php 
    foreach ($lastTickets as $technician => $categories) {
      ?>
   <div style="width:20%;float:left;">
    <h3 style="clear:both;"><?= $technician; ?>: </h3>
    <?php 
    foreach ($categories as $category) {

      ?>
        <div style="width:33%;">
            <?=  $category[0]->category."<br>"; ?>
            <?=  '<strong style="color:red;">'.$category[0]->ticket_nr."</strong><br>"; ?>
            <?=  date("d-m-Y", strtotime($category[0]->ticket_date)); ?>
            <br/><br/>
         </div>
      <?php
    }
      ?>
   
  </div>

      <?php
    }
   ?>
</html>