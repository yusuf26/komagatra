<!-- Styler -->
<style type="text/css">
td, div {
	font-family: "Arial","​Helvetica","​sans-serif";
}
.datagrid-header-row * {
	font-weight: bold;
}
.messager-window * a:focus, .messager-window * span:focus {
	color: blue;
	font-weight: bold;
}
.daterangepicker * {
	font-family: "Source Sans Pro","Arial","​Helvetica","​sans-serif";
	box-sizing: border-box;
}
.glyphicon	{font-family: "Glyphicons Halflings"}
</style>

<?php 
	// buaat tanggal sekarang
	$tanggal = date('Y-m-d H:i');
	$tanggal_arr = explode(' ', $tanggal);
	$txt_tanggal = jin_date_ina($tanggal_arr[0]);
	// $txt_tanggal .= ' - ' . $tanggal_arr[1];
?>

<!-- Data Grid -->
<table   id="tbl-u" 
title="Data Pengajuan Perubahan Simpanan" 
style="width:auto; height: auto;" 
url="<?php echo site_url('pengajuan_perubahan_simpanan/ajax_list'); ?>" 
pagination="true" rownumbers="true" 
fitColumns="true" collapsible="true"
sortName="tgl_input" sortOrder="desc"
toolbar="#tb"
striped="true">
<thead>
	<tr>
		<th data-options="field:'id', sortable:'true',halign:'center', align:'center'" hidden="true">ID</th>
		<th data-options="field:'ck', width:'13',halign:'center', align:'center'" checkbox="true"></th>
		<th data-options="field:'id_txt', width:'17', halign:'center', align:'center'">ID Ajuan</th>
		<th data-options="field:'tgl_transaksi',halign:'center', align:'center'" hidden="true">Tanggal</th>
		<th data-options="field:'tgl_transaksi_txt', width:'25', halign:'center', align:'center'">Bulan Transaksi</th>
		<th data-options="field:'anggota_id',halign:'center', align:'center'" hidden="true">Anggota ID</th>
		<th data-options="field:'nama', width:'35',halign:'center', align:'left'">Nama Anggota</th>
		<th data-options="field:'jenis_id_txt', width:'20',halign:'center', align:'left'">Jenis Simpanan</th>
		<th data-options="field:'jumlah', width:'15', halign:'center', align:'right'">Jumlah</th>
		<th data-options="field:'ket', width:'15', halign:'center', align:'left'" >Keterangan</th>
		<th data-options="field:'tgl_cair', halign:'center', align:'center'">Tanggal Cair</th>
		<th data-options="field:'status', halign:'center', align:'center'">Status</th>
		<th data-options="field:'status_id', halign:'center', align:'center'" hidden="true">Status</th>
		<th data-options="field:'nota', halign:'center', align:'center'">Cetak Nota</th>
	</tr>
</thead>
</table>

<!-- Toolbar -->
<div id="tb" style="height: 35px;">
	<div style="vertical-align: middle; display: inline; padding-top: 15px;">
		<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="setuju()">Setuju</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="tolak()">Tolak</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="hapus()">Hapus</a>
	</div>
	<div class="pull-right" style="vertical-align: middle;">
		<div id="filter_tgl" class="input-group" style="display: inline;">
			<button class="btn btn-default" id="daterange-btn" style="line-height:16px;border:1px solid #ccc">
				<i class="fa fa-calendar"></i> <span id="reportrange"><span> Tanggal</span></span>
				<i class="fa fa-caret-down"></i>
			</button>
		</div>
		<select id="cari_simpanan" name="cari_simpanan" style="width:150px; height:27px;" >	
			<option value=""> -- Tampilkan Simpanan --</option>			
			<option value="Sukarela">Simpanan Sukarela</option>			
			<option value="Wajib">Wajib</option>			
		</select>
		<select id="cari_status" name="cari_status" style="width:150px; height:27px;z-index: 999;" multiple="" >
			<option value="0">Menunggu Konfirmasi</option>			
			<option value="1">Disetujui</option>			
			<option value="2">Ditolak</option>			
		</select>
		<span>Cari :</span>
		<input name="kode_transaksi" id="kode_transaksi" size="22" style="line-height:25px;border:1px solid #ccc;">

		<a href="javascript:void(0);" id="btn_filter" class="easyui-linkbutton" iconCls="icon-search" plain="false" onclick="doSearch()">Cari</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="false" onclick="cetak()">Cetak Laporan</a>
		<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-clear" plain="false" onclick="clearSearch()">Hapus Filter</a>
	</div>
