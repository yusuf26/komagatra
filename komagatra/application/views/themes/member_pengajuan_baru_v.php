<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Pengajuan Baru - SIFOR KOPJAM</title>
	<link rel="shortcut icon" href="<?php echo base_url(); ?>icon.ico" type="image/x-icon" />
	<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
	<!-- bootstrap 3.0.2 -->
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<!-- font Awesome -->
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<!-- Theme style -->
	<link href="<?php echo base_url(); ?>assets/theme_admin/css/AdminLTE.css" rel="stylesheet" type="text/css" />

	
	<link href="<?php echo base_url(); ?>assets/extra/bootstrap-table/bootstrap-table.min.css" rel="stylesheet" type="text/css" />
	
	<?php foreach($js_files as $file) { ?>
		<script src="<?php echo $file; ?>"></script>
	<?php } ?>

	<link href="<?php echo base_url(); ?>assets/theme_admin/css/custome.css" rel="stylesheet" type="text/css" />

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->
</head>
<body>

<div class="container">
	
	<?php $this->load->view('themes/member_menu_v'); ?>

	<div class="row">
		<div class="col-md-12">
			<div class="box box-solid box-primary">
				<div class="box-header">
					<h3 class="box-title">Formulir Pengajuan Pinjaman</h3>
				</div>
				<?php echo form_open('',array('id'=>'form-pengajuan')); ?>
				<div class="box-body">

					<?php if($tersimpan == 'N') { ?>
					<div class="box-body">
						<div class="alert alert-danger alert-dismissable">
							<i class="fa fa-warning"></i>
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							Pengajuan gagal terkirim, silahkan periksa kembali dan ulangi.
						</div>
					</div>
					<?php } ?>

					<div class="form-group">
						<label>Jenis</label>
						<select id="jenis" name="jenis"	 class="form-control" style="width: 250px;">
							<option value="" data-min="0" data-max="0">-- Pilih Jenis Pinjaman --</option>
							<?php
							if(is_array($jenis_pinjaman)){
								foreach ($jenis_pinjaman as $key => $row) {
									?>
									<option value="<?php echo $row->jns_pinjaman;?>" data-urutan="<?php echo $row->tampil_urutan;?>" data-anuitas="<?php echo $row->anuitas;?>" data-min="<?php echo $row->min_pinjaman;?>" data-max="<?php echo $row->max_pinjaman;?>" ><?php echo $row->jns_pinjaman;?></option>
									<?php
								}
							}
							?>
						</select>
					</div>
					<div class="form-group" id="box-urutan">
						<label>Urutan</label>
						<input type="number" name="urutan" id="urutan" class="form-control" min="0" style="width:50px;text-align:center;" readonly="" />
					</div>

					<div class="form-group">
						<?php
						$data = array(
							'name'       => 'nominal',
							'id'			=> 'nominal',
							'class'		=> 'form-control',
							'value'      => '',
							'maxlength'  => '255',
							'style'      => 'width: 250px'
							);
						echo form_label('Nominal', 'nominal');
						echo form_input($data);
						echo form_error('nominal', '<p style="color: red;">', '</p>');
						?>
						<input type="hidden" id="nominal_val" />
					</div>
					

					<div class="form-group">
						<label>Lama Angsuran</label>
						<input type="number" id="lama_ags" min="2" max="10" class="form-control text-center" name="lama_ags" placeholder="Bln" style="width:100px;height:20px;">
					</div>

					<div class="form-group" id="bunga-tahun-box">
						<label>Bunga per Tahun</label>
						<input type="text" id="bunga_tahun" readonly="" class="form-control text-center" value="10%" style="width:100px;height:20px;">
					</div>

					<div class="form-group">
						<?php
						$data = array(
							'name'       => 'keterangan',
							'id'			=> 'keterangan',
							'class'		=> 'form-control',
							'value'      => '',
							'maxlength'  => '255',
							'style'      => 'width: 350px'
							);
						echo form_label('Keterangan', 'keterangan');
						echo form_input($data);
						echo form_error('keterangan', '<p style="color: red;">', '</p>');
						 ?>
					</div>
					<div class="form-group" id="potongan_box">
						<label>Potongan ke Simpanan Wajib</label>
						<input type="text" id="potongan" class="form-control text-number" name="potongan"  style="width:250px;height:20px;" readonly="" />
					</div>
					<div class="form-group" id="uang_diterima_box">
						<label>Uang Diterima</label>
						<input type="text" id="uang_diterima" class="form-control text-number" name="uang_diterima"  style="width:250px;height:20px;" readonly="" />
					</div>
					<div class="form-group">
						<div id="div_simulasi"></div>
					</div>
				</div><!-- /.box-body -->
				<div class="box-footer">
					<?php
					// submit
					$data = array(
						'name' 		=> 'submit',
						'id' 		=> 'submit',
						'class' 	=> 'btn btn-primary',
						'value'		=> 'true',
						'type'	 	=> 'submit',
						'content' 	=> 'Kirim Pengajuan'
						);
					echo form_button($data);

					echo form_close();
					?>	
				</div>
				<?php echo form_close(); ?>
			</div><!-- box-primary -->
		</div><!-- col -->
	</div><!-- row -->

