<?php
    require_once('../asset/connects.php');
    
    function jsdashboard($idx){
        global $sqlconn;
		$sumharisql     = mysqli_query($sqlconn, "SELECT SUM(jumlah) AS Hari FROM `utang` WHERE tanggal='".date("Y-m-d")."'");
		$sumtotalsql    = mysqli_query($sqlconn, "SELECT SUM(jumlah) AS Total FROM `utang` WHERE `status`='BELUM'");
		$sumsisasql     = mysqli_query($sqlconn, "SELECT SUM(jumlah - bayar) AS Sisa FROM `utang`");
		$tblesisasql    = mysqli_query($sqlconn, "SELECT SUM(jumlah - bayar) AS Sisa FROM `utang` WHERE id='$idx'");
		$sumharifetch   = mysqli_fetch_object($sumharisql);
		$sumtotalfetch  = mysqli_fetch_object($sumtotalsql);
		$sumsisafetch   = mysqli_fetch_object($sumsisasql);
		$tblesisafetch  = mysqli_fetch_object($tblesisasql);
		$ressumhari     = (empty($sumharifetch->Hari)) ? 0 : number_format($sumharifetch->Hari,0,",",",");
		$ressumtotal    = (empty($sumtotalfetch->Total)) ? 0 : number_format($sumtotalfetch->Total,0,",",",");
        $ressumsisa     = (empty($sumsisafetch->Sisa)) ? 0 : number_format($sumsisafetch->Sisa,0,",",",");
		$tblesisares    = (empty($tblesisafetch->Sisa)) ? 0 : number_format($tblesisafetch->Sisa,0,",",",");
		$tblesisaselct  = '.post[data-id="'.$idx.'"]';
		echo "<script type='text/javascript'>$(function(){
			$('.sumhari').fadeOut().html('$ressumhari').fadeIn();
			$('.sumtotal').fadeOut().html('$ressumtotal').fadeIn();
			$('.sumsisa').fadeOut().html('$ressumsisa').fadeIn();
			$('$tblesisaselct .editsisa').fadeOut().html('$tblesisares').fadeIn();
		});</script>";
	}
    
	if($_POST["cond"] == "filter"){
        $zfhtngfiltrsst = $_POST['status'];
        $zfhtngfiltrqry = mysqli_query($sqlconn,"SELECT DISTINCT pelanggan, COUNT(pelanggan) AS itung FROM `utang` WHERE `status`='$zfhtngfiltrsst' GROUP BY pelanggan");
        while($row = mysqli_fetch_row($zfhtngfiltrqry)){
            echo "<option class='capitalize' value='$row[0]'>$row[0] ($row[1])</option>";
        }
    }

	if($_POST["cond"] == "adddata"){
        $getdate	    = $_POST['tanggal'];
		$getnama	    = $_POST['nama'];
		$getjumlah	    = $_POST['jumlah'];
		$getket		    = $_POST['ket'];
        $add_query      = mysqli_query($sqlconn, "INSERT INTO `utang`(`tanggal`, `pelanggan`, `jumlah`, `ket`, `status`) VALUES ('$getdate','$getnama',$getjumlah,'$getket','BELUM')");
	}

	if($_POST["cond"] == "editjum"){
		$getid		= $_POST['id'];
		$getvalue	= $_POST['value'];
		$update 	= mysqli_query($sqlconn, "UPDATE utang SET jumlah=$getvalue WHERE id=$getid");
        jsdashboard($getid);
	}

	if($_POST["cond"] == "editbyr"){
		$getid		= $_POST['id'];
		$getvalue	= $_POST['value'];
		$update 	= mysqli_query($sqlconn, "UPDATE utang SET bayar=$getvalue WHERE id=$getid");
        jsdashboard($getid);
	}

	if($_POST["cond"] == "editket"){
		$getid		= $_POST['id'];
		$getvalue	= $_POST['value'];
		$update 	= mysqli_query($sqlconn, "UPDATE utang SET ket='$getvalue' WHERE id=$getid");
	}
    
	if($_POST["cond"] == "trashitem"){
		$getid	= $_POST['id'];
		$trash 	= mysqli_query($sqlconn, "DELETE FROM utang WHERE id=$getid");
	}

	if($_POST["cond"] == "lunasitem"){
		$getid		= $_POST['id'];
		$update 	= mysqli_query($sqlconn, "UPDATE utang SET `status`='LUNAS' WHERE id=$getid");
	}