</div>

<!-- Dialog Form -->
<div id="dialog-form" class="easyui-dialog" show= "blind" hide= "blind" modal="true" resizable="false" style="width:340px; height:270px; padding:20px;" closed="true" buttons="#dialog-buttons" style="display: none;">
	<form id="form" method="post" novalidate>
		<table>
			<tr>
				<td>
					<table>
						<!-- <tr style="height:35px">
							<td>Tanggal Cair </td>
							<td>:</td>
							<td>
								<div class="input-group date dtpicker col-md-5" style="z-index: 9999 !important;">
									<input type="text" name="tgl_transaksi_txt" id="tgl_transaksi_txt" style="width:150px; height:25px" required="true" readonly="readonly" />
									<input type="hidden" name="tgl_transaksi" id="tgl_transaksi" />
									<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
								</div>
							</td>	
						</tr> -->
						<tr style="height:35px">
							<td>Keterangan</td>
							<td>:</td>
							<td>
								<input id="ket" name="ket" style="width:190px; height:20px" >
							</td>	
						</tr>
						<!-- <tr style="height:35px">
							<td>Simpan Ke Kas</td>
							<td>:</td>
							<td>
								<select id="kas" name="kas_id" style="width:195px; height:25px" class="easyui-validatebox" required="true">
									<option value="0"> -- Pilih Kas --</option>			
									<?php	
									foreach ($kas_id as $row) {
										echo '<option value="'.$row->id.'">'.$row->nama.'</option>';
									}
									?>
								</select>
							</td>
						</tr> -->
				</table>
			</tr>
		</table>
	</form>
</div>

<!-- Dialog Button -->
<div id="dialog-buttons">
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="save()">Simpan</a>
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:jQuery('#dialog-form').dialog('close')">Batal</a>
</div>

<!-- Dialog Tolak -->
<div id="dialog-tolak" class="easyui-dialog" show= "blind" hide= "blind" modal="true" resizable="false" style="width:340px; height:170px; padding:20px;" closed="true" buttons="#tolak-buttons" style="display: none;">
	<form id="form" method="post" novalidate>
		<table>
			<tr style="height:35px">
				<td>Keterangan</td>
				<td>:</td>
				<td>
					<input id="ket" name="ket" style="width:190px; height:20px" >
				</td>	
			</tr>
		</table>
	</form>
</div>

<!-- Dialog Button -->
<div id="tolak-buttons">
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="save_tolak()">Simpan</a>
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:jQuery('#dialog-tolak').dialog('close')">Batal</a>
</div>

<script type="text/javascript">
	$('#cari_status').select2({
		placeholder : 'Tampilkan Status'
	});
	$('#cari_simpanan').select2({
		placeholder : 'Tampilkan Jenis Simpanan'
	});

$(document).ready(function() {
	$('#jenis_id').change(function(){
		val_jenis_id = $(this).val();
		$.ajax({
			url: '<?php echo site_url()?>simpanan/get_jenis_simpanan',
			type: 'POST',
			dataType: 'html',
			data: {jenis_id: val_jenis_id},
		})
		.done(function(result) {
			$('#jumlah').numberbox('setValue', result);
			$('#jumlah ~ span input').focus();
			$('#jumlah ~ span input').select();	
		})
		.fail(function() {
			alert('Kesalahan Konekasi, silahkan ulangi beberapa saat lagi.');
		});		
	});

	$('#tbl-u').datagrid();

	$(".dtpicker").datetimepicker({
		language:  'id',
		weekStart: 1,
		autoclose: true,
		todayBtn: true,
		todayHighlight: true,
		pickerPosition: 'bottom-right',
		format: "dd MM yyyy",
		linkField: "tgl_transaksi",
		linkFormat: "yyyy-mm-dd",
		minView : 2
	});	


	$("#cari_simpanan").change(function(){
		$('#tbl-u').datagrid('load',{
			cari_simpanan: $('#cari_simpanan').val()
		});
	});

	$("#cari_status").change(function(){
		$('#tbl-u').datagrid('load',{
			cari_status: $('#cari_status').val()
		});
	});

	$("#kode_transaksi").keyup(function(event){
		if(event.keyCode == 13){
			$("#btn_filter").click();
		}
	});

	$("#kode_transaksi").keyup(function(e){
		var isi = $(e.target).val();
		$(e.target).val(isi.toUpperCase());
	});

fm_filter_tgl();
}); // ready

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
		doSearch();
	});
}
</script>

