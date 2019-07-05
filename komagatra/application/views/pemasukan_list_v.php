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
.glyphicon	{
	font-family: "Glyphicons Halflings"
}
</style>

<?php 
// buaat tanggal sekarang
	$tanggal = date('Y-m-d H:i');
	$tanggal_arr = explode(' ', $tanggal);
	$txt_tanggal = jin_date_ina($tanggal_arr[0]);
	// $txt_tanggal .= ' - ' . $tanggal_arr[1];
?>

<!-- Data Grid -->
<table   id="dg" 
class="easyui-datagrid"
title="Data Transaksi Pemasukan Kas" 
style="width:auto; height: auto;" 
url="<?php echo site_url('pemasukan_kas/ajax_list'); ?>" 
pagination="true" rownumbers="true" 
fitColumns="true" singleSelect="true" collapsible="true"
sortName="tgl" sortOrder="DESC"
toolbar="#tb"
striped="true">
<thead>
	<tr>
		<th data-options="field:'id', sortable:'true',halign:'center', align:'center'" hidden="true">ID</th>
		<th data-options="field:'tgl_transaksi',halign:'center', align:'center'" hidden="true">Tanggal</th>
		<th data-options="field:'tgl_transaksi_txt', width:'20', halign:'center', align:'center'">Tanggal Transaksi</th>
		<th data-options="field:'id_txt', width:'17', halign:'center', align:'center'">Kode Transaksi</th>
		<th data-options="field:'jenis', width:'30', halign:'center', align:'left'">Jenis Pemasukan</th>
		<th data-options="field:'ket', width:'30', halign:'center', align:'left'">Uraian</th>
		<th data-options="field:'kas_id',width:'20', halign:'center', align:'center'" hidden="true" >Untuk Kas</th>
		<th data-options="field:'kas_id_txt',width:'20', halign:'center', align:'left'" >Debit</th>
		<th data-options="field:'akun_id',width:'20', halign:'center', align:'center'" hidden="true" >Dari Akun</th>
		<th data-options="field:'akun_id_txt',width:'20', halign:'center', align:'left'" >Kredit</th>
		<th data-options="field:'jumlah', width:'15', halign:'center', align:'right'">Jumlah</th>
		<th data-options="field:'user', width:'15', halign:'center', align:'center'">User </th>
		<th data-options="field:'nama_member',width:'20', halign:'center', align:'center'" hidden="true" >Nama Member</th>
		<th data-options="field:'tbl', sortable:'true',halign:'center', align:'center'" hidden="true">TBL</th>
	</tr>
</thead>
</table>

<!-- Toolbar -->
<div id="tb" style="height: 35px;">
	<div style="vertical-align: middle; display: inline; padding-top: 15px;">
		<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="create()">Tambah </a>
		<!-- <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="update()">Edit</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="hapus()">Hapus</a> -->
	</div>
	<div class="pull-right" style="vertical-align: middle;">
		<div id="filter_tgl" class="input-group" style="display: inline;">
			<button class="btn btn-default" id="daterange-btn" style="line-height:16px;border:1px solid #ccc">
				<i class="fa fa-calendar"></i> <span id="reportrange"><span>Pilih Tanggal</span></span>
				<i class="fa fa-caret-down"></i>
			</button>
		</div>
		
		<span>Cari :</span>
		<input name="kode_transaksi" id="kode_transaksi" size="20" placeholder="[Kode Transaksi]"style="line-height:25px;border:1px solid #ccc">
		<a href="javascript:void(0);" id="btn_filter" class="easyui-linkbutton" iconCls="icon-search" plain="false" onclick="doSearch()">Cari</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="false" onclick="cetak()">Cetak Laporan</a>
		<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-clear" plain="false" onclick="clearSearch()">Hapus Filter</a>
	</div>
</div>

