
<?php
	
?>

<style type="text/css">
	
</style>

	<div class="row">
		<div class="box box-primary">
			<div class="box-body" style="min-height: 500px;">
				<div>
					<p style="text-align:center; font-size: 15pt; font-weight: bold;"> Data Laporan Uang Makan</p>
				</div>

				<div id="tb">
					<div id="filter_tgl" class="input-group" style="display: inline;">
						<button onclick="form_tambah()" class="btn btn-success"><i class="fa fa-plus"></i> TAMBAH</button>
						<input type="text" id="fr_bulan" class="form-control" name="fr_bulan" value="" style="display: inline-block; width: 100px; line-height: 20px; vertical-align: middle; background-color:#ddd;" placeholder="Filter Bulan" />
						<a href="javascript:void(0);" id="fm_filter" class="btn btn-primary"><i class="fa fa-filter"></i> FILTER</a>
						<a href="javascript:void(0);" id="fm_cetak" class="btn bg-purple" onclick="cetak_laporan();"><i class="fa fa-print"></i> CETAK</a>
					</div>

				</div>

				<table 
					id="tablegrid"
					data-toggle="table"
					data-id-field="id"
					data-url="<?php echo site_url('uang_makan/ajax_uang_makan'); ?>" 
					data-query-params="queryParams"
					data-sort-name="bulan"
					data-sort-order="desc"
					data-pagination="true"
					data-toolbar="#tb"
					data-side-pagination="server"
					data-page-list="[5, 10, 25, 50, 100]"
					data-page-size="10"
					data-smart-display="false"
					data-select-item-name="tbl_terpilih"
					data-striped="true"
					data-search="false"
					data-show-refresh="true"
					data-show-columns="true"
					data-show-toggle="true"
					data-method="post"
					data-content-type="application/x-www-form-urlencoded"
					data-cache="false" >
					<thead>
						<tr>
							<th data-field="id" data-switchable="false" data-visible="false">ID</th>
							<th data-field="no" data-sortable="true" data-valign="middle" data-align="center" data-halign="center">No</th>
							<th data-field="bulan" data-sortable="true" data-valign="middle" data-align="center" data-halign="center" data-formatter="bulan_ft">Bulan<br>Penagihan</th>
							<th data-field="nama" data-sortable="true" data-valign="middle" data-align="left" data-halign="center" >Anggota</th>
							<th data-field="jumlah" data-sortable="true" data-valign="middle" data-align="right" data-halign="center">Jumlah</th>
							<th data-field="aksi" data-sortable="false" data-align="center" data-halign="center" data-valign="middle" data-formatter="aksi_ft">Aksi</th>
						</tr>
					</thead>
				</table>
				<?php
					//var_dump($data_simpanan);
				?>

			</div><!--box-p -->
		</div><!--box-body -->
	</div><!--row -->


<!-- Modal -->
<div id="modal_aksi" class="modal fade" role="dialog">
	<form>
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">Konfirmasi</h4>
	</div>
	<div class="modal-body">
		<p class="modal_hasil">
			
		</p>
		<div id="div_alasan">
			
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" id="link_konfirmasi_batal" data-dismiss="modal">Batal</button>
		<a href="javascript:void(0)" class="btn btn-primary" id="link_konfirmasi">OK</a>
	</div>
	</form>
</div>

<div id="modal_tambah" class="modal fade" role="dialog">
	<form id="form-tambah" class="form-horizontal" action="" method="POST" >
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">TAMBAH</h4>
		</div>
		<div class="modal-body">
			<div class="form-group">
			    <label class="col-sm-2 control-label">Bulan</label>
			    <div class="col-sm-8">
			     <input type="text" class="form-control fr_bulan" name="bulan" id="bulan" placeholder="Bulan Penagihan" value="<?php echo date('m-Y');?>" />
			    </div>
			</div>
			<div class="form-group">
			    <label class="col-sm-2 control-label">Anggota</label>
			    <div class="col-sm-8">
			     	<select class="select2_anggota" name="anggota_id" id="anggota_id"  style="width:100%;">
			     		<?php
			     		if(!empty($anggota)){
			     			foreach ($anggota as $key => $row) {
				     			?>	
								<option value="<?php echo $row['id'];?>"><?php echo $row['anggota_id'].' - '.$row['nama'];?></option>
				     			<?php
				     		}
			     		}
			     		
			     		?>
			     	</select>
			    </div>
			</div>
			<div class="form-group">
			    <label class="col-sm-2 control-label">Jumlah</label>
			    <div class="col-sm-8">
			     <input type="text" class="form-control" id="jumlah" placeholder="Jumlah" />
			     <input type="hidden" name="jumlah" id="jumlah_val">
			    </div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
			<a href="javascript:void(0)" class="btn btn-primary" id="btn_tambah">SIMPAN</a>
		</div>
	</form>