<script type="text/javascript">
var url;

function form_select_clear() {
	$('select option')
	.filter(function() {
		return !this.value || $.trim(this.value).length == 0;
	})
	.remove();
	$('select option')
	.first()
	.prop('selected', true);	
}

function doSearch(){
	$('#tbl-u').datagrid('load',{
		cari_simpanan: $('#cari_simpanan').val(),
		kode_transaksi: $('#kode_transaksi').val(),
		tgl_dari: 	$('input[name=daterangepicker_start]').val(),
		tgl_sampai: $('input[name=daterangepicker_end]').val()
	});
}

function clearSearch(){
	location.reload();
}

function setuju(){

	var row = $('#tbl-u').datagrid('getSelections');

	var check = [];
	if(row.length > 0){
		for(var i = 0 , len = row.length; i < len; i++){
		    check.push(row[i].status_id);
		}	
		// console.log($.inArray('1', check));
		// console.log(check);
		if($.inArray('1', check) == 0|| $.inArray('2', check) == 0){

			$.messager.alert('Warning','Maaf, Ajuan yang bisa di setujui hanya yang berstatus Menunggu Konfirmasi.');

		}else {
			$('#dialog-form').dialog('open').dialog('setTitle','Data Pengajuan Perubahan Simpanan');
			$('#form').form('clear');
			
			$('#tgl_transaksi_txt').val('<?php echo $txt_tanggal;?>');
			$('#tgl_transaksi').val('<?php echo $tanggal;?>');
			$('#kas option[value="0"]').prop('selected', true);
			$('#jumlah ~ span input').keyup(function(){
				var val_jumlah = $(this).val();
				$('#jumlah').numberbox('setValue', number_format(val_jumlah));
			});

			url = '<?php echo site_url('pengajuan_perubahan_simpanan/setuju'); ?>';
		}
	}else {
		$.messager.alert('Warning','Maaf, Silahkan Pilih Pengajuan terlebih dahulu.');
	}
}

function tolak(){

	var row = $('#tbl-u').datagrid('getSelections');

	var check = [];
	if(row.length > 0){
		for(var i = 0 , len = row.length; i < len; i++){
		    check.push(row[i].status_id);
		}	
		if($.inArray('1', check) == 0|| $.inArray('2', check) == 0){

			$.messager.alert('Warning','Maaf, Ajuan yang bisa ditolak hanya yang berstatus Menunggu Konfirmasi.');

		}else {
			$('#dialog-tolak').dialog('open').dialog('setTitle','Data Pengajuan Perubahan Simpanan');
			$('#form').form('clear');
		}
	}else {
		$.messager.alert('Warning','Maaf, Silahkan Pilih Pengajuan terlebih dahulu.');
	}
}


function save() {
	var row = $('#tbl-u').datagrid('getSelections');
	//validasi teks kosong
	var tgl_transaksi = $("#tgl_transaksi").val();
	var ket = $("#ket").val();

	var isValid = $('#form').form('validate');
	if (isValid) {

		$.ajax({
			type	: "POST",
			url: url,
			data	: {data:row, ket:ket, tgl_transaksi:tgl_transaksi},
			dataType : 'json',
			success	: function(result){
				if(result.ok) {

					jQuery('#dialog-form').dialog('close');
					$('#tbl-u').datagrid('reload');
					$.messager.show({
						title:'<div><i class="fa fa-info"></i> Informasi</div>',
						msg: result.msg,
						timeout:2000,
						showType:'slide'
					});

				}
			}
		});

	} else {
		$.messager.show({
			title:'<div><i class="fa fa-info"></i> Informasi</div>',
			msg: '<div class="text-red"><i class="fa fa-ban"></i> Maaf, Lengkapi seluruh pengisian data.</div>',
			timeout:2000,
			showType:'slide'
		});
	}
}

