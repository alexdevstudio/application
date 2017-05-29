<?php 




if($basic_templates){
	echo "<pre>"; print_r($basic_templates->result());
}else{
echo	'<p>No basic templates yet.</p>';
}

if($specific_templates){
	echo "<pre>"; print_r($specific_templates->result());
}
else{
echo	'<p>No specific templates yet.</p>';
	}
 ?>