<!DOCTYPE html>
<html>
  <head>
    <style>
    body {
        height: 842px;
        width: 595px;
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
  #tel-fields,
  #voip-fields{
    display:  none;
  }
.tech-title-tr,
.tech-tasks-category{
  width:50%;
  float:left;
  margin-bottom:  15px;
}
.signs{
  overflow: auto;
}
@media print
{    
    button,select
    {
        display: none !important;
    }
}
    </style>

  
  </head>
  <body class=''>
  <img src="assets/images/letterHead.png" alt="">
  <div class='header'style="">
    ΔΕΛΤΙΟ ΤΕΧΝΙΚΗΣ ΕΞΥΠΗΡΕΤΗΣΗΣ – ΥΠΗΡΕΣΙΕΣ ΠΛΗΡΟΦΟΡΙΚΗΣ
  </div>
  <form action='' method='post'>
  <div class='date-number overflow'>
    <div style="" class='date-number-item'> 
      <div class='date-number-subitem'>ΗΜΕΡΟΜΗΝΙΑ</div>
      <div class='date-number-subitem date bold'>x - x - 2017</div>
    </div>
    <div style="" class='date-number-item time'> <span class='bold'>ΩΡΕΣ:</span> 9:30 – 16:30</div>
    <div style="" class='date-number-item id-number'> No  85142x</div>
  </div>
 
 <div class='client-description f13 border'>
      
  ΠΕΛΑΤΗΣ: ΚΕ.ΕΛ.Π.ΝΟ. Ν.Π.Ι.Δ.   <br>  
  <span class='bold blue'>  ΔΙΕΥΘΥΝΣΗ: ΑΓΡΑΦΩΝ 3-5 ΜΑΡΟΥΣΙ  </span><br>
  Δ.Ο.Υ.: ΙΑ’ ΑΘΗΝΩΝ   Α.Φ.Μ.: 090193594  ΤΗΛ.: 210.52.12.190  FAX.: 210.52.12.191<br>
  ΥΠΕΥΘΥΝΟΣ: Κος ΠΑΡΙΣΣΗΣ  ΤΟΠΟΣ ΕΡΓΑΣΙΩΝ ΚΤΙΡΙΑΚΕΣ ΕΓΚΑΤΑΣΤΑΣΕΙΣ ΚΕ.ΕΛ.Π.ΝΟ. Ν.Π.Ι.Δ.<br> 
  <span class="bold">ΑΓΡΑΦΩΝ 3-5 ΜΑΡΟΥΣΙ  Υπόγειο, 1ος, 2ος, 3ος, 4ος. Αβέρωφ 10 Αθήνα 
  </span>

 </div>
 <div class='technicians f13 overflow'>
   <div class="technicians-item techs">ΤΕΧΝΙΚΟΙ: ΓΙΩΡΓ. ΜΑΡΑΤΟΣ – ΑΛΕΞ. ΤΙΣΑΚΟΒ – ΠΑΝΑΓ. ΚΛΗΜΗΣ           </div>
   <div class="technicians-item super">ΕΠΙΒΛΕΨΗ:ΕΥΑΓ. ΜΟΥΡΓΕΛΑΣ </div>
 </div>
 <div class='checks  f11 border p5 overflow'>
 <div style="clear:both;overflow:auto;">
   <div class=' tech-title-tr bold underline'>ΕΡΓΑΣΙΕΣ ΤΕΧΝΙΚΟΥ : </div>
    <div class="tech-tasks-category"> <select name="category" id="category" >
     <option value="Πληροφορική" default>Πληροφορική</option>
     <option  value="Τηλεφωνία">Τηλεφωνία</option>
     <option  value="VOIP">VOIP</option>
   </select> </div>
   </div>
   <div class='cat-tabs' id='pc-fields' > 
   <div  class='checks-item checks-item-left'>
