		<div class="header w-full bg-blue-500 relative py-6 px-4 xl:px-0">
			<div class="container grid grid-cols-3 gap-x-4 mx-auto clearfix">
				<div class="col-span-3 lg:col-span-1 mb-4 lg:mb-0">
					<input type="text" placeholder="Nama Pelanggan" class="addnama text-xl w-full shadow rounded focus:outline-none focus:bg-gray-100 p-3 px-5" onclick="this.setSelectionRange(0, this.value.length)" data-tippy-content='Nama Pelanggan (max. 24 huruf)'>
				</div><!-- end nama -->
				<div class="col-span-3 lg:col-span-1 mb-4 lg:mb-0">
					<input type="text" placeholder="Keterangan" class="addket text-xl w-full shadow rounded focus:outline-none focus:bg-gray-100 p-3 px-5" onclick="this.setSelectionRange(0, this.value.length)" data-tippy-content='Masukan Keterangan'>
				</div><!-- end tags -->
				<div class="col-span-3 lg:col-span-1">
					<input type="hidden" class="adddate hidden" value="<?= date("Y-m-j") ?>"/>
					<input type="text" placeholder="Jumlah, Lalu Enter" class="addjumlah currency text-xl w-full shadow rounded focus:outline-none focus:bg-gray-100 p-3 px-5" onclick="this.setSelectionRange(0, this.value.length)" data-tippy-content='Masukan Jumlah'>
				</div>
			</div>
		</div>