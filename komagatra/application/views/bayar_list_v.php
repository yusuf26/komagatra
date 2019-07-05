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

	$tanggal = date('Y-m-d H:i');
	$tanggal_arr = explode(' ', $tanggal);
	$txt_tanggal = jin_date_ina($tanggal_arr[0]);
	$txt_tanggal .= ' - ' . $tanggal_arr[1];

?>

<!-- Data Grid -->
<table   id="dg" 
<?php /*class="easyui-datagrid" */ ?>
title="Data Angsuran" 
style="width:auto; height: auto;" 
url="<?php echo site_url('bayar/ajax_list'); ?>" 
pagination="true" rownumbers="true" 
fitColumns="true"  collapsible="true"
sortName="tgl_pinjam" sortOrder="desc"
toolbar="#tb"
striped="true">
<thead>
	<tr>
		<th data-options="field:'id',halign:'center', align:'center'" hidden="true">ID</th>
		<th data-options="field:'ck', width:'13',halign:'center', align:'center'" checkbox="true"></th>
		<th data-options="field:'id_txt', width:'13',halign:'center', align:'center'">Kode</th>
		<th data-options="field:'tgl_pinjam_txt', width:'15', halign:'center', align:'center'">Tanggal Pinjam</th>
		<th data-options="field:'anggota_id', width:'14', halign:'center', align:'center'">ID Anggota</th>
		<th data-options="field:'anggota_id_txt', width:'35', halign:'center', align:'left'">Nama Anggota</th>
		<th data-options="field:'jumlah', width:'15', halign:'center', align:'right'" >Pokok <br> Pinjaman</th>
		<th data-options="field:'jenis', width:'30', halign:'center', align:'left'" >Jenis <br> Pinjaman</th>
		<th data-options="field:'lama_angsuran_txt', width:'14', halign:'center', align:'center'">Lama <br> Pinjam</th>
		<th data-options="field:'ags_pokok', width:'15', halign:'center', align:'right'">Angsuran <br> Pokok</th>
		<th data-options="field:'bunga', width:'15', halign:'center', align:'right'">Bunga <br> Angsuran</th>
		<th data-options="field:'biaya_adm', width:'15', halign:'center', align:'right'">Biaya <br> Admin </th>
		<th data-options="field:'angsuran_bln', width:'15', halign:'center', align:'right'">Angsuran <br> Per Bulan</th> 
		<th data-options="field:'bayar', halign:'center', align:'center'">Bayar</th>
	</tr>
</thead>
</table>

<!-- Toolbar -->
<div id="tb" style="height: 35px;">
	<div class="pull-left">
		<a href="javascript:void(0);" class="btn btn-sm btn-success" id="lunasAll">Pelunasan</a>
		<a href="javascript:void(0);" class="btn btn-sm btn-primary" id="angsuranAll">Bayar Angsuran</a>
	</div>
	<div class="pull-right" style="vertical-align: middle;">
		<div id="filter_tgl" class="input-group" style="display: inline;">
			<button class="btn btn-default" id="daterange-btn">
				<i class="fa fa-calendar"></i> <span id="reportrange"><span>Pilih Tanggal</span></span>
				<i class="fa fa-caret-down"></i>
			</button>
		</div>
		<span>Cari :</span>
		<input name="kode_transaksi" id="kode_transaksi" size="23" placeholder="Kode Transaksi"  style="line-height:23px;border:1px solid #ccc">
		<input name="cari_nama" id="cari_nama" size="23" placeholder="Nama Anggota" style="line-height:22px;border:1px solid #ccc">

		<a href="javascript:void(0);" id="btn_filter" class="easyui-linkbutton" iconCls="icon-search" plain="false" onclick="doSearch()">Cari</a>
		<a href="javascript:void(0);" class="easyui-linkbutton" iconCls="icon-clear" plain="false" onclick="clearSearch()">Hapus Filter</a>
	</div>
</div>



<!-- Dialog form input pelunasan -->
<div id="dialog-form" class="easyui-dialog" show= "blind" hide= "blind" modal="true" resizable="false" style="width:400px; height:340px; padding: 20px 25px" closed="true" buttons="#dialog-buttons" style="display: none;">
	<form id="form" method="post" novalidate>
		<table>
		<tr style="height:35px">
			<td> Tanggal Transaksi</td>
			<td> :</td>
			<td>
				<div class="input-group date dtpicker col-md-5" style="z-index: 9999 !important;">
					<input type="text" name="tgl_transaksi_txt" id="tgl_transaksi_txt" style="width:155px; height:25px" required="true" readonly="readonly" />
					<input type="hidden" name="tgl_transaksi" id="tgl_transaksi" />
					<div class="input-group-addon"><i class="fa fa-calendar"></i></div>
				</div>
			</td>	
		</tr>	
		<tr style="height:30px">
			<td> Jumlah Bayar</td>
			<td> :</td>
			<td> <input type="text" class="easyui-numberbox" id="jumlah_bayar" name="jumlah_bayar" data-options="precision:0,groupSeparator:',',decimalSeparator:','," class="easyui-validatebox" required="true" style="width:201px; height:23px" readonly="" />
				</td>
		</tr>	
		<tr>
			<td> Simpan Ke Kas</td>
			<td> :</td>
			<td> 
				<select id="kas" name="kas_id" style="width:200px; height:23px" class="easyui-validatebox" required="true">
						<option value="0"> -- Pilih Kas -- </option>			
						<?php	
						foreach ($kas_id as $row) {
							echo '<option value="'.$row->id.'">'.$row->nama.'</option>';
						}
						?>
					</select>
			</td>
		</tr>
		<tr style="height:35px">
			<td> Keterangan</td>
			<td> :</td>
			<td> <input id="ket" name="ket" style="width:195px; height:20px" > </td>	
		</tr>
			<input type="hidden" value="lunas" id="check_lunas" />
			<span id="angsuran_ke" class="inputform" style="color:#fff">
			<span id="sisa_ags" class="inputform" style="color:#fff"></span>
			<span id="denda" class="inputform" style="color:#fff"></span>
			<input type="hidden" id="denda_val" name="denda_val" value="" />
		</table>
	</form>
