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
        <input type="hidden" name='client' value='<?php echo $this->session->userdata("client"); ?>'>
        <input type="hidden" name='technician' value='<?php echo $this->session->userdata("user"); ?>'>

    <select name="category" id="category" >
     <option <?= set_select('category', 'Πληροφορική'); ?> value="Πληροφορική" default>Πληροφορική</option>
     <option  <?= set_select('category', 'Τηλεφωνία'); ?> value="Τηλεφωνία">Τηλεφωνία</option>
     <option  <?= set_select('category', 'VOIP'); ?> value="VOIP">VOIP</option>
     <option  <?= set_select('category', 'Copiers'); ?> value="Copiers">Copiers</option>
     <option  <?= set_select('category', 'UPS'); ?> value="UPS">UPS</option>
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
 <h3> Τελευταία Ημερήσια Δελτία: <?= $this->session->userdata('user'); ?></h3>

  <div >

    <?php
    foreach ($lastTickets as $category) {
      ?>
        <div style="width:20%;float:left;">
            <?=  $category[0]->category."<br>"; ?>
            <?=  '<strong style="color:red;">'.$category[0]->ticket_nr."</strong><br>"; ?>
            <?=  date("d-m-Y", strtotime($category[0]->ticket_date)); ?>
            <br/><br/>
         </div>

      <?php
    }
   ?>
   
  </div>


   <form style="display:block; clear:both;" id="reset" action="<?= base_url(); ?>keelpno/reset" method="post">
 <br> <br> <br> <br> <br>
    <input style="color:#fff;background:red;" id="" type="submit" value="Reset">
 </form>
 </body>
</html>