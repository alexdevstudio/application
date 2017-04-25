<?php if(!isset($this->session->client)){ ?>
<h2>Διεύθυνση:</h2>
<form action="<?= base_url()."keelpno" ?>" method='post'>
<input type="hidden" name="client" value="marousi">
	 <input type="submit" value="Μαρούσι">
 </form>
 <form action="<?= base_url()."keelpno" ?>" method='post'>
<input type="hidden" name="client" value="vari">
	 <input type="submit" value="Βάρη">
 </form>


<?php }else if(!isset($this->session->user)){ ?>
<form action="<?= base_url()."keelpno" ?>" method='post'>
<h2>Τεχνικός:</h2>
	 <select name="user" id="">
	 	<option value="Άλεξ">Άλεξ</option>
	 	<option value="Γιώργος">Γιώργος</option>
	 	<option value="Τάκης">Τάκης</option>
	 	<option value="Θανάσης">Θανάσης</option>
	 </select>
	 <input type="submit" value="Επιλογή">
 </form>

 <?php 	}else { 

if(!isset($this->session->type)){

?>
<form action="<?= base_url()."keelpno" ?>" method='post'>
<h2>Κατηγορία:</h2>
	 <select name="type" id="">
	 		 <option  value="Πληροφορική" default>Πληροφορική</option>
    		 <option   value="Τηλεφωνία">Τηλεφωνία</option>
    		 <option   value="VOIP">VOIP</option>
	 </select>
	 <input type="submit" value="Επιλογή">
 </form>
 
<?php }else{
		redirect( base_url()."keelpno/add" ,'refresh');
	} ?>

<?php 	} ?>

<form id="reset" action="<?= base_url(); ?>keelpno/reset" method="post">
 <br> <br> <br> <br> <br>
    <input style="color:#fff;background:red;" id="" type="submit" value="Reset">
 </form>