<input type="checkbox" name='tasks_lists[]' value="task1"> ΠΡΟΓΡΑΜΜΑΤΙΣΜΟΙ ΚΕΝΤΡΙΚΩΝ SERVERS<br>
<input type="checkbox" name='tasks_lists[]' value="task2"> ΤΡΟΠΟΠΟΙΗΣΕΙΣ – ΑΝΑΒΑΘΜΙΣΕΙΣ – ΕΛΕΓΧΟΣ & ΕΡΓΑΣΙΕΣ ΣΤΟ SOFTWARE ΤΩΝ SERVERS<br>
<input type="checkbox" name='tasks_lists[]' value="task3"> SERVICE HARDWARE ΤΩΝ SERVERS  <br>
<input type="checkbox" name='tasks_lists[]' value="task4"> ΠΡΟΓΡΑΜΜΑΤΙΣΜΟΙ SERVER ΛΟΓΙΣΤΗΡΙΟΥ  <br>
<input type="checkbox" name='tasks_lists[]' value="task5"> ΕΛΕΓΧΟΣ & ΕΡΓΑΣΙΕΣ ΣΤΟ SOFTWARE SERVER ΛΟΓΙΣΤΗΡΙΟΥ    <br>
<input type="checkbox" name='tasks_lists[]' value="task6"> ΕΛΕΓΧΟΣ UPS <br>
<input type="checkbox" name='tasks_lists[]' value="task7"> BACK UP <br>
<input type="checkbox" name='tasks_lists[]' value="task8"> ΕΛΕΓΧΟΣ INTERNET  <br>
<input type="checkbox" name='tasks_lists[]' value="task9"> ΕΛΕΓΧΟΣ & ΕΡΓΑΣΙΕΣ ΑΠΟΚΑΤΑΣΤΑΣΗΣ EMAILS <br>
<input type="checkbox" name='tasks_lists[]' value="task10"> ΕΚΤΡΟΠΗ ΓΡΑΜΜΩΝ INTERNET      <br>
<input type="checkbox" name='tasks_lists[]' value="task11"> ΣΥΓΧΡΟΝΙΣΜΟΙ ROUTER ΑΝΤΙΜΕΤΩΠΙΣΗ - ΔΗΛΩΣΗ ΒΛΑΒΗΣ ΣΕ ΕΥΘΕΙΕΣ <br>
<input type="checkbox" name='tasks_lists[]' value="task12"> ΓΡΑΜΜΕΣ ΟΤΕ ΓΙΑ ROUTER      <br>
<input type="checkbox" name='tasks_lists[]' value="task13"> ΥΠΗΡΕΣΙΕΣ ΗΛΕΚΤΡΟΝΙΚΟΥ ΤΑΧΥΔΡΟΜΕΙΟΥ   <br>
<input type="checkbox" name='tasks_lists[]' value="task14"> SERVICE – ΑΝΑΒΑΘΜΙΣΗ Η/Υ      <br>
<input type="checkbox" name='tasks_lists[]' value="task15"> ΕΓΚΑΤΑΣΤΑΣΗ – ΑΝΑΒΑΘΜΙΣΗ SOFTWARE ΣΕ Η/Υ<br>
<input type="checkbox" name='tasks_lists[]' value="task16"> ΕΓΚΑΤΑΣΤΑΣΗ ΝΕΟΥ H/Y<br>
<input type="checkbox" name='tasks_lists[]' value="task17"> SERVICE – ΑΝΑΒΑΘΜΙΣΗ SOFTWARE NOTEBOOK<br>
<input type="checkbox" name='tasks_lists[]' value="task18"> SERVICE ΠΕΡΙΦΕΡΕΙΑΚΩΝ ΟΘΟΝΩΝ<br>
   </div>

   <div class='checks-item checks-item-right'>