</div>

<script type="text/javascript">
	


	function form_tambah()
	{
		$('#modal_tambah').modal('show');
		$('.modal-backdrop.fade.in').css('z-index', '1039');
		$('.modal-backdrop.fade.in').css('background-color', '#000');
	}

	$('#jumlah').on('change keyup paste', function() {
		var n = parseInt($(this).val().replace(/\D/g, ''), 10);
		$('#jumlah_val').val(n);
		$(this).val(number_format(n, 0, '', '.'));
	});

	

	function fm_filter_tgl() {
		$('#daterange-btn').daterangepicker({
			ranges: {
				'Hari ini': [moment(), moment()],
				'Kemarin': [moment().subtract('days', 1), moment().subtract('days', 1)],
				'7 Hari yang lalu': [moment().subtract('days', 6), moment()],
				'30 Hari yang lalu': [moment().subtract('days', 29), moment()],
				'Bulan ini': [moment().startOf('month'), moment().endOf('month')],
				'Bulan kemarin': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
				'Tahun ini': [moment().startOf('year').startOf('month'), moment().endOf('year').endOf('month')],
				'Tahun kemarin': [moment().subtract('year', 1).startOf('year').startOf('month'), moment().subtract('year', 1).endOf('year').endOf('month')]
			},
			showDropdowns: true,
			format: 'YYYY-MM-DD',
			startDate: moment().startOf('year').startOf('month'),
			endDate: moment().endOf('year').endOf('month')
		},
		function(start, end) {
			$('#reportrange span').html(start.format('D MMM YYYY') + ' - ' + end.format('D MMM YYYY'));
			//doSearch();
		});
	}

	function bulan_ft(value, row, index) {
		return '<span title="'+row.bulan+'">'+row.bulan_txt+'</span>';
	}
	

	function aksi_ft(value, row, index) {
		var nsi_out = '';

		nsi_out += ' <a data-data_aksi="Hapus" data-data_id="'+row.id+'" class="a_hapus btn btn-sm btn-danger" href="javascript:void(0);"><i class="fa fa-times-circle"></i> Hapus</a>';
		return  nsi_out;
	}

	$('#fr_bulan, .fr_bulan').datepicker({
				format: "mm-yyyy",
				weekStart: 1,
				startView: 1,
				minViewMode: 1,
				language: "id",
				autoclose: true,
				clearBtn: true,
				todayHighlight: true
			});
	$('.select2_anggota').select2({
				placeholder: "Pilih Anggota"
		});
	$(function() {

		

		var $table = $('#tablegrid');

		$table.on('load-success.bs.table', function(event) {
			$('.editable').editable();
		});

		$table.on('click', '.a_diterima, .a_ditolak, .a_dipending, .a_hapus, .a_dilaksanakan, .a_belum, .a_dibatal', function(event) {
			var data_id = $(this).data('data_id');
			var data_aksi = $(this).data('data_aksi');
			var jenis = $(this).data('jenis');

			$('#link_konfirmasi').show();
			$('#link_konfirmasi_batal').text('Batal');
			$('.modal_hasil').html('Apakah Yakin Ingin <strong>'+data_aksi+'</strong> Ajuan ini?');
			var fm_tgl_cair = '';
			
			$('#modal_aksi').modal('show');
			
			$('.modal-backdrop.fade.in').css('z-index', '1039');
			$('.modal-backdrop.fade.in').css('background-color', '#000');
			$('#link_konfirmasi').data('data_id', data_id);
			$('#link_konfirmasi').data('data_aksi', data_aksi);
			$('#link_konfirmasi').data('jenis', jenis);
			$('#link_konfirmasi').text('OK '+data_aksi);
			$('.datepicker').datepicker({
				format: "yyyy-mm-dd",
				weekStart: 1,
				language: "id",
				calendarWeeks: true,
				autoclose: true,
				todayHighlight: true
			});
		});

		$('#link_konfirmasi').click(function(event) {
			var data_id = $(this).data('data_id');
			var data_aksi = $(this).data('data_aksi');
			var jenis = $(this).data('jenis');
			var data_alasan = $('#alasan').val();
			var data_tgl_cair = $('#tgl_cair').val();
			var kas_id = $('#kas').val();
			var angsuran_ke = $('#angsuran_ke').val();
			var jumlah_simp = $('#jumlah_simp').val();
			$.ajax({
				url: '<?php echo site_url('uang_makan/aksi'); ?>',
				type: 'POST',
				dataType: 'html',
				data: {id: data_id, aksi: data_aksi, alasan: data_alasan, kas_id:kas_id, jenis:jenis, angsuran_ke:angsuran_ke, jumlah_simp:jumlah_simp},
			})
			.done(function(data) {
				if(data == 'OK') {
					$('.modal_hasil').html('<div class="alert alert-success">Pengajuan Telah Sukses <strong>' + data_aksi + '</strong></div>');
					$('#link_konfirmasi').hide('slow');
					$('#div_alasan').hide('fast');
					$('#link_konfirmasi_batal').text('Tutup');
					$table.bootstrapTable('refresh');
				} else {
					$('.modal_hasil').html('<div class="alert alert-danger">Gagal, silahkan ulangi kembali. Kemungkinan data error atau sudah tidak ada.</div>');
				}
			})
			.fail(function() {
				alert('Error, Silahkan ulangi');
			});
		});

		

		$('#btn_tambah').on('click',function(){
			var bulan = $('#bulan').val();
			var anggota_id = $('#anggota_id').val();
			var jumlah = $('#jumlah_val').val();
			$.ajax({
				url: '<?php echo site_url('uang_makan/tambah'); ?>',
				type: 'POST',
				dataType: 'json',
				data: {bulan:bulan,anggota_id:anggota_id,jumlah:jumlah},
			}).done(function(data) {
				if(data.output){
					$table.bootstrapTable('refresh');
					$('#modal_tambah').modal('hide');
				}
			}).fail(function() {
				alert('Error, Silahkan ulangi');
			});
			
		});

		// $(".datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
		fm_filter_tgl();
		$('.select2_jenis').select2({
			placeholder: "Semua Jenis"
		});
		$('.select2_status').select2({
			placeholder: "Semua Status"
		});
		$('#fm_filter').click(function(event) {
			$table.bootstrapTable('refresh');
		});
	});


	function get_ags(id)
	{
		$.ajax({
			url: '<?php echo site_url('pengajuan_simpanan/get_ags/'); ?>',
			type: 'POST',
			dataType: 'json',
			data: {id: id},
		})
		.done(function(data) {
			console.log(data);
			if(data.output == true) {
				
				if(data.angsuran_ke == 'done'){
					$('.modal_hasil').html('<div class="alert alert-success">Simpanan sudah selesai di angsur</div>');
					$('#div_alasan').hide();
					$('#link_konfirmasi').hide();
				}else {
					$('#angsuran_ke').val(data.angsuran_ke);
					$('#jumlah_simp').val(data.jumlah);
				}
			} else {
				$('.modal_hasil').html('<div class="alert alert-danger">Gagal, silahkan ulangi kembali. Kemungkinan data error atau sudah tidak ada.</div>');
			}
		})
		.fail(function() {
			alert('Error, Silahkan ulangi');
		});
	}

	function queryParams(params) {
		//console.log(params);
		return {
 			"limit"		: params.limit,
 			"offset"		: params.offset,
 			//"search"		: params.search,
 			"sort"		: params.sort,
 			"order"		: params.order,
 			"fr_bulan"	: $('#fr_bulan').val(),
		}
	}

	function cetak_laporan () {
		
		var fr_bulan	= $('#fr_bulan').val();

		// if(fr_jenis == null) { fr_jenis = '';}
		// if(fr_status == null) { fr_status = '';}
		
		var win = window.open('<?php echo site_url("uang_makan/laporan/?fr_bulan=' + fr_bulan + '"); ?>');
		if (win) {
			win.focus();
		} else {
			alert('Popup jangan di block');
		}
	}

</script>