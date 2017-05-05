<style>
	table tr:nth-child(odd) {
    background: #dcdcdc;
	}
	td {
	    height: 75px;
	    overflow: hidden;
	}
</style>

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
	 	<option value="Γιάννης">Γιάννης</option>
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
    		 <option   value="Copiers">Copiers</option>
    		 <option   value="UPS">UPS</option>
	 </select>
	 <input type="submit" value="Επιλογή">
 </form>
 <form id="reset" action="<?= base_url(); ?>keelpno/reset" method="post">
 <br> <br> 
    <input style="color:#fff;background:red;" id="" type="submit" value="Reset">
 </form>
 <?php if (isset($tickets)) { ?>
 <hr>
 <div style="width:50%;float:left">
<h2 style="text-align:center">Υπάλληλοι</h2>
 	<table>
 		<thead>
 			<tr>
	 			<th style="width:10%;">Ημ/νια</th>
	 			<th style="width:20%;">Κατηγορία</th>
	 			<th style="width:55%;">Σχόλια</th>
	 			<th style="width:15%;">Επεξ.</th>
 			</tr>
 		</thead>
 		<tbody>
 			<?php 
 				foreach ($tickets as $ticket) {
 					if ($ticket->daily=='0') {
 						?>
							<tr>
								<td style="text-align:center;"><?= date("d M y",strtotime($ticket->ticket_date)); ?></td>
								<td style="text-align:center;"> <?= $ticket->category; ?></td>
								<td><br><?= Modules::run('keelpno/ttruncat', $ticket->technician_comments, 150); ?><br><br></td>
								<td style="text-align:center;"><a target="_blank" href="<?= base_url().'keelpno/edit/'.$ticket->id; ?>">Επεξ.</a> </td>
								
							</tr>

 						<?php
 					}
 				}
 			 ?>
 		</tbody>
 	</table>
 </div>

 <div style="width:50%;float:left">
 <h2 style="text-align:center">Ημερήσια</h2>
 	<table>
 		<thead>
 			<tr>
	 			<th style="width:10%;">Ημ/νια</th>
	 			<th style="width:20%;">Κατηγορία</th>
	 			<th style="width:55%;">Σχόλια</th>
	 			<th style="width:15%;">Επεξ.</th>
 			</tr>
 		</thead>
 		<tbody>
 			<?php 

 				foreach ($tickets as $ticket) {
 					if ($ticket->daily=='1') {
 						?>
							<tr>
								<td style="text-align:center;"><?= date("d M y",strtotime($ticket->ticket_date)); ?></td>
								<td style="text-align:center;"><?= $ticket->category; ?></td>
								<td><br><?= Modules::run('keelpno/ttruncat', $ticket->technician_comments, 150); ?><br><br></td>
								<td style="text-align:center;"><a target="_blank" href="<?= base_url().'keelpno/edit/'.$ticket->id; ?>">Επεξ.</a> </td>
								
							</tr>

 						<?php
 					}
 				}
 			 ?>
 		</tbody>
 	</table>
 </div>

<?php } ?>

<?php }else{
		redirect( base_url()."keelpno/add" ,'refresh');
	} ?>

<?php 	} ?>