<!-- Dialog Form -->
<div id="dialog-form" class="easyui-dialog" show= "blind" hide= "blind" modal="true" resizable="false" style="width:370px; height:400px; padding-left:20px; padding-top:20px; " closed="true" buttons="#dialog-buttons" style="display: none;">
	<form id="form" method="post" novalidate>
		<table style="height:200px" >
			<tr style="height:35px">
				<td>Jenis Pemasukan </td>
				<td>:</td>
				<td>
					<select id="jenis" name="jenis" style="width:195px; height:25px" class="easyui-validatebox" required="true">
						<option value="0" selected=""> -- Pilih Jenis Pemasukan --</option>	
						<option value="Pembayaran Angsuran">Pembayaran Angsuran</option>
						<option value="Setoran Sukarela">Setoran Sukarela</option>
						<!-- <option value="Pelunasan Pinjaman">Pelunasan Pinjaman</option> -->
						<option value="Lain-lain">Lain-lain</option>
					</select>
				</td>	
			</tr>
			<tr style="height:35px;display:none;" id="box-angsuran">
				<td>Angsuran </td>
				<td>:</td>
				<td>
					<select id="angsuran" name="angsuran" class="easyui-validatebox" required="true">
						<option value="">-- Pilih Bayar Angsuran -- </option>
						<?php
						if(!empty($angsuran)){
							foreach ($angsuran as $key => $ag) {
								?>
								<option value="<?php echo $ag['id'];?>" data-nominal="<?php echo $ag['ags_per_bulan'];?>" data-nama="<?php echo $ag['nama'];?>"><?php echo 'TPJ'.sprintf('%05d', $ag['id']).' - '.$ag['nama'];?></option>
								<?php	
							}
						}
						?>
					</select>
				</td>	
			</tr>
			<tr style="height:35px;display:none;" id="box-setoran">
				<td>Setoran Sukarela </td>
				<td>:</td>
				<td>
					<select id="setoran" name="setoran" class="easyui-validatebox" required="true">
						<option value="">-- Pilih Bayar Setoran -- </option>
						<?php
						if(!empty($setoran)){
							foreach ($setoran as $key => $pj) {
								?>
								<option value="<?php echo $pj['id'];?>" data-nama="<?php echo $pj['nama'];?>" data-nominal="<?php echo $pj['nominal'];?>"><?php echo $pj['ajuan_id'].' - '.$pj['nama'];?></option>
								<?php	
							}
						}
						?>
					</select>
				</td>	
			</tr>
			<tr style="height:35px">
				<td>Tanggal Transaksi </td>
				<td>:</td>
				<td>
					<div class="input-group date dtpicker col-md-5" style="z-index: 9999 !important;">
						<input type="text" name="tgl_transaksi_txt" id="tgl_transaksi_txt" style="width:150px; height:25px" required="true" readonly="readonly" />
						<input type="hidden" name="tgl_transaksi" id="tgl_transaksi" />
						<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
					</div>
				</td>	
			</tr>
			<tr style="height:35px">
				<td> Nama Penyetor </td>
				<td>:</td>
				<td>
					<input id="nama_member" name="nama_member" style="width:190px; height:20px" >
				</td>	
			</tr>
			<tr style="height:35px">
				<td>Jumlah </td>
				<td>:</td>
				<td>
					<input class="easyui-numberbox" id="jumlah" name="jumlah" data-options="precision:0,groupSeparator:',',decimalSeparator:'.'" class="easyui-validatebox" required="true" style="width:195px; height:25px"  />
				</td>	
			</tr>
			
			<tr style="height:35px">
				<td> Keterangan </td>
				<td>:</td>
				<td>
					<input id="ket" name="ket" style="width:190px; height:20px" >
				</td>	
			</tr>
			<tr style="height:100px;vertical-align: top;" id="dari-akun">
				<td>Dari Akun</td>
				<td>:</td>
				<td>
					<select id="akun_id" name="akun_id[]" style="width:195px; height:25px" class="easyui-validatebox" required="true" multiple="">	
						<option value="0"> -- Pilih Akun --</option>			
						<?php	
						foreach ($akun_id as $row) {
							if(strlen($row->kd_aktiva) != 1){
								$kode ='';
								$nama_akun = $row->jns_trans;
							}else{
								$kode ='';
								$nama_akun = $row->jns_trans;
							}
							echo '<option value="'.$row->id.'">
							'.$kode.' '.$nama_akun.'
							</option>';
						}
						?>
					</select>
				</td>
			</tr>
			<tr style="height:35px">
				<td>Untuk Kas</td>
				<td>:</td>
				<td>
					<select id="kas" name="kas_id" style="width:195px; height:25px" class="easyui-validatebox" required="true">
						
						<?php	
						foreach ($kas_id as $row) {
							echo '<option value="'.$row->id.'">'.$row->nama.'</option>';
						}
						?>
					</select>
				</td>
			</tr>
		</table>
	</form>
</div>

<!-- Dialog Button -->
<div id="dialog-buttons">
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="save()">Simpan</a>
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:jQuery('#dialog-form').dialog('close')">Batal</a>
</div>