<input type="checkbox" name='tasks_lists[]' value="task19"> SERVICE ΠΕΡΙΦΕΡΕΙΑΚΩΝ ΕΚΤΥΠΩΤΩΝ<br>
<input type="checkbox" name='tasks_lists[]' value="task20"> ΕΓΚΑΤΑΣΤΑΣΗ ΕΚΤΥΠΩΤΗ / ΠΟΛΥΜΗΧΑΝΗΜΑΤΟΣ  <br>
<input type="checkbox" name='tasks_lists[]' value="task21">ΤΟΠΟΘΕΤΗΣΗ ΝΕΟΥ ΕΚΤΥΠΩΤΗ / ΠΟΛΥΜΗΧΑΝΗΜΑΤΟΣ<br>
<input type="checkbox" name='tasks_lists[]' value="task22"> ΕΡΓΑΣΙΕΣ ΕΛΕΓΧΟΥ ΔΙΚΤΥΑΚΟΥ SOFTWARE <br>
<input type="checkbox" name='tasks_lists[]' value="task23"> ΕΠΕΞΕΡΓΑΣΙΑ ΕΓΓΡΑΦΩΝ  <br>
<input type="checkbox" name='tasks_lists[]' value="task24"> ΕΡΓΑΣΙΕΣ ΣΕ TABLET/KINHTO <br>
<input type="checkbox" name='tasks_lists[]' value="task25"> ΠΡΟΓΡΑΜΜΑΤΙΣΜΟΣ ROUTERS <br>
<input type="checkbox" name='tasks_lists[]' value="task26"> ΠΡΟΓΡΑΜΜΑΤΙΣΜΟΣ FIREBOX <br>
<input type="checkbox" name='tasks_lists[]' value="task27"> ΑΝΑΒΑΘΜΙΣΕΙΣ ΠΡΟΓΡΑΜΜΑΤΩΝ MICROSOFT <br>
SWITCHES ΚΑΤΑΝΕΜΗΤΩΝ:
  SERVER ROOM<br> <input type="checkbox" name='tasks_lists[]' value="task28">  Υπ. <input type="checkbox" name='tasks_lists[]' value="task29">  1ου <input type="checkbox" name='tasks_lists[]' value="task30">  2ου <input type="checkbox" name='tasks_lists[]' value="task31">  3ου <input type="checkbox" name='tasks_lists[]' value="task32">  4ου <input type="checkbox" name='tasks_lists[]' value="task33"> <br>
ΑΠΟΚATAΣΤΑΣΗ ΒΛΑΒΗΣ PATCH PANELS:
  SERVER ROOM<br> <input type="checkbox" name='tasks_lists[]' value="task34">  Υπ. <input type="checkbox" name='tasks_lists[]' value="task35">  1ου <input type="checkbox" name='tasks_lists[]' value="task36">  2ου <input type="checkbox" name='tasks_lists[]' value="task37">  3ου <input type="checkbox" name='tasks_lists[]' value="task38">  4ου <input type="checkbox" name='tasks_lists[]' value="task39"> <br>
ΕΛΕΓΧΟΣ – ΑΠΟΚATAΣΤΑΣΗ ΚΑΛΩΔΙΩΣΕΩΝ PATCH CORDS:
  SERVER ROOM<br> <input type="checkbox" name='tasks_lists[]' value="task40">  Υπ. <input type="checkbox" name='tasks_lists[]' value="task41">  1ου <input type="checkbox" name='tasks_lists[]' value="task42">  2ου <input type="checkbox" name='tasks_lists[]' value="task43">  3ου <input type="checkbox" name='tasks_lists[]' value="task44">  4ου <input type="checkbox" name='tasks_lists[]' value="task45"> <br>
