<section class="content">
    <?php echo empty($msg) ? '' : $msg; ?>
    <div class="row">
    <?php echo $destek->menu($tab);
    $ayar = $data->destek_ayar();
    ?>

    <!-- /.col -->
        <form action="/destek_ayar_kaydet" method="post" enctype="multipart/form-data">
            <?php echo csrf_field() ?>
            <div class="col-md-9">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Destek Ayarları Yapılandırma - Admin</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <input name="destek_mail" id="destek_mail" class="form-control" placeholder="Kullanıcıların cevap vereceği Mail adresi " value="<?php echo $ayar->destek_mail ?>">
                        </div>
                        <div class="form-group">
                            <input name="sms_user" id="sms_user" class="form-control" placeholder="Sms Kullanıcı" value="<?php echo $ayar->sms_user ?>">
                        </div>
                        <div class="form-group">
                            <input type="password" name="sms_sifre" id="sms_sifre" class="form-control" placeholder="Sms Şifre" value="<?php echo $ayar->sms_sifre ?>">
                        </div>
                        <div class="form-group">
                            <input name="sms_baslik" id="sms_baslik" class="form-control" placeholder="SMS BAŞLIK" value="<?php echo $ayar->sms_baslik ?>">
                        </div>
                        <div class="form-group">
                            <input name="destek_admin_isim" id="destek_admin_isim" class="form-control" placeholder="Gönderen" value="<?php echo $ayar->destek_admin_isim ?>">
                        </div>
                        <div class="form-group">
                            <input <?php echo $ayar->sms_aktiflik == 1 ? 'checked="checked"' : '';?> name="sms_aktiflik" id="sms_aktiflik" type="checkbox" value="1"/> <label for="sms_aktiflik">SMS Gönderimi Aktif</label>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> Gönder</button>
                        </div>
                        <a href="/destek" type="reset" class="btn btn-default"><i class="fa fa-times"></i> Vazgeç</a>
                    </div>
                    <!-- /.box-footer -->
                </div>
                <!-- /. box -->
            </div>
        </form>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