</div>
<!-- Dialog Button -->
<div id="dialog-buttons">
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" onclick="save()">Simpan</a>
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:jQuery('#dialog-form').dialog('close')">Batal</a>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$(".dtpicker").datetimepicker({
			language:  'id',
			weekStart: 1,
			autoclose: true,
			todayBtn: true,
			todayHighlight: true,
			pickerPosition: 'bottom-right',
			format: "dd MM yyyy - hh:ii",
			linkField: "tgl_transaksi",
			linkFormat: "yyyy-mm-dd hh:ii"
		}).on('changeDate', function(ev){
			hitung_denda();
		});
	$('#dg').datagrid({
		rowStyler:function(index,row){
			if (row.merah == 1){
				return 'background-color:pink;color:blue;font-weight:bold;';
			}
		}
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


}); //ready

$('#lunasAll').click(function(){
	
	var dg = $('#dg').datagrid('getChecked');
	
	if(dg.length > 0){
		if(confirm('Apakah Anda yakin ingin melakukan pelunasan ???')){
			jQuery('#dialog-form').dialog('open').dialog('setTitle','Pelunasan');
			jQuery('#tgl_transaksi_txt').val('<?php echo $txt_tanggal;?>');
			jQuery('#tgl_transaksi').val('<?php echo $tanggal;?>');
			$('#check_lunas').val('lunas');
			$('#jumlah_bayar ~ span input').keyup(function(){
				var val_jumlah = $(this).val();
				$('#jumlah_bayar').numberbox('setValue', number_format(val_jumlah));
			});
			var jumlah_bayar =0;
			$.each(dg, function(k, v) {
				var angsuran_bayar = parseInt(v.angsuran * (v.total_angsuran - v.angsuran_ke + 1));
			    jumlah_bayar += angsuran_bayar;
			    // console.log(angsuran_bayar);
			});
			$('#jumlah_bayar').numberbox('setValue', number_format(jumlah_bayar));
			$('#jumlah_bayar ~ span input').val(jumlah_bayar);
		}
	}else {
		alert('Silahkan Check terlebih dahulu ');
	}
	
});

$('#angsuranAll').click(function(){
	
	var dg = $('#dg').datagrid('getChecked');
	
	console.log(dg);
	if(dg.length > 0){
		if(confirm('Apakah Anda yakin ingin melakukan pembayaran angsuran ini ???')){
			jQuery('#dialog-form').dialog('open').dialog('setTitle','Bayar Angsuran');
			jQuery('#tgl_transaksi_txt').val('<?php echo $txt_tanggal;?>');
			jQuery('#tgl_transaksi').val('<?php echo $tanggal;?>');

			$('#check_lunas').val('angsur');

			$('#jumlah_bayar ~ span input').keyup(function(){
				var val_jumlah = $(this).val();
				$('#jumlah_bayar').numberbox('setValue', number_format(val_jumlah));
			});
			var jumlah_bayar =0;
			$.each(dg, function(k, v) {
				var angsuran_bayar = parseInt(v.angsuran);
			    jumlah_bayar += angsuran_bayar;
			    // console.log(angsuran_bayar);
			});
			
			$('#jumlah_bayar').numberbox('setValue', number_format(jumlah_bayar));
			$('#jumlah_bayar ~ span input').val(jumlah_bayar);
		}
	}else {
		alert('Silahkan Check terlebih dahulu ');
	}
	
});


function save()
{
	var tgl_transaksi = $('#tgl_transaksi').val();
	var dg = $('#dg').datagrid('getChecked');
	var kas = $('#kas').val();
	var ket = $('#ket').val();
	var check_lunas = $('#check_lunas').val();

	$.ajax({
		type	: "POST",
		url		: "<?php echo site_url('bayar/lunasAll')?>",
		data : {data:dg, tgl_transaksi:tgl_transaksi, kas:kas, ket:ket, check_lunas:check_lunas},
		dataType : 'json',
		success	: function(result){
			if(result.output == true){
				$('#dg').datagrid('reload');
				$.messager.show({
					title:'<div><i class="fa fa-info"></i> Informasi</div>',
					msg: 'Berhasil melakukan pembayaran',
					timeout:2000,
					showType:'slide'
				});
				jQuery('#dialog-form').dialog('close');
			}

		},
		error : function() {
			alert('Terjadi Kesalahan Kneksi');
		}
	});
}

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
		cari_nama: $('#cari_nama').val(),
		tgl_dari: 	$('input[name=daterangepicker_start]').val(),
		tgl_sampai: $('input[name=daterangepicker_end]').val()
	});
}

function clearSearch(){
	location.reload();
}
</script>