<!-- Dialog Form -->
<div id="dialog-print" class="easyui-dialog" show= "blind" hide= "blind" modal="true" resizable="false" style="width:370px; height:340px; padding-left:20px; padding-top:20px; " closed="true" buttons="#dialog-buttons-print" style="display: none;">
	<form id="form_print" method="post" novalidate>
		<table style="height:200px" >
			<tr style="height:10px">
				<td> Diterima Oleh </td>
				<td>:</td>
				<td>
					<input id="nama_operator" name="nama_operator" style="width:190px; height:20px" >
				</td>	
			</tr>
			<tr style="height:10px">
				<td> Diketahui Oleh </td>
				<td>:</td>
				<td>
					<input id="nama_admin" name="nama_admin" style="width:190px; height:20px" >
				</td>	
			</tr>
			<tr style="height:10px">
				<td> Nama Penyetor </td>
				<td>:</td>
				<td>
					<input id="nama_member_print" name="nama_member_print" style="width:190px; height:20px" >
				</td>	
			</tr>
		</table>
	</form>
</div>

<!-- Dialog Button -->
<div id="dialog-buttons-print">
	<button type="button" class="easyui-linkbutton" iconCls="icon-ok" onclick="save_print()">Simpan</button>
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:jQuery('#dialog-print').dialog('close')">Batal</a>
</div>

<script type="text/javascript">

	$('#angsuran').select2({
		placeholder : '-- Pilih Angsuran --',
		width : '100%'
	});
	$('#setoran').select2({
		placeholder : '-- Pilih Setoran --',
		width : '100%'
	});
	$('#akun_id').select2({
		placeholder : '-- Pilih Akun --',
		width : '100%',
		height: '200px'
	});
	$('#kas').select2({
		placeholder : '-- Pilih Kas --',
		width : '100%'
	});

