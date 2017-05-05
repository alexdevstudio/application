<!DOCTYPE html>
<html>
  <head>
    <style>
    body {
        height: 842px;
        width: 630px;
        /* to centre page on screen*/
        margin-left: auto;
        margin-right: auto;
    font-family: 'Tahoma';

    }
    div{
      box-sizing: border-box;
    }
    img{
      max-width: 100%;
    }
    .header {
    border: 1px solid #000;
    font-size: 16px;
    text-align: center;
    font-weight: bold;
    color: #05053d;
}

.blue{
  color: #05053d;
}

.date-number-item{
  width:33%;
  float:left;
  text-align:center;
  font-size: 13px
}
.date-number-subitem{
  width:50%;
  float:left;
}
.bold{
  font-weight:bold;
}
.center{
  text-align: center;
}

.f16{
  font-size: 16px;
}

.f14{
  font-size: 14px;
}

.f13{
  font-size: 13px;
}

.f12{
  font-size: 12px;
}
.f11{
  font-size: 11px;
}

.id-number{
  color:red;
  font-size: 14px;
  text-align: right;
  font-weight: bold;
}
.border{
    border:1px solid #000;    
}
.overflow{
  overflow:   auto;
}
.techs{
  width:70%;
}
.super{
  width:30%;
}
.technicians-item{
  float:left;

}
.underline{
  text-decoration:  underline;
}

.checks-item{
  float:left;
   width:50%;
}

.p5{
  padding:5px;
}
.mt15{
    margin-top: 15px
}
.signs-item-left{
  float:left;
  width: 50%;
}
.signs-item-right{
  float:right;
  width: 40%;
  text-align: left;
  }
  textarea{
    color:rgb(67, 142, 255);
    border:none;
    resize:none;
  }
  input{
     border:none;
     color:red;
     font-weight: bold;
  } 
  #tel-fields,
  #voip-fields{
    display:  none;
  }
.tech-title-tr,
.tech-tasks-category{
  width:50%;
  float:left;
  margin-bottom:  5px;
}
.signs{
  overflow: auto;
}
section{
  overflow: auto;
}
.checks {
    min-height: 300px;
}
#ticket_nr{
    width: 80px;
}
.signs-item-left img {
    height: 80px;
}
.full{
  clear:both;
}

