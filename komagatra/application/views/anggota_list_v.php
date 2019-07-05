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


<!-- Data Grid -->
<table   id="tbl-u" 
title="Data Anggota" 
style="width:auto; height: auto;" 
url="<?php echo site_url('anggota/ajax_list'); ?>" 
pagination="true" rownumbers="true" 
fitColumns="true" collapsible="true"
sortName="id" sortOrder="desc"
toolbar="#tb"
striped="true">
<thead>
	<tr>
		<th data-options="field:'id', sortable:'true',halign:'center', align:'center'" hidden="true">ID</th>
		<th data-options="field:'photo', width:5, halign:'center', align:'center'">Photo</th>
		<th data-options="field:'id_anggota',  halign:'center', align:'center'">ID Anggota</th>
		<!-- <th data-options="field:'anggota_baru',halign:'center', align:'left'">Anggota Baru</th> -->
		<th data-options="field:'username',halign:'center', align:'left'">Username</th>
		<th data-options="field:'nama_lengkap',  halign:'center', align:'left'">Nama Lengkap</th>
		<th data-options="field:'jenis_kelamin', halign:'center', align:'left'" >Jenis Kelamin</th>
		<!-- <th data-options="field:'alamat', halign:'center', align:'center'">Alamat</th> -->
		<th data-options="field:'kota', halign:'center', align:'center'">Kota</th>
		<th data-options="field:'jabatan', halign:'center', align:'center'">Jabatan</th>
		<th data-options="field:'departement', halign:'center', align:'center'">Direktorat</th>
		<th data-options="field:'tgl_regis', halign:'center', align:'center'">Tanggal Registrasi</th>
		<th data-options="field:'aktif', halign:'center', align:'center'">Aktif Keanggotaan</th>
		<th data-options="field:'aksi', halign:'center', align:'center'">Aksi</th>
	</tr>
</thead>
</table>

<!-- Toolbar -->
<div id="tb" style="height: 35px;">
	<div style="vertical-align: middle; display: inline; padding-top: 15px;">
		<a href="<?php echo site_url('anggota/add');?>" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="setuju()">Tambah Data Anggota</a>
	</div>
	<div class="pull-right" style="vertical-align: middle;">
		<a href="javascript:void(0)" class="easyui-linkbutton"  plain="false" onclick="ekspor()"><i class='fa fa-excel'></i> Ekspor</a>
		<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="false" onclick="cetak()">Cetak</a>
	</div>
</div>


<script type="text/javascript">
	$('#tbl-u').datagrid();

	function hapus(id)
	{
		if(confirm('Apakah Anda Yakin ingin menghapus data ini ??')){
			window.location.href = '<?php echo site_url('anggota/hapus/');?>/'+id;
		}
	}

	function cetak(){
		$('#tbl-u').datagrid('print','DataGrid'); 
	}
	function ekspor(){
		$('#tbl-u').datagrid('toExcel','anggota_excel.xls');
	}
</script>