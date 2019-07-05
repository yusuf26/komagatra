<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Pengajuan Penarikan Simpanan Baru - SIFOR KOPJAM</title>
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
					<h3 class="box-title">Formulir Pengajuan Penarikan Simpanan </h3>
				</div>
				<?php echo form_open('',array('id'=>'form_submit')); ?>
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
						<?php 
						$attr_form = 'jenis';
						$attr_form_label = 'Jenis';
						$options = array(
							'' => 'Pilih Jenis Simpanan',
							'Sukarela'		=> 'Sukarela',
							'Wajib'	=> 'Wajib',
							'Pokok'	=> 'Pokok',
							);
						echo '<label for="'.$attr_form.'">'.$attr_form_label.'</label>
								<div>';
						echo form_dropdown($attr_form, $options, '', 'id="'.$attr_form.'" class="form-control" style="width: 250px;"');
						echo '</div>';
						?>
					</div>	
					<div class="form-group">
						<?php
						$data = array(
							'name'       => 'jumlah',
							'id'			=> 'jumlah',
							'class'		=> 'form-control',
							'value'      => '',
							'maxlength'  => '255',
							'readonly' 		=> true,
							'style'      => 'width: 250px'
							);
						echo form_label('Jumlah Simpanan', 'jumlah');
						echo form_input($data);
						echo form_error('nominal', '<p style="color: red;">', '</p>');
						?>
						<input type="hidden" id="jumlah_val">
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
						echo form_label('Nominal Penarikan', 'nominal');
						echo form_input($data);
						echo form_error('nominal', '<p style="color: red;">', '</p>');
						?>
						<input type="hidden" id="nominal_val">
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
						echo '<br>'; ?>
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
		$('#nominal').on('change keyup paste', function() {
			var n = parseInt($(this).val().replace(/\D/g, ''), 10);
			$('#nominal_val').val(n);
			$(this).val(number_format(n, 0, '', '.'));
		});
		// $('#jenis').on('change', function() {
		// 	oc_lama_ags();
		// });
		// oc_lama_ags();

		$('#tipe-ags').on('change', function() {
			if($(this).val() == 1){
				$('#box-ags').show();
			}else {
				$('#box-ags').hide();
			}
		});

		$('#jenis').on('change',function(){
			$('#jumlah').val(0);
			if($(this).val() != ''){
				get_simpanan($(this).val());
			}
		});

	});

	function get_simpanan(jenis) {
		$.ajax({
			url: '<?php echo site_url('member/get_simpanan_by_anggota')?>',
			type: 'POST',
			dataType: 'json',
			data: {'jenis': jenis}
		})
		.done(function(result) {
			if(result.output){
				$('#jumlah').val(number_format(result.jumlah,0,'','.'));
				$('#jumlah_val').val(result.jumlah);
			}
			console.log("success");
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
	}

	$('#form_submit').submit(function(event){

		if($('#jenis').val() == ''){
			alert('Silahkan Pilih Jenis Simpanan Terlebih Dahulu');
		}else {
			var jumlah = parseInt($('#jumlah_val').val());
			var nominal = parseInt($('#nominal_val').val());
			// alert(jumlah+' = '+nominal);
			if(nominal >= jumlah){
				alert('Maaf, Penarikan tidak boleh lebih dari Simpanan Anda');
			}else {
				$(this).submit();
			}
		}

		event.preventDefault();
	});


</script>

</body>
</html>