<html>
<head>
<title>Upload Form</title>
</head>
<body>

<?php echo $error;?>

<?php echo form_open_multipart('live/upload_netconnect_xml');?>

    Select File To Upload for NetConnect import:<br />
    <input type="file" name="userfile"  />
    <br /><br />
    <input type="submit" name="submit" value="Upload" class="btn btn-success" />
</form>

</body>
</html>