</div>


	<!-- jQuery 2.0.2 -->
	<script src="<?php echo base_url(); ?>assets/theme_admin/js/jquery.min.js"></script>
	<!-- Bootstrap -->
	<script src="<?php echo base_url(); ?>assets/theme_admin/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/extra/bootstrap-table/bootstrap-table.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/extra/bootstrap-table/extensions/filter-control/bootstrap-table-filter-control.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>assets/extra/bootstrap-table/bootstrap-table-id-ID.js" type="text/javascript"></script>


<script type="text/javascript">
	$(function() {
		$('#bunga-tahun-box').hide();
		$('#potongan_box').hide();
		$('#uang_diterima_box').hide();
		$('#box-urutan').hide();
		$('#nominal').on('change keyup paste', function() {
			var n = parseInt($(this).val().replace(/\D/g, ''), 10);
			$('#nominal_val').val(n);
			$(this).val(number_format(n, 0, '', '.'));
		});

		$('.text-number').on('change keyup paste', function() {
			var n = parseInt($(this).val().replace(/\D/g, ''), 10);
			// $('#nominal_val').val(n);
			$(this).val(number_format(n, 0, '', '.'));
		});

		$('#jenis').on('change', function() {
			oc_lama_ags();

		});

		$('#jenis, #nominal, #lama_ags').on('change', function() {
			simulasikan();
		});


	});

	function simulasikan() {
		var jenis = $('#jenis').val();
		var var_nominal = $('#nominal').val();
		var var_lama_ags = $('#lama_ags').val();
		var min_ags = $('#lama_ags').attr('min');
		var max_ags = $('#lama_ags').attr('max');

		var anuitas = $('option:selected', '#jenis').attr('data-anuitas');

		$('#potongan').val('');
		$('#uang_diterima').val('');
		$('#div_simulasi').html('');
		if(var_nominal != 0 && var_nominal != '' && var_lama_ags != '' && var_lama_ags != 0){
			console.log(var_lama_ags+'>='+max_ags+'dan'+var_lama_ags+'<='+min_ags);
			if( var_lama_ags >= min_ags ||  var_lama_ags <= max_ags ){
				if(anuitas == 0){
					var potongan = ($('#nominal_val').val() * 1) / 100;
					$('#potongan').val(number_format(potongan, 0, '', '.'));
					var uang_diterima = $('#nominal_val').val() - potongan;
					$('#uang_diterima').val(number_format(uang_diterima, 0, '', '.'));
				}

				$('#submit').button('loading');
				$.ajax({
					url: '<?php echo site_url('member/simulasi')?>',
					type: 'POST',
					dataType: 'html',
					data: {'nominal': var_nominal, 'lama_ags': var_lama_ags, 'jenis': jenis}
				})
				.done(function(result) {
					$('#div_simulasi').html(result);
					console.log("success");
					$('#submit').button('reset');
				})
				.fail(function() {
					console.log("error");
				})
				.always(function() {
					console.log("complete");
				});
			}else{
				alert('Mohon maaf, lama angsuran tidak sesuai !!!');
				
			}
		}
	}

	function oc_lama_ags() {
		var jenis = $('#jenis').val();
		var min = $('option:selected', '#jenis').attr('data-min');
		var max = $('option:selected', '#jenis').attr('data-max');
		var anuitas = $('option:selected', '#jenis').attr('data-anuitas');
		var urutan = $('option:selected', '#jenis').attr('data-urutan');

		$('#lama_ags').attr('placeholder',''+min+' - '+max+' Bln');
		$('#lama_ags').attr('min',min);
		$('#lama_ags').attr('max',max);

		$('#potongan_box').hide();
		$('#uang_diterima_box').hide();
		
		$('#nominal').val('');
		$('#nominal_val').val('');
		$('#lama_ags').val('');

		$('#bunga-tahun-box').hide();
		if(anuitas == 0){
			$('#potongan_box').show();
			$('#uang_diterima_box').show();
		}else {
			$('#bunga-tahun-box').show();
		}

		if(urutan == 1){
			$('#box-urutan').show();
			check_urutan(jenis);
		}else {
			$('#box-urutan').hide();
			$('#urutan').val(0);
		}
	}

	function check_urutan(jenis)
	{

		$.ajax({
			url: '<?php echo site_url('member/check_urutan')?>',
			type: 'POST',
			dataType: 'json',
			data: {jenis:1}
		})
		.done(function(result) {
			$('#urutan').val(result.output);
		});
	}

	 $('#form-pengajuan').submit(function(event) {
	 	var jenis = $('#jenis').val();
	 	var nominal = parseInt($('#nominal_val').val());
	 	if(jenis == 'Pinjaman Jangka Pendek' && nominal > 10000000){
	 		alert('Maaf, Maksimal Pinjaman Jangka Pendek hanya 10.000.000')
	 	}else {
	 		$(this).submit();
	 	}
	 	event.preventDefault();
	 });



</script>

</body>
</html>