@media print
{    

    button, #category, #technician
    {
        display: none !important;
    }
    .error,
    #reset{
      display: none;
    }
     
}
    </style>

  
  </head>
  <body class=''>
  <img src="<?= base_url(); ?>assets/images/letterHead.png" alt="">
  <?php echo validation_errors('<div style="color:red" class="error">', '</div>'); ?>
  <div class='header'style="">
  <?php   
    $type = $this->session->type;

    if($type == 'Πληροφορική'){
        $type_show = 'ΠΛΗΡΟΦΟΡΙΚΗΣ';
    }else if($type == 'Τηλεφωνία'){
        $type_show = 'ΤΗΛΕΠΙΚΟΙΝΩΝΙΩΝ';
    }else if($type == 'VOIP'){
        $type_show = 'VOIP';
    }else if($type == 'Copiers'){
        $type_show = 'COPIERS';
    }else if($type == 'UPS'){
        $type_show = 'UPS';
    }
   ?>
    ΔΕΛΤΙΟ ΤΕΧΝΙΚΗΣ ΕΞΥΠΗΡΕΤΗΣΗΣ – ΥΠΗΡΕΣΙΕΣ <?= $type_show; ?>
  </div>
  <form action='' method='post'>
  <div class='date-number overflow'>
    <div style="" class='date-number-item'> 
      <div class='date-number-subitem'>ΗΜΕΡΟΜΗΝΙΑ</div>
      <div class='date-number-subitem date bold'>
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
      </div>
    </div>
    <div style="" class='date-number-item time'> <span class='bold'>ΩΡΕΣ:</span> 9:30 – 16:30</div>
    <?php 
        if($this->session->userdata("type")=="UPS")
        {
          if(!isset($_POST ['ticket_nr']))
          {
           $_POST ['ticket_nr'] = intval(Modules::run('keelpno/getLastDailyTickets')['UPS'][0]->ticket_nr)+1;
          }
        }
     ?>
    <div style="" class='date-number-item id-number'> No  <input type="number" id='ticket_nr' value="<?= set_value('ticket_nr'); ?>" name="ticket_nr"></div>
  </div>
 
 <div class='client-description f13 border p5'>
   <?php  
    if($this->session->client=="vari"){

    ?>   
  ΠΕΛΑΤΗΣ: Κ.Ε.Δ.Υ. - ΚΕ.ΕΛ.Π.ΝΟ. Ν.Π.Ι.Δ.   - 
  <span class='bold blue'>  ΔΙΕΥΘΥΝΣΗ: ΦΛΕΜΙΝΓΚ 34 ΒΑΡΗ ΚΟΡΩΠΙΟΥ  </span><br>
  Δ.Ο.Υ.: ΙΑ’ ΑΘΗΝΩΝ   Α.Φ.Μ.: 090193594  ΤΗΛ.: 210.88.99.000  <br>
  ΥΠΕΥΘΥΝΟΣ: Κος ΤΣΕΚΑΣ ΧΡΗΣΤΟΣ  ΤΟΠΟΣ ΕΡΓΑΣΙΩΝ: ΚΤΗΡΙΟ ΚΕΔΥ ΒΑΡΗΣ<br> 
  
  <?php   
    }elseif ($this->session->client=="marousi") {
     ?>
<span class='bold blue'>  ΔΙΕΥΘΥΝΣΗ: ΑΓΡΑΦΩΝ 3-5 ΜΑΡΟΥΣΙ  </span><br>
  Δ.Ο.Υ.: ΙΑ’ ΑΘΗΝΩΝ   Α.Φ.Μ.: 090193594  ΤΗΛ.: 210.52.12.190  FAX.: 210.52.12.191<br>
  ΥΠΕΥΘΥΝΟΣ: 
  <?php
  if($this->session->type=='UPS')
    echo ' Κος ΛΙΑΣΚΟΣ  ';
  else if($this->session->type=='Copiers')
    echo ' Κα ΣΠΗΛΙΟΠΟΥΛΟΥ  ';
  else
    echo ' Κος ΠΑΡΙΣΣΗΣ  ';
  ?>
  ΤΟΠΟΣ ΕΡΓΑΣΙΩΝ ΚΤΙΡΙΑΚΕΣ ΕΓΚΑΤΑΣΤΑΣΕΙΣ ΚΕ.ΕΛ.Π.ΝΟ. Ν.Π.Ι.Δ.<br> 
  <span class="bold">ΑΓΡΑΦΩΝ 3-5 ΜΑΡΟΥΣΙ  Υπόγειο, 1ος, 2ος, 3ος, 4ος. Αβέρωφ 10 Αθήνα 
</span>
     <?php
    }
   ?>
 </div>
 <div class='technicians f13 overflow'>
   <div class="technicians-item techs"> 
   <?php  
    if($this->session->client == 'marousi' && ($this->session->type=='Τηλεφωνία' || $this->session->type=='VOIP')){
      $this->session->user = 'Άλεξ';
      echo "ΤΕΧΝΙΚΟΣ: ΑΛΕΞΑΝΔΡΟΣ ΤΙΣΑΚΟΒ";
    }else{
      $user = $this->session->user;
      switch (  $user ) {
        
        case 'Γιώργος':
           echo "ΤΕΧΝΙΚΟΣ: ΓΕΩΡΓΙΟΣ ΜΑΡΑΤΟΣ";
          break;
        case 'Τάκης':
           echo "ΤΕΧΝΙΚΟΣ: ΠΑΝΑΓΙΩΤΗΣ ΚΛΗΜΗΣ";
          break;
        case 'Θανάσης':
           echo "ΤΕΧΝΙΚΟΣ: ΘΑΝΑΣΗΣ ΧΟΥΙΔΗΣ";
          break;
          case 'Γιάννης':
           echo "ΤΕΧΝΙΚΟΣ: ΓΙΑ ΤΗΝ <span style='font-size:11px';>EPSILON TELEDATA</span> ΕΤΑΙΡΙΑ ANDOR Κος ΓΙΑΝΝΗΣ ΖΟΥΡΑΣ";
          break;
        default:
           echo "ΤΕΧΝΙΚΟΣ: ΑΛΕΞΑΝΔΡΟΣ ΤΙΣΑΚΟΒ";
          break;
      }
      
    }
    ?>
  
   </div>
   <div class="technicians-item super">ΕΠΙΒΛΕΨΗ:ΕΥΑΓ. ΜΟΥΡΓΕΛΑΣ </div>
 </div>
 <div class='checks  f11 border p5 overflow'>
 <div style="clear:both;overflow:auto;">
   <div class=' tech-title-tr bold underline'>ΕΡΓΑΣΙΕΣ ΤΕΧΝΙΚΟΥ : </div>
   
    <input type="hidden" name="client" value="<?= $this->session->client; ?>"> 
    <input type="hidden" name="technician" value="<?= $this->session->user; ?>"> 
    <input type="hidden" name="category" value="<?= $this->session->type; ?>"> 
     
   </div>
   <div class='cat-tabs' id='pc-fields' > 
   <section id='servers'>
   <div class='  bold underline'>Servers : </div>
     <?php
     $i = 1; 
   foreach ($categories->result() as $category) {
    if($category->category=='server'){
      if($i==1){
        $i++;
        $class='left';
      }else{
        $i=1;
        $class='right';
      }
       ?>
       <div  class='checks-item checks-item-<?= $class; ?>'>
      <input type="checkbox" name='tasks_lists[]' <?= set_checkbox('tasks_lists', $category->id); ?> value="<?= $category->id ?>"> <?= $category->name ?>
      </div>
      <?php }
   }

    ?>
    </section>
    <section  id='network'>
    <div class=' mt15 bold underline'>Δίκτυο : </div>
     <?php
     if($this->session->client=='marousi'){

     
     $i = 1; 
     $patchpanels = array();
     $patchcords = array();
     $switches = array();
      foreach ($categories->result() as $category) {

    if($category->category=='network' || $category->category=='network_marousi'){
      if( strpos( $category->name, 'SWITCHES' ) !== false ){
        $category->name = str_replace('SWITCHES', '', $category->name);
        $switches[]=$category;
        continue;
      }elseif(strpos( $category->name, 'PATCH PANELS' ) !== false){
        $category->name = str_replace('PATCH PANELS', '', $category->name);
        $patchpanels[]=$category;
        continue;
      }elseif(strpos( $category->name, 'PATCH CORDS' ) !== false){
        $category->name = str_replace('PATCH CORDS', '', $category->name);
        $patchcords[]=$category;
        continue;
      }
      if($i==1){
        $i++;
        $class='left';
      }else{
        $i=1;
        $class='right';
      }
       ?>
       <div  class='checks-item checks-item-<?= $class; ?>'>
      <input type="checkbox" <?= set_checkbox('tasks_lists', $category->id); ?> name='tasks_lists[]' value="<?= $category->id ?>"> <?= $category->name ?>
      </div>
      <?php }
   }
   ?>
  <div  class='checks-item checks-item-<?= $class; ?>'>
      ΕΛΕΓΧΟΣ/ΕΡΓΑΣΙΕΣ SWITCHES: <br>
   <?php
      foreach ($switches as $category) {
        
        ?>
       
      <input type="checkbox" <?= set_checkbox('tasks_lists', $category->id); ?> name='tasks_lists[]' value="<?= $category->id ?>"> <?= $category->name ?>
     
      <?php
      }
      if($i==1){
        $i++;
        $class='left';
      }else{
        $i=1;
        $class='right';
      }
    ?>
     </div>
      <div  class='checks-item checks-item-<?= $class; ?>'>
       ΕΛΕΓΧΟΣ/ΕΡΓΑΣΙΕΣ PATCH PANELS: <br>
   <?php
      foreach ($patchpanels as $category) {

   ?>
       
      <input type="checkbox" <?= set_checkbox('tasks_lists', $category->id); ?> name='tasks_lists[]' value="<?= $category->id ?>"> <?= $category->name ?>
     
      <?php
      }
      if($i==1){
        $i++;
        $class='left';
      }else{
        $i=1;
        $class='right';
      }
    ?>
     </div>
      <div  class='checks-item checks-item-<?= $class; ?>'>
      ΕΛΕΓΧΟΣ/ΕΡΓΑΣΙΕΣ PATCH CORDS: <br>
   <?php
      foreach ($patchcords as $category) {
        
        ?>
       
      <input type="checkbox" <?= set_checkbox('tasks_lists', $category->id); ?> name='tasks_lists[]' value="<?= $category->id ?>"> <?= $category->name ?>
     
      <?php
      }
      if($i==1){
        $i++;
        $class='left';
      }else{
        $i=1;
        $class='right';
      }
    ?>
     </div>
     <?php }else if($this->session->client=='vari'){ 
   
     $i = 1; 
     $patchpanels = array();
     $patchcords = array();
     $switches = array();
      foreach ($categories->result() as $category) {

    if($category->category=='network' || $category->category=='network_vari'){
      if( strpos( $category->name, 'SWITCHES' ) !== false ){
        $category->name = str_replace('SWITCHES', '', $category->name);
        $category->name = str_replace('Αριστερό Κτίριο', 'ΑΚ', $category->name);
        $category->name = str_replace('Δεξί Κτίριο', 'ΔΚ', $category->name);
        $category->name = str_replace('Ισόγειο', 'εισ.', $category->name);
        $switches[]=$category;
        continue;
      }elseif(strpos( $category->name, 'PATCH PANELS' ) !== false){
        $category->name = str_replace('PATCH PANELS', '', $category->name);
        $category->name = str_replace('Αριστερό Κτίριο', 'ΑΚ', $category->name);
        $category->name = str_replace('Δεξί Κτίριο', 'ΔΚ', $category->name);
         $category->name = str_replace('Ισόγειο', 'εισ.', $category->name);
        $patchpanels[]=$category;
        continue;
      }elseif(strpos( $category->name, 'PATCH CORDS' ) !== false){
        $category->name = str_replace('PATCH CORDS', '', $category->name);
        $category->name = str_replace('Αριστερό Κτίριο', 'ΑΚ', $category->name);
        $category->name = str_replace('Δεξί Κτίριο', 'ΔΚ', $category->name);
         $category->name = str_replace('Ισόγειο', 'εισ.', $category->name);
        $patchcords[]=$category;
        continue;
      }
      if($i==1){
        $i++;
        $class='left';
      }else{
        $i=1;
        $class='right';
      }
       ?>
       <div  class='checks-item checks-item-<?= $class; ?>'>
      <input type="checkbox" <?= set_checkbox('tasks_lists', $category->id); ?> name='tasks_lists[]' value="<?= $category->id ?>"> <?= $category->name ?>
      </div>
      <?php }
   }
   ?>
  <div  class='full'>
  <hr>
      ΕΛΕΓΧΟΣ/ΕΡΓΑΣΙΕΣ SWITCHES: 
        
   <?php
      foreach ($switches as $category) {
        
        ?>
       
      <input type="checkbox" <?= set_checkbox('tasks_lists', $category->id); ?> name='tasks_lists[]' value="<?= $category->id ?>"> <?= $category->name ?>
     
      <?php
      }
      if($i==1){
        $i++;
        $class='left';
      }else{
        $i=1;
        $class='right';
      }
    ?>
     </div>
      <div  class='full'>
  <hr>
       ΕΛΕΓΧΟΣ/ΕΡΓΑΣΙΕΣ PATCH PANELS: 
   <?php
      foreach ($patchpanels as $category) {

   ?>
       
      <input type="checkbox" <?= set_checkbox('tasks_lists', $category->id); ?> name='tasks_lists[]' value="<?= $category->id ?>"> <?= $category->name ?>
     
      <?php
      }
      if($i==1){
        $i++;
        $class='left';
      }else{
        $i=1;
        $class='right';
      }
    ?>
     </div>
     <hr>
      <div  class='full'>
      ΕΛΕΓΧΟΣ/ΕΡΓΑΣΙΕΣ PATCH CORDS: 
   <?php
      foreach ($patchcords as $category) {
        
        ?>
       
      <input type="checkbox" <?= set_checkbox('tasks_lists', $category->id); ?> name='tasks_lists[]' value="<?= $category->id ?>"> <?= $category->name ?>
     
      <?php
      }
      if($i==1){
        $i++;
        $class='left';
      }else{
        $i=1;
        $class='right';
      }
    ?>
     </div>
     <?php } ?>
    </section>
     <section id='pc'>
    <div class='mt15  bold underline'>Υπολογιστές / Laptop : </div>
     <?php
     $i = 1; 
      foreach ($categories->result() as $category) {
          if($category->category=='pc'){
            if($i==1){
              $i++;
              $class='left';
            }else{
              $i=1;
              $class='right';
            }
       ?>
       <div  class='checks-item checks-item-<?= $class; ?>'>
      <input type="checkbox" <?= set_checkbox('tasks_lists', $category->id); ?> name='tasks_lists[]' value="<?= $category->id ?>"> <?= $category->name ?>
      </div>
      <?php }
   }

    ?>
    </section>
       <section id='printer'>
    <div class=' mt15 bold underline'>Εκτυπωτές / Πολυμηχανήματα / Φωτοτυπικά  </div>
     <?php
     $i = 1; 
      foreach ($categories->result() as $category) {
    if($category->category=='printer'){
      if($i==1){
        $i++;
        $class='left';
      }else{
        $i=1;
        $class='right';
      }
       ?>
       <div  class='checks-item checks-item-<?= $class; ?>'>
      <input type="checkbox" <?= set_checkbox('tasks_lists', $category->id); ?> name='tasks_lists[]' value="<?= $category->id ?>"> <?= $category->name ?>
      </div>
      <?php }
   }

    ?>
    </section>
   
    </div>
    <div class='cat-tabs' id='tel-fields'>
    <section id='telehpony'>
   <div class='  bold underline'>Τελεφωνία : </div>
     <?php
     $i = 1; 
     $client_category = 'telephony_'.$this->session->userdata('client');
   foreach ($categories->result() as $category) {

    if($category->category=='telephony' || $category->category==$client_category){
      if($i==1){
        $i++;
        $class='left';
      }else{
        $i=1;
        $class='right';
      }
       ?>
       <div  class='checks-item checks-item-<?= $class; ?>'>
      <input type="checkbox" <?= set_checkbox('tasks_lists', $category->id); ?> name='tasks_lists[]' value="<?= $category->id ?>"> <?= $category->name ?>
      </div>
      <?php }
   }

    ?>
    </section>
 
