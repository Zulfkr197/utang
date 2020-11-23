<?php
	require_once('../asset/connects.php');

	$zfhtngvalstat 		= $_POST['status'];
	$zfhtngfiltrplgn 	= (empty($_POST['filter'])) ? '' : 'AND pelanggan="'.$_POST['filter'].'"';

	$zfhtnggethari		= "-".$_POST['hari'];
	$zfhtnggetblan		= (empty($_POST['bulan'])) ? '' : "-".$_POST['bulan'];
	$zfhtnggettahun		= $_POST['tahun'];
	if(!empty($_POST['hari']) && empty($_POST['bulan'])){
		$zfhtngfiltrblan 	= (empty($_POST['hari'])) ? "AND tanggal LIKE '$zfhtnggettahun$zfhtnggetblan%'" : "AND tanggal='$zfhtnggettahun-".date('m')."$zfhtnggethari'";
	}else{
		$zfhtngfiltrblan 	= (empty($_POST['hari'])) ? "AND tanggal LIKE '$zfhtnggettahun$zfhtnggetblan%'" : "AND tanggal='$zfhtnggettahun$zfhtnggetblan$zfhtnggethari'";
	}

	$zfhtngquery		= mysqli_query($sqlconn,"SELECT `tanggal`, `pelanggan`, `jumlah`, `bayar`, `ket`, `id` FROM `utang` WHERE `status`='$zfhtngvalstat' $zfhtngfiltrplgn $zfhtngfiltrblan ORDER BY id DESC");
	
	// Create Table if not Found
	if (!$zfhtngquery) {
		$table_sql		= 'CREATE TABLE `utang` ( `id` int(4) NOT NULL AUTO_INCREMENT, `tanggal` date NOT NULL, `pelanggan` varchar(24) NOT NULL, `jumlah` int(8) NOT NULL, `bayar` int(8) NOT NULL, `ket` text NOT NULL, `status` varchar(5) NOT NULL, PRIMARY KEY (`id`), KEY `fast` (`tanggal`,`pelanggan`,`jumlah`,`bayar`,`status`) USING BTREE ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
		$table_create 	= mysqli_query($sqlconn, $table_sql);
		echo "<script>setTimeout(function(){ location.reload(); }, 100);</script>";
	}
	
	if(!mysqli_num_rows($zfhtngquery)){
		echo "<div class='w-full border mx-auto rounded bg-blue-100 border-blue-300 text-blue-700 text-center p-4 mb-4 text-base text-base font-semibold'>Data Kosong.</div>";
	}
	else{
		
		echo "
		<div class='text-base rounded-t hidden lg:grid grid-cols-11 gap-4 text-base font-semibold text-white bg-blue-500 tracking-wide py-5'>
			<div class='col-span-3 pl-4 lg:pl-0 px-2'>
				<div class='flex gap-x-6 capitalize'>
					<div class='w-1/4 text-center'>
						Tgl
					</div>
					<div class='w-3/4'>
						Pelanggan
					</div>
				</div>
			</div>
			<div class='col-span-5 px-2'>
				<div class='flex gap-x-6'>
					<div class='w-1/3'>
						Jumlah
					</div>
					<div class='w-1/3'>
						Bayar
					</div>
					<div class='w-1/3'>
						Sisa
					</div>
				</div>
			</div>
			<div class='col-span-3 px-2'>Ket</div>
		</div>	
		";

		while($row = mysqli_fetch_row($zfhtngquery)){
			$bulan 		= [ 1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember' ];
			$dates 		= explode('-',$row[0]);
			$tanggal	= $dates[2]." ".substr($bulan[$dates[1]], 0, 3);
			$pelanggan	= $row[1];
			$jumlah		= number_format($row[2],0,",",",");
			$bayar		= (empty($row[3])) ? 0 : number_format($row[3],0,",",",");
			$sisa		= number_format(($row[2] - $row[3]),0,",",",");

			// jika tgl skrng lebih besar dari pada 7
			if($sisa == 0 || $sisa < 0){
				$telattpy	= 'data-tippy-content="Lunas" data-tippy-placement="left"';
			}else{
				$telattpy	= ((date('j') - $dates[2]) > 6) ? 'data-tippy-content="Lebih dari 7 Hari" data-tippy-placement="left"' : '';
			}
			$ket		= (empty($row[4])) ? "-" : "<input type='text' class='editket lg:py-2 w-full rounded bg-gray-50 border border-gray-300 p-2 cursor-pointer' value='$row[4]' disabled/>";

			echo "
			<div class='post text-lg lg:text-lg bg-white border border-gray-200 grid grid-cols-11 lg:gap-4 text-gray-700 pt-2 my-6 lg:px-0 lg:py-4 lg:my-0 items-center rounded-md lg:rounded-none' data-id='$row[5]' $telattpy>
				<div class='col-span-12 lg:col-span-3 lg:pl-4 lg:py-4 text-gray-700'>
					<div class='lg:flex lg:gap-x-6 capitalize'>
						<div class='flex w-full lg:w-1/4 lg:text-center py-4 px-8 border-b-2 border-gray-200 lg:p-0 lg:border-0'>
							<div class='w-1/4 flex lg:hidden text-base font-semibold uppercase text-gray-600'>Tgl</div>
							<div class='w-3/4 pl-4 lg:pl-0 lg:w-auto'>$tanggal</div>
						</div>
						<div class='flex w-full lg:w-3/4 overflow-ellipsis overflow-hidden py-4 px-8 border-b-2 border-gray-200 lg:p-0 lg:border-0'>
							<div class='w-1/4 flex lg:hidden text-base font-semibold uppercase text-gray-600'>Nama</div>
							<div class='w-3/4 pl-4 lg:pl-0 lg:w-auto'>$pelanggan</div>
						</div>
					</div>
				</div>
				<div class='col-span-12 lg:col-span-5 lg:px-2 lg:py-2'>
					<div class='lg:flex lg:gap-x-6'>
						<div class='flex w-full lg:w-1/3 py-4 px-8 border-b-2 border-gray-200 lg:p-0 lg:border-0'>
							<div class='w-1/4 flex items-center lg:hidden text-base font-semibold uppercase text-gray-600'>Jumlah</div>
							<div class='w-3/4 pl-4 lg:pl-0 lg:w-auto' data-tippy-content='Klik 2x, untuk mengedit'><input type='text' class='editjum lg:py-2 w-full rounded bg-gray-50 border border-gray-300 p-2 cursor-pointer currency' value='$jumlah' disabled/></div>
						</div>
						<div class='flex w-full lg:w-1/3 py-4 px-8 border-b-2 border-gray-200 lg:p-0 lg:border-0'>
							<div class='w-1/4 flex items-center lg:hidden text-base font-semibold uppercase text-gray-600'>Bayar</div>
							<div class='w-3/4 pl-4 lg:pl-0 lg:w-auto' data-tippy-content='Klik 2x, untuk mengedit'><input type='text' class='editbyr lg:py-2 w-full rounded bg-gray-50 border border-gray-300 p-2 cursor-pointer currency' value='$bayar' disabled/></div>
						</div>
						<div class='flex w-full lg:w-1/3 py-4 px-8 border-b-2 border-gray-200 lg:p-0 lg:border-0 items-center'>
							<div class='w-1/4 flex items-center lg:hidden text-base font-semibold uppercase text-gray-600'>Sisa</div>
							<div class='w-3/4 pl-4 lg:pl-0 lg:w-auto editsisa'>$sisa</div>
						</div>
					</div>
				</div>
				<div class='col-span-12 lg:col-span-3 lg:px-2 lg:py-2 text-gray-700'>
					<div class='lg:flex lg:gap-x-4'>
						<div class='flex w-full lg:w-4/6 py-4 px-8 border-b-2 border-gray-200 lg:p-0 lg:border-0'>
							<div class='w-1/4 flex items-center lg:hidden text-base font-semibold uppercase text-gray-600'>Ket</div>
							<div class='w-3/4 pl-4 lg:pl-0 lg:w-auto' data-tippy-content='Klik 2x, untuk mengedit'>$ket</div>
						</div>
						<div class='flex w-full lg:w-2/6 py-4 px-8 border-b-2 border-gray-200 rounded-b-md lg:p-0 lg:border-0'>
							<div class='w-1/4 flex lg:hidden text-base font-semibold uppercase text-gray-600'>Aksi</div>
							<div class='w-3/4 pl-4 lg:pl-0 flex gap-x-3 w-auto lg:w-full h-full pr-0 lg:pr-2'>
								<button class='lunasitem bg-blue-500 hover:bg-blue-600 hover:text-gray-100 text-white text-sm py-4 lg:py-2 rounded w-1/2' data-tippy-content='Lunas'><svg class='w-5 h-5 mx-auto' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='4' d='M5 13l4 4L19 7' /></svg></button>
								<button class='trashitem bg-red-500 hover:bg-red-600 hover:text-gray-100 text-white text-sm py-4 lg:py-2 rounded w-1/2 text-center' data-tippy-content='Hapus'><svg class='w-5 h-5 mx-auto' xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16' /></svg></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			";
		}
	}

	if(!empty($_POST['filter'])){
		$getfiltr 		= $_POST['filter'];
		$sumharisql     = mysqli_query($sqlconn, "SELECT SUM(jumlah) AS Total FROM `utang` WHERE `status`='LUNAS' AND pelanggan='$getfiltr'");
		$sumtotalsql    = mysqli_query($sqlconn, "SELECT SUM(jumlah) AS Total FROM `utang` WHERE `status`='BELUM' AND pelanggan='$getfiltr'");
		$sumsisasql     = mysqli_query($sqlconn, "SELECT SUM(jumlah - bayar) AS Sisa FROM `utang` WHERE pelanggan='$getfiltr'");
		$sumharifetch   = mysqli_fetch_object($sumharisql);
		$sumtotalfetch  = mysqli_fetch_object($sumtotalsql);
		$sumsisafetch   = mysqli_fetch_object($sumsisasql);
		$ressumhari     = (empty($sumharifetch->Hari)) ? 0 : number_format($sumharifetch->Hari,0,",",",");
		$ressumtotal    = (empty($sumtotalfetch->Total)) ? 0 : number_format($sumtotalfetch->Total,0,",",",");
		$ressumsisa     = (empty($sumsisafetch->Sisa)) ? 0 : number_format($sumsisafetch->Sisa,0,",",",");
		// tambahkan js hitung, jika tidak kosong
		echo "<script type='text/javascript'>$(function(){
			$('.sumhari').parents('.col-span-3').find('.text-lg').text('Jumlah Lunas');
			$('.sumhari').fadeOut().html('$ressumhari').fadeIn();
			$('.sumtotal').fadeOut().html('$ressumtotal').fadeIn();
			$('.sumsisa').fadeOut().html('$ressumsisa').fadeIn();
		});</script>";
	}else{
		$sumharisql  = mysqli_query($sqlconn, "SELECT SUM(jumlah) AS Hari FROM `utang` WHERE tanggal='".date("Y-m-d")."' AND `status`='$zfhtngvalstat'");
		$sumharifetch  = mysqli_fetch_object($sumharisql);
		$sumttlsql  = mysqli_query($sqlconn, "SELECT SUM(jumlah) AS Total FROM `utang` WHERE `status`='$zfhtngvalstat'");
		$sumttlfetch  = mysqli_fetch_object($sumttlsql);
		$sumsisasql  = mysqli_query($sqlconn, "SELECT SUM(jumlah - bayar) AS Sisa FROM `utang` WHERE `status`='$zfhtngvalstat'");
		$sumsisafetch  = mysqli_fetch_object($sumsisasql);
		$ressumhari     = (empty($sumharifetch->Hari)) ? 0 : number_format($sumharifetch->Hari,0,",",",");
		$ressumtotal    = (empty($sumttlfetch->Total)) ? 0 : number_format($sumttlfetch->Total,0,",",",");
		$ressumsisa     = (empty($sumsisafetch->Sisa)) ? 0 : number_format($sumsisafetch->Sisa,0,",",",");
		echo "<script type='text/javascript'>$(function(){
			$('.sumhari').parents('.col-span-3').find('.text-lg').text('Hari Ini');
			$('.sumhari').fadeOut().html('$ressumhari').fadeIn();
			$('.sumtotal').fadeOut().html('$ressumtotal').fadeIn();
			$('.sumsisa').fadeOut().html('$ressumsisa').fadeIn();
		});</script>";
	}
?>