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
   </select>
    <select name="category" id="category" >
     <option <?= set_select('category', 'Πληροφορική'); ?> value="Πληροφορική" default>Πληροφορική</option>
     <option  <?= set_select('category', 'Τηλεφωνία'); ?> value="Τηλεφωνία">Τηλεφωνία</option>
     <option  <?= set_select('category', 'VOIP'); ?> value="VOIP">VOIP</option>
   </select>
<input type="number" name="ticket_nr" placeholder='Αριθμός ΔΤΕ'>

   <button type="submit">Create</button>
	</form>
</html>