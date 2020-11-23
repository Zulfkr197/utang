<?php
	/* Load */
	require_once('asset/connects.php');
	date_default_timezone_set('Asia/Ujung_Pandang');
    if (!$sqlconn) {
        // Create Database if not Found
        $mysqlconn      = mysqli_connect($host, $sqluser, $sqlpass);
        $db_sql			= "CREATE DATABASE IF NOT EXISTS zulfkr_iPOS5";
        $db_create 		= mysqli_query($sqlconn, $db_sql);
        echo "<script>location.reload();</script>";
	}
?>
<!doctype html>
<html lang="en">
	<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="favicon.png">
    <!-- CSS -->
	<link rel="stylesheet" href="asset/css/tailwind.min.css">
	<link rel="stylesheet" href="asset/css/modaal.min.css">
	<title>Pengelola Utang</title>
	<!-- Optional JavaScript -->
	<script src="asset/js/jquery.min.js"></script>
	<script src="asset/js/popper.min.js"></script>
	<script src="asset/js/tippy-bundle.umd.min.js"></script>
    <script src="asset/js/notify.min.js"></script>
	<script src="asset/js/jquery.formatCurrency.js"></script>
	<script src="asset/js/modaal.min.js"></script>
    <style>
	.notifyjs-corner{bottom:12px!important;right:12px!important;}
	.notifyjs-bootstrap-error {background-position: 12px 10px;font-weight:600;padding-left:42px;}
	.notifyjs-bootstrap-success {background-position: 12px 10px;font-weight:600;padding-left:42px;}
	.dialogs .modaal-container{border-radius:.375rem}
	.dialogs .modaal-content-container h1{font-size:1.25rem;line-height:1.75rem;font-weight:600;margin-bottom:.75rem;color:#111827}
	.dialogs .modaal-confirm-content{color:#9CA3AF;font-size:1rem;line-height:1.5rem}
	.dialogs .modaal-confirm-wrap{background:#F3F4F6;padding:0;margin:28px -28px -28px;padding:1.5rem;display:flex;font-size:1rem}
	.dialogs .modaal-confirm-wrap>button{display:inline-block;width:50%;font-size:1rem;line-height:1.5rem}
	.dialogs .modaal-confirm-btn.modaal-ok{background:#10B981;text-shadow:0 0 1px #000;font-weight:600;letter-spacing:.6px}
	.dialogs.del .modaal-confirm-btn.modaal-ok{background:#EF4444;}
    </style>
	</head>
	<body class='bg-gray-50'>
        <?php require_once('include/header.php'); ?>
		
		<div class="wrapper container mx-auto mt-4 py-6 relative w-full px-4 xl:px-0 pb-0 xl:pb-6">
			<div class="messages"></div>

			<div class="pintas">
				<button class='reload relative mb-6 inline-block bg-white rounded-md border border-gray-300 shadow-sm p-6 hover:bg-blue-500 hover:text-gray-100 hover:border-blue-600 font-medium py-4 xl:py-2 px-6 items-center mr-2'>
					<svg class="absolute w-4 h-4 relative inline-block" style="top:-2px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg><path stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M15 19l-7-7 7-7' /></svg>
					<span class="pl-2">Muat Ulang</span>		
				</button>
			</div>

			<div class="dashboard mb-10">
				<div class="grid grid-cols-3 gap-4">
					<div class="col-span-3 lg:col-span-1 bg-white rounded-md border border-gray-300 shadow-sm p-8">
						<div class='text-gray-400 text-lg mb-3'>Hari Ini</div>
						<div class='sumhari text-4xl lg:text-5xl font-semibold text-blue-600'>
						<?php
							$sumharisql  = mysqli_query($sqlconn, "SELECT SUM(jumlah) AS Hari FROM `utang` WHERE tanggal='".date("Y-m-d")."' AND `status`='BELUM'");
							$sumharifetch  = mysqli_fetch_object($sumharisql);
							echo (empty($sumharifetch->Hari)) ? 0 : number_format($sumharifetch->Hari,0,",",",");
						?>
						</div>
						<!-- Jumlah Count Hari ini -->
					</div>
					<div class="col-span-3 lg:col-span-1 bg-white rounded-md border border-gray-300 shadow-sm p-8">
						<div class='text-gray-400 text-lg mb-3'>Total Hutang</div>
						<div class='sumtotal text-4xl lg:text-5xl font-semibold'>
						<?php
							$sumttlsql  = mysqli_query($sqlconn, "SELECT SUM(jumlah) AS Total FROM `utang` WHERE `status`='BELUM'");
							$sumttlfetch  = mysqli_fetch_object($sumttlsql);
							echo (empty($sumttlfetch->Total)) ? 0 : number_format($sumttlfetch->Total,0,",",",");
						?>
						</div>
					</div>
					<div class="col-span-3 lg:col-span-1 bg-white rounded-md border border-gray-300 shadow-sm p-8">
						<div class='text-gray-400 text-lg mb-3'>Sisa Hutang</div>
						<div class='sumsisa text-4xl lg:text-5xl font-semibold'>
						<?php
							$sumsisasql  = mysqli_query($sqlconn, "SELECT SUM(jumlah - bayar) AS Sisa FROM `utang` WHERE `status`='BELUM'");
							$sumsisafetch  = mysqli_fetch_object($sumsisasql);
							echo (empty($sumsisafetch->Sisa)) ? 0 : number_format($sumsisafetch->Sisa,0,",",",");
						?>
						</div>
					</div>
				</div>
				<div class="dashboardajax"></div>
			</div>
			<!-- end dashboard -->
			
			<div class="tables mb-10">
				<!-- Status -->
				<div class="status text-xl flex items-center mb-8">
					<div class="statbelum cursor-pointer pb-3 inline-block border-b-4 border-gray-50 mr-6 font-semibold border-blue-500 statactive" data-stat='BELUM'>Belum Lunas</div>
					<div class="statlunas cursor-pointer pb-3 inline-block border-b-4 border-gray-50 text-gray-600 hover:text-black" data-stat='LUNAS'>Lunas</div>
				</div>

				<div class="groupfilter mb-4">
					<div class="fltr_plngng block md:inline-block relative mb-4 w-full md:w-48 mr-0 md:mr-3">
						<select class="block appearance-none w-full bg-white px-6 py-4 pr-6 rounded border border-gray-300 shadow-sm leading-tight focus:outline-none focus:shadow-outline" data-tippy-content='Pelanggan'>
							<option value="">Filter Pelanggan</option>
						</select>
						<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
							<svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
						</div>
					</div><!-- end filter -->					
					<div class="fltr_hari block md:inline-block relative mb-4 w-full md:w-40 mr-0 md:mr-3">
						<select class="block appearance-none w-full bg-white px-6 py-4 pr-6 rounded border border-gray-300 shadow-sm leading-tight focus:outline-none focus:shadow-outline" data-tippy-content='Tanggal'>
							<option value="">Filter Hari</option>
							<?php
								for($d=1; $d<=date('t'); $d++){
									echo "<option value='$d'>$d</option>";
								}
							?>
						</select>
						<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
							<svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
						</div>
					</div><!-- end hari -->
					<div class="fltr_bulan block md:inline-block relative mb-4 w-full md:w-40 mr-0 md:mr-3">
						<select class="block appearance-none w-full bg-white px-6 py-4 pr-6 rounded border border-gray-300 shadow-sm leading-tight focus:outline-none focus:shadow-outline" data-tippy-content='Bulan'>
						<option value="">Filter Bulan</option>
							<?php
								$bulan = [ 1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember' ];
								for ($i=1; $i<=12; $i++){
									echo "<option value='".str_pad($i,2,"0",STR_PAD_LEFT)."'>$bulan[$i]</option>";
								}
							?>
						</select>
						<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
							<svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
						</div>
					</div><!-- end bulan -->
					<div class="fltr_thn block md:inline-block relative mb-4 w-full md:w-32">
						<select class="block appearance-none w-full bg-white px-6 py-4 pr-6 rounded border border-gray-300 shadow-sm leading-tight focus:outline-none focus:shadow-outline" data-tippy-content='Tahun'>
							<?php
								$zfpgtthn_query = mysqli_query($sqlconn,"SELECT DISTINCT SUBSTR(tanggal, 1, 4) AS tahun FROM utang");
								if(!mysqli_num_rows($zfpgtthn_query)){
									echo "<option value='2020'>2020</option>";
								}else{
									while($row = mysqli_fetch_row($zfpgtthn_query)){
										$active = (date("Y") == $i) ? ' selected' : '';
										echo "<option value='$row[0]'$active>$row[0]</option>";
									}
								}
							?>
						</select>
						<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
							<svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
						</div>
					</div><!-- end tahun -->
				</div>

				<div class='memuat text-gray-600 hidden'>Sedang Memuat..</div>
				<div class="results"></div>
				<div class="ajaxresult"></div>

			</div>

			<div class="footer text-base text-gray-400">
				<p>Dibuat oleh zulfkr. Asset oleh  <a class='text-blue-500 hover:text-blue-800' href='https://tailwindcss.com/'>Tailwinds</a>, <a class='text-blue-500 hover:text-blue-800' href='https://heroicons.com/'>Heroicons</a> </p>
			</div>

		</div>
		<?php require_once('include/javascript.php'); ?>
	</body>
</html>