</div>
<div class='cat-tabs' id='voip-fields'>
  <section id='voip'>
   <div class='  bold underline'>VOIP (PRI: 2106863200 και 2105212054) </div> <br>
     <?php
     $i = 1; 
   foreach ($categories->result() as $category) {
    if($category->category=='voip'){
      if($i==1){
        $i++;
        $class='left';
      }else{
        $i=1;
        $class='right';
      }
       ?>
      <div  class='checks-item checks-item-<?= $class; ?>'>
         <input type="checkbox" <?= set_checkbox('tasks_lists', $category->id); ?> name='tasks_lists[]' value="<?= $category->id ?>"> <?= $category->name ?>
      </div>
      <?php }
   }

    ?>
    </section>
</div>
<!-- Copiers-->
<div class='cat-tabs' id='copiers-fields'>
  <section id='copiers'>
   <div class='  bold underline'>Φωτοτυπικά: </div> <br>
     <?php
     $i = 1; 
     $client_category = 'copiers_'.$this->session->userdata('client');
   foreach ($categories->result() as $category) {
    if($category->category==$client_category){
      if($i==1){
        $i++;
        $class='left';
      }else{
        $i=1;
        $class='right';
      }
       ?>
      <div  class='checks-item checks-item-<?= $class; ?>'>
         <input type="checkbox" <?= set_checkbox('tasks_lists', $category->id); ?> name='tasks_lists[]' value="<?= $category->id ?>"> <?= $category->name ?>
      </div>
      <?php }
   }
   ?>
   </section>
   <section id='copiers_tasks'>
   <div class='mt15  bold underline'>Εργασίες: </div> <br>
   <?php
   foreach ($categories->result() as $category) {
     if($category->category=='copiers' ){
        if($i==1){
          $i++;
          $class='left';
        }else{
          $i=1;
          $class='right';
        }
         ?>
        <div  class='checks-item checks-item-<?= $class; ?>'>
           <input type="checkbox" <?= set_checkbox('tasks_lists', $category->id); ?> name='tasks_lists[]' value="<?= $category->id ?>"> <?= $category->name ?>
        </div>
        <?php }
      }
    ?>
    </section>