$(document).ready(function() {
	$(".dtpicker").datetimepicker({
		language:  'id',
		weekStart: 1,
		autoclose: true,
		todayBtn: true,
		todayHighlight: true,
		pickerPosition: 'bottom-right',
		format: 'dd MM yyyy',
		linkField: "tgl_transaksi",
		linkFormat: "yyyy-mm-dd",
		minView : 2,
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


	$('#jenis').on('change',function(){
		
		$('#box-angsuran').hide();
		$('#box-setoran').hide();
		$('#dari-akun').show();
		$('#angsuran').select2('val','');
		$('#setoran').select2('val','');
		$('#jumlah').numberbox('setValue', 0);
		$('#nama_member').val('');
		var val =  $(this).val();
		if(val= 'Pembayaran Angsuran'){
			$('#box-angsuran').show();
			$('#dari-akun').hide();
		}else if(val == 'Setoran Sukarela'){
			$('#box-setoran').show();
			$('#dari-akun').hide();
		}
	});

	$('#angsuran').on('change',function(){
		var nominal = $('option:selected', this).attr('data-nominal');
		var nama_member = $('option:selected', this).attr('data-nama');
		$('#jumlah').numberbox('setValue', number_format(nominal));
		$('#nama_member').val(nama_member);
	});

	$('#setoran').on('change',function(){
		var nominal = $('option:selected', this).attr('data-nominal');
		var nama_member = $('option:selected', this).attr('data-nama');
		$('#jumlah').numberbox('setValue', number_format(nominal));
		$('#nama_member').val(nama_member);
	});

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
	$('#dg').datagrid('load',{
		kode_transaksi: $('#kode_transaksi').val(),
		tgl_dari: 	$('input[name=daterangepicker_start]').val(),
		tgl_sampai: $('input[name=daterangepicker_end]').val()
	});
}

function clearSearch(){
	location.reload();
}

function create(){
	$('#dialog-form').dialog('open').dialog('setTitle','Tambah Data');
	$('#form').form('clear');
	$('#tgl_transaksi_txt').val('<?php echo $txt_tanggal;?>');
	$('#tgl_transaksi').val('<?php echo $tanggal;?>');
	$('#kas option[value="1"]').prop('selected', true);
	$('#akun_id option[value="0"]').prop('selected', true);
	$('#jenis option[value="0"]').prop('selected', true);

	$('#angsuran').hide();
	$('#setoran').hide();
	$('#jumlah ~ span input').keyup(function(){
		var val_jumlah = $(this).val();
		$('#jumlah').numberbox('setValue', number_format(val_jumlah));
	});
	url = '<?php echo site_url('pemasukan_kas/create'); ?>';
}

function save() {
	var string = $("#form").serialize();
	var kas = $("#kas").val();
	var akun_id = $("#akun_id").val();
	var string = $("#form").serialize();
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

	// if(akun_id == 0) {
	// 	$.messager.show({
	// 		title:'<div><i class="fa fa-warning"></i> Peringatan ! </div>',
	// 		msg: '<div class="text-red"><i class="fa fa-ban"></i> Maaf, Jenis Akun belum dipilih.</div>',
	// 		timeout:2000,
	// 		showType:'slide'
	// 	});
	// 	$("#akun_id").focus();
	// 	return false;
	// }

	var isValid = $('#form').form('validate');
	if (isValid) {
		$.ajax({
			type	: "POST",
			url: url,
			data	: string,
			success	: function(result){
				var result = eval('('+result+')');
				$.messager.show({
					title:'<div><i class="fa fa-info"></i> Informasi</div>',
					msg: result.msg,
					timeout:2000,
					showType:'slide'
				});
				if(result.ok) {
					jQuery('#dialog-form').dialog('close');
					//clearSearch();
					$('#dg').datagrid('reload');
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

function update(){
	var row = jQuery('#dg').datagrid('getSelected');

	if(row){
		jQuery('#dialog-form').dialog('open').dialog('setTitle','Edit Data Setoran');
		jQuery('#form').form('load',row);
		url = '<?php echo site_url('pemasukan_kas/update'); ?>/' + row.id;
		$('#jumlah ~ span input').keyup(function(){
			var val_jumlah = $(this).val();
			$('#jumlah').numberbox('setValue', number_format(val_jumlah));
		});
		
	}else {
		$.messager.show({
			title:'<div><i class="fa fa-warning"></i> Peringatan !</div>',
			msg: '<div class="text-red"><i class="fa fa-ban"></i> Maaf, Data harus dipilih terlebih dahulu </div>',
			timeout:2000,
			showType:'slide'
		});
	}
}

function hapus(){  
	var row = $('#dg').datagrid('getSelected');  
	if (row){ 
		$.messager.confirm('Konfirmasi','Apakah Anda akan menghapus data kode transaksi : <code>' + row.id_txt + '</code> ?',function(r){  
			if (r){  
				$.ajax({
					type	: "POST",
					url		: "<?php echo site_url('pemasukan_kas/delete'); ?>",
					data	: 'id='+row.id,
					success	: function(result){
						var result = eval('('+result+')');
						$.messager.show({
							title:'<div><i class="fa fa-info"></i> Informasi</div>',
							msg: result.msg,
							timeout:2000,
							showType:'slide'
						});
						if(result.ok) {
							$('#dg').datagrid('reload');
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
	}  else {
		$.messager.show({
			title:'<div><i class="fa fa-warning"></i> Peringatan !</div>',
			msg: '<div class="text-red"><i class="fa fa-ban"></i> Maaf, Data harus dipilih terlebih dahulu </div>',
			timeout:2000,
			showType:'slide'
		});	
	}
	$('.messager-button a:last').focus();
}

function cetak () {
	var row = $('#dg').datagrid('getSelected'); 
	var kode_transaksi 	= $('#kode_transaksi').val();	
	if(row){

		var kode_transaksi = row.id_txt;
		$('#dialog-print').dialog('open').dialog('setTitle','Print Data');
		$('#form_print').form('clear');
		$('#nama_member_print').val(row.nama_member);

	}else {
		var tgl_dari			= $('input[name=daterangepicker_start]').val();
		var tgl_sampai			= $('input[name=daterangepicker_end]').val();

		var win = window.open('<?php echo site_url("cetak/cetak_laporan/1/kas/?kode_transaksi=' + kode_transaksi + '&tgl_dari=' + tgl_dari + '&tgl_sampai=' + tgl_sampai + '"); ?>');
		if (win) {
			win.focus();
		} else {
			alert('Popup jangan di block');
		}
	}
}

function save_print()
{
	var row = $('#dg').datagrid('getSelected'); 
	var kode_transaksi 	= $('#kode_transaksi').val();	
	var nama_operator 	= $('#nama_operator').val();	
	var nama_admin 	= $('#nama_admin').val();	
	var nama_member_print 	= $('#nama_member_print').val();	

	if(row){
		var kode_transaksi = row.id_txt;
		$.ajax({
			type	: "POST",
			url		: '<?php echo site_url('cetak/update_cetak');?>',
			dataType : 'json',
			data : {tbl:row.tbl, id:row.id, id_txt:row.id_txt, nama_operator:nama_operator, nama_admin:nama_admin , nama_member:nama_member_print},
			success	: function(result){
				if(result.ok) {
					jQuery('#dialog-print').dialog('close');
					kode_transaksi = result.id_txt;
					var win = window.open('<?php echo site_url("cetak/cetak_laporan/1/kas/'+row.tbl+'?kode_transaksi=' + kode_transaksi+'&tgl_dari=&tgl_sampai="); ?>','_blank');
					if (win) {
						win.focus();
					} else {
						alert('Popup jangan di block');
					}
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
}

</script>