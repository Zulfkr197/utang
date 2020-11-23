<script type='text/javascript'>
    /* JS */
    tippy('[data-tippy-content]');
	/* Ajax */
	$(function(){
		var notify_error = { position: "bottom right", className: "error", autoHideDelay: 2000 },
			notify_success = { position: "bottom right", className: "success", autoHideDelay: 2000 };

		$('.addnama').keyup(function(e){
            if(e.keyCode == 13){
                $('.addket').focus().select();
            }
		});
		$('.addket').keyup(function(e){
            if(e.keyCode == 13){
                $('.addjumlah').focus().select();
            }
		});
		filtr = function(){
			var getData		= {
				cond 	: 'filter',
                status	: $('.statactive').data('stat')
			};
            $.ajax({
                type	: "POST",
                url		: "include/ajax.php",
                data	: getData,
                beforeSend: function(){
					$('.fltr_plngng select').html();
                },
                success: function (result) {
                    setTimeout(function(){
                        $('.fltr_plngng select').html('<option value="">Filter Pelanggan</option>'+result).fadeIn();
                    },250);
                },
            });
        }
        filtr();
		tables = function(){
			var getData		= {
                status	: $('.statactive').data('stat'),
                hari	: $(".fltr_hari select").val(),
                bulan	: $(".fltr_bulan select").val(),
                tahun	: $(".fltr_thn select").val(),
				filter	: $(".fltr_plngng select").val()
			};
            $.ajax({
                type	: "POST",
                url		: "include/tables.php",
                data	: getData,
                beforeSend: function(){
                    $('.tables>.memuat').fadeIn();
                    $('.tables>.results').fadeOut();
                },
                success: function (result) {
                    setTimeout(function(){
                        $('.tables>.memuat').fadeOut();
                        $('.tables>.results').html(result).fadeIn();
						tippy('[data-tippy-content]');
                    },250);
                },
            });
        }
        tables();
		editable = function(e){
			$.ajax({
				type: "POST",
				url: "include/ajax.php",
				data: getData,
				beforeSend: function () {
					e.prop("disabled", true).addClass('opacity-25 cursor-not-allowed');
				},
				success: function (result) {
					setTimeout(function () {
						$.notify("Berhasil di update", notify_success);
						e.removeClass('border-gray-400 bg-gray-100 cursor-select px-2 rounded opacity-25 cursor-not-allowed').off('dblclick');
						$('.ajaxresult').html(result);
					}, 500);
				}
			});
		}

		$('.addjumlah').keyup(function(e){
            if(e.keyCode == 13){
                if($('.addnama').val() == ''){
                    $.notify("Nama Tidak Boleh Kosong", notify_error);
                }
                else if($('.addket').val() == ''){
                    $.notify("Keterangan Tidak Boleh Kosong", notify_error);
                }else{
                    var getData		= {
                            cond 	: 'adddata',
                            nama 	: $('.addnama').val(),
                            ket 	: $('.addket').val(),
                            jumlah 	: $('.addjumlah').val().replace(/,/g, ''),
                            tanggal	: $('.adddate').val()
                        };
                    
                    $.ajax({
                        type: "POST",
                        url: "include/ajax.php",
                        data: getData,
                        beforeSend: function () {
                            $('.addnama,.addket,.addjumlah').prop("disabled", true).addClass('opacity-25 cursor-not-allowed');
                        },
                        success: function (result) {
                            setTimeout(function () {
                                $('.messages').append("<div class='berhasil bg-green-100 border border-green-300 text-green-700 py-3 px-4 mb-6 rounded'>Data Berhasil di Tambahkan </div>");
                                $('.addnama,.addket,.addjumlah').prop("disabled", false).removeClass('opacity-25 cursor-not-allowed').val('');
                                $('.addnama').focus().select();
								$('.ajaxresult').html(result);
                                tables();
                            }, 500);
                            setTimeout(function () {
                                $('.messages>.berhasil').fadeOut().remove();
                            }, 5000);
                        }
                    });
                }
                
            }
		});
		
		$(".results").on('mouseenter', '.trashitem', function(){
			var getThiz = $(this);
			$(this).modaal({
				custom_class : 'dialogs del',
				width : 500,
				type: 'confirm',
				confirm_button_text: 'Ya',
				confirm_cancel_button_text: 'Batal',
				confirm_title: 'Konfirmasi Hapus Item',
				confirm_content: '<p>Apakah yakin ingin dihapus?</p>',
				confirm_callback: function() {
					var getThis = getThiz;
						getData	= {
							cond 	 : 'trashitem',
							id 		 : getThis.parents('.post').data('id')
						};
					$.ajax({
						type: "POST",
						url: "include/ajax.php",
						data: getData,
						beforeSend: function(){
							getThis.parents('.post').fadeOut();
						},
						success: function (result) {
							setTimeout(function () {
								var getTtal	 = $('.sumtotal').text().replace(/,/g, ''),
									getSisa  = $('.sumsisa').text().replace(/,/g, '');
								$('.sumtotal').fadeOut().html(Number(getTtal) - getThis.parents('.post').find('.editjum').val().replace(/,/g, '')).formatCurrency({ roundToDecimalPlace:0, symbol:''}).fadeIn();
								$('.sumsisa').fadeOut().html(Number(getSisa) - getThis.parents('.post').find('.editsisa').text().replace(/,/g, '')).formatCurrency({ roundToDecimalPlace:0, symbol:''}).fadeIn();
								$.notify("Berhasil dihapus", notify_success);
							}, 500);
						}
					});
				}
			});
		});
		
		$(".results").on('mouseenter', '.lunasitem', function(){
			var getThiz = $(this);
			$(this).modaal({
				custom_class : 'dialogs',
				width : 500,
				type: 'confirm',
				confirm_button_text: 'Ya',
				confirm_cancel_button_text: 'Batal',
				confirm_title: 'Konfirmasi Lunas',
				confirm_content: '<p>Apakah yakin sudah lunas?</p>',
				confirm_callback: function() {
					var getThis = getThiz;
						getData	= {
							cond 	 : 'lunasitem',
							id 		 : getThis.parents('.post').data('id')
						};
					$.ajax({
						type: "POST",
						url: "include/ajax.php",
						data: getData,
						beforeSend: function(){
							getThis.parents('.post').fadeOut();
						},
						success: function (result) {
							setTimeout(function () {
								var getTtal	 = $('.sumtotal').text().replace(/,/g, ''),
									getSisa  = $('.sumsisa').text().replace(/,/g, '');
								$('.sumtotal').fadeOut().html(Number(getTtal) - getThis.parents('.post').find('.editjum').val().replace(/,/g, '')).formatCurrency({ roundToDecimalPlace:0, symbol:''}).fadeIn();
								$('.sumsisa').fadeOut().html(Number(getSisa) - getThis.parents('.post').find('.editsisa').text().replace(/,/g, '')).formatCurrency({ roundToDecimalPlace:0, symbol:''}).fadeIn();
								$.notify("Hutang Lunas", notify_success);
							}, 500);
						}
					});
				}
			});
		});
		
		$(".results").on('dblclick', '.editjum,.editbyr,.editket', function(){
			$(this).prop("disabled", false).removeClass('cursor-pointer').addClass('bg-gray-200 cursor-select rounded').select();
		})
		$(".results").on('blur', '.editjum,.editbyr,.editket', function(){
			$(this).off('dblclick').prop("disabled", true).removeClass('bg-gray-200 cursor-select rounded').addClass('cursor-pointer');
		});
		$(".results").on('keyup', '.editjum', function(e){
			if(e.keyCode == 13){
				var getThis = $(this);
					getData	= {
						cond 	: 'editjum',
						id 		: getThis.parents('.post').data('id'),
						value 	: getThis.val().replace(/,/g, '')
					};
					editable(getThis);
			}
		});
		$(".results").on('keyup', '.editbyr', function(e){
			if(e.keyCode == 13){
				var getThis = $(this);
					getData	= {
						cond 	: 'editbyr',
						id 		: getThis.parents('.post').data('id'),
						value 	: getThis.val().replace(/,/g, '')
					};
					editable(getThis);
			}
		});
		$(".results").on('keyup', '.editket', function(e){
			if(e.keyCode == 13){
				var getThis = $(this);
					getData	= {
						cond 	: 'editket',
						id 		: getThis.parents('.post').data('id'),
						value 	: getThis.val()
					};
					editable(getThis);
			}
		});

        $('.statlunas').click(function(){
            $(this).removeClass('text-gray-600 hover:text-black').addClass('font-semibold border-blue-500 statactive');
            $('.statbelum').removeClass('font-semibold border-blue-500 statactive').addClass('text-gray-600 hover:text-black');
            tables();
			filtr();
        });
        $('.statbelum').click(function(){
            $(this).removeClass('text-gray-600 hover:text-black').addClass('font-semibold border-blue-500 statactive');
            $('.statlunas').removeClass('font-semibold border-blue-500 statactive').addClass('text-gray-600 hover:text-black');
            tables();
			filtr();
        });

		$(".groupfilter select").change(function() {
			tables();
		});

	});

	/* Jquery */
	$(function(){
		$('.reload').click(function(){
			var domain = window.location.hostname,
				https = window.location.protocol,
				path = window.location.pathname;
			location.replace(https + "//" + domain + path);
		});

		//$(".currency").on('input', function(e) {
		$("body").on('input', '.currency', function(){
            $(this).val($(this).val().replace(/[^0-9|,|.]/g, ''));
            $(this).formatCurrency({ roundToDecimalPlace:0, symbol:''});
		});
	});
</script>