</div>
<!-- UPS -->
<div class='cat-tabs' id='ups-fields'>
  <section id='ups'>
     <?php
     $i = 1; 
    // $client_category = 'ups_'.$this->session->userdata('client');
   foreach ($categories->result() as $category) {
    if($category->category=='UPS' ){
      if($i==1){
        $i++;
        $class='left';
      }else{
        $i=1;
        $class='right';
      }
       ?>
      <div  class='checks-item checks-item-<?= $class; ?>'>
         <input type="checkbox" <?= set_checkbox('tasks_lists', $category->id); ?> name='tasks_lists[]' value="<?= $category->id ?>"> <?= $category->name ?>
      </div>
      <?php }
   }
   ?>
   </section>
</div>
 </div>
 
 <div class='client-text border mt15 p5 overflow'>
   <div class='client-text-heading'>ΣΧΟΛΙΑ – ΠΑΡΑΤΗΡΗΣΕΙΣ ΠΕΛΑΤΗ  </div>
   <textarea class='f13' name="customer_comments" id="" style='width:99%' rows="1"><?= set_value('customer_comments'); ?></textarea>
 </div>
 <div class='tech-text border mt15 p5 overflow'>
   <div class='tech-text-heading'>ΑΛΛΕΣ ΕΡΓΑΣΙΕΣ – ΠΑΡΑΤΗΡΗΣΕΙΣ:  </div>
   <textarea class='f13' name="technician_comments" id="" style='width:99%' rows="7"><?= set_value('technician_comments'); ?></textarea>
    

 </div>
 <div class='signs mt15'>
    <?php
    if ($this->session->type=="UPS") {
        ?>
        <div style="text-align:center" class='signs-item signs-item-left f13'>ΥΠΟΓΡΑΦΗ ΓΙΑ ΤΗΝ EPSILON TELEDATA
        <?php
      } else {
        ?>
        <div style="text-align:center" class='signs-item signs-item-left'>ΥΠΟΓΡΑΦΗ ΤΕΧΝΙΚΟΥ <br>
        <?php
      }

    if ($this->session->user ==  'Άλεξ' ) 
      echo '<img src="'. base_url().'assets/images/tisakov.jpg" alt="">';
    ?>
   
    </div>
    <?php
      if ($this->input->post('daily')=="1") {
        if($this->session->type=="UPS"){
          ?>
              <div class='signs-item signs-item-right f13' style="text-align:center">ΥΠΟΓΡΑΦΗ ΟΝΟΜΑ ΥΠΕΥΘΥΝΟΥ ΠΕΛΑΤΗ<br> <span class='f11'>(ΓΙΑ ΤΗΝ ΑΠΟΔΟΧΗ ΥΛΟΠΟΙΗΣΗΣ ΤΗΣ ΥΠΗΡΕΣΙΑΣ)</span></div>
          <?php
        }else{
          ?>
        <div class='signs-item signs-item-right' style="text-align:center">ΥΠΟΓΡΑΦΗ ΠΕΛΑΤΗ  </div>

          <?php
        }
        ?>
        
        <?php
      } else {
        ?>
        <div class='signs-item signs-item-right' style="text-align:center">ΥΠΟΓΡΑΦΗ ΥΠΑΛΛΗΛΟΥ <?= ($this->session->client=="marousi")?"ΚΕΕΛΠΝΟ":"ΚΕΔΥ"; ?> <br> <span class='f11'>(ΓΙΑ ΤΗΝ ΑΠΟΔΟΧΗ ΠΟΙΟΤΗΤΑΣ ΤΗΣ ΠΑΡΑΣΧΕΘΕΙΣΑΣ ΥΠΗΡΕΣΙΑΣ)</span></div>
        <?php
      }
      
    ?>
    
 </div>
 <button style="clear:both;display:inline-block" type='submit'>Save</button>
 </form>
 <form id="reset" action="<?= base_url(); ?>keelpno/reset" method="post">
 <br> <br> <br> <br> <br>
    <input id="" type="submit" value="Go Home">
 </form>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
<script>
$(document).ready(function(){

  showCat("<?= $this->session->type; ?>");

})
  function showCat(a){
      $('.cat-tabs').hide();
     
      if(a=='Πληροφορική'){
        var b = 'pc-fields';
      }else if(a=='Τηλεφωνία'){
        var b = 'tel-fields';
      }else if(a=='VOIP'){
        var b = 'voip-fields';
      }else if(a=='Copiers'){
        var b = 'copiers-fields';
      }else if(a=='UPS'){
        var b = 'ups-fields';
      }

      $('#'+b).show();
      
  }
</script>
  </body>
</html>

<?php 
$this->session->unset_userdata('type'); ?>