<input type="checkbox" name='tasks_lists[]' value="task46"> ΕΓΚΑΤΑΣΤΑΣΗ ΠΡΟΣΩΡΙΝΟΥ ΔΙΚΤΥΟΥ & INTERNET  <br>
<input type="checkbox" name='tasks_lists[]' value="task47"> ΚΑΤΑΓΡΑΦΗ ΕΞ/ΣΜΟΥ ΓΙΑ ΚΕΝΤΡΙΚΗ ΒΑΣΗ ΔΕΔΟΜΕΝΩΝ <br>
<input type="checkbox" name='tasks_lists[]' value="task48"> ΕΡΓΑΣΙΕΣ ΣΤΟ Σ.Κ.Α.Ε. ΑΒΕΡΩΦ 10 ΑΘΗΝΑ  <br>
<input type="checkbox" name='tasks_lists[]' value="task49"> ΕΡΓΑΣΙΕΣ ΚΕ.ΕΛ.Π.ΝΟ. Γ’ ΣΕΠΤΕΜΒΡΙΟΥ 56 ΑΘΗΝΑ <br>

   </div>
    </div>
    <div class='cat-tabs' id='tel-fields'>
 <div class='checks-item checks-item-left'>
      <input type="checkbox" name='tasks_lists[]' value="task50"> ΠΡΟΓΡΑΜΜΑΤΙΣΜΟΣ ΕΣΩΤΕΡΙΚΟΥ ΤΗΛΕΦΩΝΟΥ<br>
      <input type="checkbox" name='tasks_lists[]' value="task51"> ΑΝΤΙΚΑΤΑΣΤΑΣΗ ΕΣΩΤΕΡΙΚΟΥ ΤΗΛΕΦΩΝΟΥ<br>
      <input type="checkbox" name='tasks_lists[]' value="task52"> ΜΕΤΑΦΟΡΑ ΕΣΩΤΕΡΙΚΟΥ ΤΗΛΕΦΩΝΟΥ<br>
      
      </div>
      <div class='checks-item checks-item-right'>
      <input type="checkbox" name='tasks_lists[]' value="task53"> ΠΡΟΓΡΑΜΜΑΤΙΣΜΟΣ FAX<br>
      <input type="checkbox" name='tasks_lists[]' value="task54"> SERVICE FAX<br>
      </div>
</div>
<div class='cat-tabs' id='voip-fields'>
 <div class='checks-item checks-item-left'>
      <input type="checkbox" name='tasks_lists[]' value="task55"> ΠΡΟΓΡΑΜΜΑΤΙΣΜΟΣ ΕΣΩΤΕΡΙΚΟΥ ΤΗΛΕΦΩΝΟΥ<br>
      <input type="checkbox" name='tasks_lists[]' value="task56"> ΑΝΤΙΚΑΤΑΣΤΑΣΗ ΕΣΩΤΕΡΙΚΟΥ ΤΗΛΕΦΩΝΟΥ<br>
      <input type="checkbox" name='tasks_lists[]' value="task57"> ΜΕΤΑΦΟΡΑ ΕΣΩΤΕΡΙΚΟΥ ΤΗΛΕΦΩΝΟΥ<br>
      
      </div>
      <div class='checks-item checks-item-right'>
      <input type="checkbox" name='tasks_lists[]' value="task58"> ΕΛΕΓΧΟΣ BACKUP<br>
      <input type="checkbox" name='tasks_lists[]' value="task59"> ΠΡΟΓΡΑΜΜΑΤΙΣΜΟΣ FAX<br>
      <input type="checkbox" name='tasks_lists[]' value="task60"> SERVICE FAX<br>
      </div>
</div>
 </div>
 
 <div class='client-text border mt15 p5 overflow'>
   <div class='client-text-heading'>ΣΧΟΛΙΑ – ΠΑΡΑΤΗΡΗΣΕΙΣ ΠΕΛΑΤΗ  </div>
   <textarea class='f13' name="" id="" style='width:99%' rows="3"></textarea>
 </div>
 <div class='tech-text border mt15 p5 overflow'>
   <div class='tech-text-heading'>ΑΛΛΕΣ ΕΡΓΑΣΙΕΣ – ΠΑΡΑΤΗΡΗΣΕΙΣ:  </div>
   <textarea class='f13' name="" id="" style='width:99%' rows="8"></textarea>
    

 </div>
 <div class='signs mt15'>
    <div class='signs-item signs-item-left'>ΥΠΟΓΡΑΦΗ ΤΕΧΝΙΚΟΥ</div>
    <div class='signs-item signs-item-right'>ΥΠΟΓΡΑΦΗ ΠΕΛΑΤΗ</div>
 </div>
 <button style="clear:both;display:inline-block" type='submit'>Save</button>
 </form>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
<script>
$(document).ready(function(){

  $('#category').on('change',function(){

    var a = $(this).val();
     showCat(a);
      });

})
  function showCat(a){
      $('.cat-tabs').hide();
     
      if(a=='Πληροφορική'){
        var b = 'pc-fields';
      }else if(a=='Τηλεφωνία'){
        var b = 'tel-fields';
      }else if(a=='VOIP'){
        var b = 'voip-fields';
      }
      $('#'+b).show();
      console.log(a);
  }
</script>
  </body>
</html>