function save_tolak() {
	var row = $('#tbl-u').datagrid('getSelections');
	//validasi teks kosong
	var kas = $("#kas").val();
	var tgl_transaksi = $("#tgl_transaksi").val();
	var ket = $("#ket").val();

	if(kas == 0) {
		$.messager.show({
			title:'<div><i class="fa fa-warning"></i> Peringatan ! </div>',
			msg: '<div class="text-red"><i class="fa fa-ban"></i> Maaf, Simpan Ke Kas belum dipilih.</div>',
			timeout:2000,
			showType:'slide'
		});
		$("#kas").focus();
		return false;
	}

	var isValid = $('#form').form('validate');
	if (isValid) {

		$.ajax({
			type	: "POST",
			url: '<?php echo site_url('pengajuan_perubahan_simpanan/tolak');?>',
			data	: {data:row, ket:ket},
			dataType : 'json',
			success	: function(result){
				if(result.ok) {
					jQuery('#dialog-tolak').dialog('close');
					$('#tbl-u').datagrid('reload');
					$.messager.show({
						title:'<div><i class="fa fa-info"></i> Informasi</div>',
						msg: result.msg,
						timeout:2000,
						showType:'slide'
					});

				}
			}
		});

	} else {
		$.messager.show({
			title:'<div><i class="fa fa-info"></i> Informasi</div>',
			msg: '<div class="text-red"><i class="fa fa-ban"></i> Maaf, Lengkapi seluruh pengisian data.</div>',
			timeout:2000,
			showType:'slide'
		});
	}
}
function hapus(){  
	var row = $('#tbl-u').datagrid('getSelections');

	var check = [];
	if(row.length > 0){
		for(var i = 0 , len = row.length; i < len; i++){
		    check.push(row[i].status_id);
		}	
		if($.inArray('1', check) == 0){
			$.messager.alert('Warning','Maaf, Ajuan yang sudah disetujui tidak bisa di hapus.');
		}else {
			$.messager.confirm('Konfirmasi','Apakah Anda akan menghapus data ini ?',function(r){  
				if (r){  
					$.ajax({
						type	: "POST",
						url		: "<?php echo site_url('pengajuan_perubahan_simpanan/delete'); ?>",
						data	: {data:row},
						success	: function(result){
							var result = eval('('+result+')');
							$.messager.show({
								title:'<div><i class="fa fa-info"></i> Informasi</div>',
								msg: result.msg,
								timeout:2000,
								showType:'slide'
							});
							if(result.ok) {
								$('#tbl-u').datagrid('reload');
							}
						},
						error : function (){
							$.messager.show({
								title:'<div><i class="fa fa-warning"></i> Peringatan !</div>',
								msg: '<div class="text-red"><i class="fa fa-ban"></i> Maaf, Terjadi kesalahan koneksi, silahkan muat ulang !</div>',
								timeout:2000,
								showType:'slide'
							});
						}
					});  
				}  
			}); 
			$('.messager-button a:last').focus();
		}
	}else {
		$.messager.alert('Warning','Maaf, ASilahkan Pilih Pengajuan terlebih dahulu.');
	}

}

function cetak () {
	var cari_simpanan 	= $('#cari_simpanan').val();
	var kode_transaksi 	= $('#kode_transaksi').val();
	var tgl_dari			= $('input[name=daterangepicker_start]').val();
	var tgl_sampai			= $('input[name=daterangepicker_end]').val();
	
	var win = window.open('<?php echo site_url("cetak_pengajuan/laporan/3/?cari_simpanan=' + cari_simpanan + '&kode_transaksi=' + kode_transaksi + '&tgl_dari=' + tgl_dari + '&tgl_sampai=' + tgl_sampai + '"); ?>');
	if (win) {
		win.focus();
	} else {
		alert('Popup jangan di block');
	}
}
</script>

