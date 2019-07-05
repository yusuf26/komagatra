
<style type="text/css">
    .form-horizontal .control-label{
        text-align: left;
        font-size: 15px;
        font-weight: normal;
        padding-top: 4px;
    }
    .list-group{
        margin-bottom: 0;
    }
</style>


<?php
$last = $this->db->order_by('id',"desc")
        ->select('anggota_id')
        ->limit(1)
        ->get('tbl_anggota')
        ->row();
$id_anggota = str_replace('AG', '', $last->anggota_id);
$id_anggota = 'AG'.sprintf('%04d', $id_anggota + 1);
if(!empty($id)){
    $id_anggota = $ag['anggota_id'];
}
?>

<div class="panel panel-primary">
  <!-- Default panel contents -->
  <div class="panel-heading">Form Anggota</div>
    <?php
    if($this->session->flashdata('error_msg')){
        ?>
        <div class="alert alert-danger" role="alert">
        <?php echo $this->session->flashdata('error_msg');?>
        </div>
        <?php
    }
    ?>
  <!-- List group -->
    <form id="ff" action="<?php echo site_url('anggota/form_process');?>" method="post" enctype="multipart/form-data" class="form-horizontal">
        <input type="hidden" name="id" value="<?php echo $id;?>">
        <ul class="list-group">
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="">ID Anggota <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="anggota_id" required="" value="<?php echo $id_anggota;?>">
                    </div>
                    <div class="col-sm-5">
                        <label class="">Anggota Baru <span class="text-danger">*</span></label>
                        <select class="select2" name="anggota_baru" style="width:100%;" placeholder="Pilih Jenis Anggota" required="">
                            <option value="1" <?php if(isset($ag['anggota_baru']))if($ag['anggota_baru'] == 1) echo 'selected';?> >Ya</option>
                            <option value="0" <?php if(isset($ag['anggota_baru']))if($ag['anggota_baru'] == 0) echo 'selected';?>>Tidak</option>
                        </select>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" required="" value="<?php if(isset($ag)) echo $ag['nama'];?>">
                    </div>
                    <div class="col-sm-5">
                        <label class="">NIK </label>
                        <input type="text" class="form-control" name="nik" value="<?php if(isset($ag) && !empty($ag['nik'])) echo $ag['nik'];?>">
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    
                    <div class="col-sm-6">
                        <label class="">NPWP </label>
                        <input type="text" class="form-control" name="npwp" value="<?php if(isset($ag) && !empty($ag['npwp'])) echo $ag['npwp'];?>">
                    </div>
                    <div class="col-sm-5">
                        <label class="">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="identitas" required="" value="<?php if(isset($ag)) echo $ag['identitas'];?>">
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tmp_lahir" required="" value="<?php if(isset($ag)) echo $ag['tmp_lahir'];?>">
                    </div>
                    <div class="col-sm-5">
                        <label class="">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" required="" placeholder="Pilih Tanggal Lahir" id="tgl_lahir_txt" value="<?php if(isset($ag)) echo jin_date_ina($ag['tgl_lahir']);?>">
                        <input type="hidden" class="form-control" name="tgl_lahir" id="tgl_lahir" value="<?php if(isset($ag)) echo $ag['tgl_lahir'];?>">
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="select2" name="jk" style="width:100%;" placeholder="Pilih Jenis Kelamin" required="">
                            <option></option>
                            <option value="L" <?php if(isset($ag)) if($ag['jk'] == 'L' )echo 'selected';?> >Laki-laki</option>
                            <option value="P" <?php if(isset($ag)) if($ag['jk'] == 'P' )echo 'selected';?> >Perempuan</option>
                        </select>
                    </div>
                    <div class="col-sm-5">
                        <label class=""> Status <span class="text-danger">*</span></label>
                        <select class="select2" name="status" style="width:100%;" placeholder="Pilih Status" required="">
                            <option></option>
                            <?php
                            $arr_status = array('Belum Kawin' => 'Belum Kawin',
                                                'Kawin' => 'Kawin',
                                                'Cerai Hidup' => 'Cerai Hidup',
                                                'Cerai Mati' => 'Cerai Mati',
                                                'Lainnya' => 'Lainnya');
                            foreach ($arr_status as $s => $status) {
                                $s_selected = false;
                                if(isset($ag)){
                                    if($ag['status'] == $s){
                                        $s_selected = 'selected';
                                    }
                                }
                                ?>
                                <option value="<?php echo $s;?>" <?php echo $s_selected;?>><?php echo $status;?></option>
                                <?php    
                            }
                            ?>
                        </select>
                    </div>
                    
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-6">
                        <label class=""> Direktorat <span class="text-danger">*</span></label>
                        <select class="select2" name="departement" style="width:100%;" placeholder="Pilih Direktorat" required="">
                            <option></option>
                            <?php
                            $this->db->select('id_departement,departement');
                            $this->db->from('departement');
                            $query = $this->db->get();
                            if($query->num_rows()>0){
                                $result = $query->result();
                                foreach ($result as $d) {
                                    $d_selected = false;
                                    if(isset($ag)){
                                        if($ag['departement'] == $d->id_departement){
                                            $d_selected = 'selected';
                                        }
                                    }
                                    ?>
                                    <option value="<?php echo $d->id_departement;?>" <?php echo $d_selected;?>><?php echo $d->departement;?></option>
                                    <?php 
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-5">
                        <label class=""> Pekerjaan <span class="text-danger">*</span></label>
                      <select class="select2" name="pekerjaan" style="width:100%;" placeholder="Pilih Pekerjaan" required="">
                            <option></option>
                            <?php
                            $this->db->select('id_kerja,jenis_kerja');
                            $this->db->from('pekerjaan');
                            $query = $this->db->get();
                            if($query->num_rows()>0){
                                $result = $query->result();
                                foreach ($result as $val) {

                                    $p_selected = false;
                                    if(isset($ag)){
                                        if($ag['pekerjaan'] == $val->jenis_kerja){
                                            $p_selected = 'selected';
                                        }
                                    }

                                    ?>
                                    <option value="<?php echo $val->jenis_kerja;?>" <?php echo $p_selected;?>><?php echo $val->jenis_kerja;?></option>
                                    <?php    
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="">Agama <span class="text-danger">*</span></label>
                        <select class="select2" name="agama" style="width:100%;" placeholder="Pilih Direktorat" required="">
                            <option></option>
                            <?php
                            $arr_agama = array('Islam' => 'Islam',
                                                    'Katolik' => 'Katolik',
                                                    'Protestan' => 'Protestan',
                                                    'Hindu' => 'Hindu',
                                                    'Budha' => 'Budha',
                                                    'Lainnya' => 'Lainnya');
                            foreach ($arr_agama as $a => $agama) {

                                $a_selected = false;
                                if(isset($ag)){
                                    if($ag['agama'] == $a){
                                        $a_selected = 'selected';
                                    }
                                }

                                ?>
                                <option value="<?php echo $a;?>" <?php echo $a_selected;?>><?php echo $agama;?></option>
                                <?php    
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-5">
                        <label class="">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="5" name="alamat"><?php if(isset($ag)) echo $ag['alamat'];?></textarea>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="">Kota <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" name="kota" required="" value="<?php if(isset($ag)) echo $ag['kota'];?>">
                    </div>
                    <div class="col-sm-5">
                        <label class="">Nomor Telepon / HP <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" name="notelp" required="" value="<?php if(isset($ag)) echo $ag['notelp'];?>">
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    
                    <div class="col-sm-6">
                        <label class="">Tanggal Registrasi </label>
                        <input type="text" class="form-control" required="" placeholder="Pilih Tanggal Registrasi" id="tgl_daftar_txt" value="<?php if(isset($ag)) echo jin_date_ina($ag['tgl_daftar']);?>">
                      <input type="hidden" class="form-control" name="tgl_daftar" id="tgl_daftar" value="<?php if(isset($ag)) echo $ag['tgl_daftar'];?>">
                    </div>
                    <div class="col-sm-5">
                        <label class="">Jabatan <span class="text-danger">*</span></label>
                      <select class="select2" name="jabatan_id" style="width:100%;" placeholder="Pilih Jabatan" required="">
                            <option></option>
                            <?php
                            $arr_jabatan = array(1 => 'Anggota',2 => 'Ketua',3 => 'Bendahara I',4 => 'Bendahara II');
                            foreach ($arr_jabatan as $j => $jabatan) {


                                $j_selected = false;
                                if(isset($ag)){
                                    if($ag['jabatan_id'] == $j){
                                        $j_selected = 'selected';
                                    }
                                }

                                ?>
                                <option value="<?php echo $j;?>" <?php echo $j_selected;?>><?php echo $jabatan;?></option>
                                <?php    
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    
                    <div class="col-sm-6">
                        <label class="">Nama Bank</label>
                      <input type="text" class="form-control" name="nama_bank" value="<?php if(isset($ag)) echo $ag['nama_bank'];?>">
                    </div>
                    <div class="col-sm-5">
                        <label class="">No Rekening</label>
                      <input type="text" class="form-control" name="no_rekening" value="<?php if(isset($ag)) echo $ag['no_rekening'];?>">
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="">Nama Rekening</label>
                        <input type="text" class="form-control" name="nama_rekening" value="<?php if(isset($ag)) echo $ag['nama_rekening'];?>">
                    </div>
                    <div class="col-sm-3">
                        <label class="">Nominal Gaji</label>
                        <input type="text" class="form-control numberformat" name="gaji" value="<?php if(isset($ag)) echo number_format($ag['gaji'],0,'','.');?>">
                    </div>
                    <div class="col-sm-1">
                        <?php
                        if(isset($ag)){
                            echo '<label class="label label-info">'.jin_date_ina(date('Y-m-d',strtotime($ag['updated_at']))).'</label>';
                        }
                        ?>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-5">
                        <label class="">Password</label>
                        <input type="password" class="form-control" name="password" >
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-6">
                            <div class="col-sm-8">
                            <label class="">Photo</label>
                            <input type="file" class="form-control" name="userfile" id="get_photo ">
                        </div>
                        <div class="col-sm-2">
                            <?php
                            if(isset($ag)){
                                ?>
                                <input type="hidden" name="val_photo" value="<?php echo $ag['file_pic'];?>">
                                <?php
                                if(!empty($ag['file_pic'])){
                                    ?>
                                    <img src="<?php echo base_url().'uploads/anggota/'.$ag['file_pic'];?>" class="img-responsive">
                                    <?php
                                }else {
                                    ?>
                                    <img src="<?php echo base_url();?>assets/theme_admin/img/avatar5.png" class="img-responsive">
                                    <?php
                                }
                            }else {
                                ?>
                                <img src="<?php echo base_url();?>assets/theme_admin/img/avatar5.png" class="img-responsive">
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-4">
                        <label class="">Aktif Kengggotaan </label>
                        <select class="select2" name="aktif" style="width:100%;" placeholder="Pilih Aktif Kengggotaan" required="">
                            <option></option>
                            <?php
                            $arr_aktif = array('Y' => 'Aktif','N' => 'Non Aktif','P'=> 'Pasif');
                            foreach ($arr_aktif as $ak => $aktif) {

                                $ak_selected = false;
                                if(isset($ag)){
                                    if($ag['aktif'] == $ak){
                                        $ak_selected = 'selected';
                                    }
                                }

                                ?>
                                <option value="<?php echo $ak;?>" <?php echo $ak_selected;?>><?php echo $aktif;?></option>
                                <?php    
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <?php
                        if(isset($ag)){
                            if($ag['aktif'] == 'N'){
                                echo '<label class="label label-info">'.jin_date_ina(date('Y-m-d',strtotime($ag['updated_at']))).'</label>';
                            }
                        }
                        ?>
                    </div>
                    <div class="col-sm-4">
                        <label class="">Tipe Anggota </label>
                        <select class="select2" name="tipe_anggota" style="width:100%;" placeholder="Pilih Tipe Anggota" required="">
                            <option></option>
                            <option value="Penasehat" <?php if(isset($ag) && $ag['tipe_anggota'] == 'Penasehat') echo 'selected';?> >Penasehat</option>
                            <option value="Pengawas" <?php if(isset($ag) && $ag['tipe_anggota'] == 'Pengawas') echo 'selected';?>>Pengawas</option>
                            <option value="Pengurus" <?php if(isset($ag) && $ag['tipe_anggota'] == 'Pengurus') echo 'selected';?>>Pengurus</option>
                            <option value="Anggota" <?php if(isset($ag) && $ag['tipe_anggota'] == 'Anggota') echo 'selected';?>>Anggota</option>
                        </select>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-sm-12">
                        <button name="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button> 
                        <a href="<?php echo site_url('anggota');?>" class="btn btn-default"><i class="fa fa-sign-out"></i>  Kembali</a>
                    </div>
                </div>
            </li>
        </ul>
    </form>
</div>




<script type="text/javascript">
    $('.select2').select2();

    $("#tgl_daftar_txt").datetimepicker({
        language:  'id',
        weekStart: 1,
        autoclose: true,
        todayBtn: true,
        todayHighlight: true,
        pickerPosition: 'bottom-right',
        format: 'dd MM yyyy',
        linkField: "tgl_daftar",
        linkFormat: "yyyy-mm-dd",
        minView : 2,
    }); 
    $("#tgl_lahir_txt").datetimepicker({
        language:  'id',
        weekStart: 1,
        autoclose: true,
        todayBtn: true,
        todayHighlight: true,
        pickerPosition: 'bottom-right',
        format: 'dd MM yyyy',
        linkField: "tgl_lahir",
        linkFormat: "yyyy-mm-dd",
        minView : 2,
    }); 

    $('.numberformat').keyup(function(){
        var n = parseInt($(this).val().replace(/\D/g, ''), 10);
        $(this).val(number_format(n, 0, '', '.'));
    });

</script>