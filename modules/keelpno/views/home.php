<?php if(!isset($this->session->user)){ ?>
<form action="<?= base_url()."keelpno" ?>" method='post'>
Τεχνικός:
	 <select name="user" id="">
	 	<option value="Άλεξ">Άλεξ</option>
	 	<option value="Γιώργος">Γιώργος</option>
	 	<option value="Τάκης">Τάκης</option>
	 </select>
	 <input type="submit" value="Επιλογή">
 </form>

 <?php 	}else{ 

if(!isset($this->session->type)){

?>
<form action="<?= base_url()."keelpno" ?>" method='post'>
Κατηγορία:
	 <select name="type" id="">
	 		 <option  value="Πληροφορική" default>Πληροφορική</option>
    		 <option   value="Τηλεφωνία">Τηλεφωνία</option>
    		 <option   value="VOIP">VOIP</option>
	 </select>
	 <input type="submit" value="Επιλογή">
 </form>
 <form id="reset" action="<?= base_url(); ?>keelpno/reset" method="post">
 <br> <br> <br> <br> <br>
    <input id="" type="submit" value="Reset">
 </form>
<?php }else{
		redirect( base_url()."keelpno/add" ,'refresh');
	} ?>

<?php 	} ?>