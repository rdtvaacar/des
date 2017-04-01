<?php

namespace Acr\Destek;

use Acr\Destek\Model\Destek_dosya_model;
use Acr\Destek\Controllers\Controller;
use Acr\Destek\Model\Destek_model;
use Acr\Destek\Controllers\MailController;


class Destek extends Controller
{
    protected $basarili           = '<div class="alert alert-success">Başarıyla Eklendi</div>';
    protected $dosyaBuyuk         = '<div class="alert alert-danger">Yüklemeye çalıştığınız dosyanın boyutu 20 MB\'den büyük</div>';
    protected $gonderildi         = '<div class="alert alert-success">Mesajınız başarıyla gönderildi, en kısa zamanda size yanıt vermeye çalışacağız, teşekkür ederiz.</div>';
    protected $basariliGuncelleme = '<div class="alert alert-success">Başarıyla Güncellendi</div>';

    function index($sayfa, $yer, $tab, $mesaj_id, $msg)
    {
        $destek = new Destek();
        return view('acr_destek.index', compact('destek', 'sayfa', 'yer', 'tab', 'mesaj_id', 'msg'));
    }

    function anasayfa($sayfa, $yer, $tab, $mesaj_id, $msg)
    {
        $destek = new Destek();
        $data   = new Destek_model();
        return view('acr_destek::' . $yer . $sayfa, compact('destek', 'tab', 'data', 'mesaj_id', 'msg'));
    }

    function sil($destek_id, $tab)
    {
        $data = new Destek_model();
        if ($tab == 'destek_cop') {
            $data->sil($destek_id);
        } else {
            $data->cope_tasi($destek_id);
        }
        return $destek_id;
    }

    function tek_sil($destek_id, $tab)
    {
        $data = new Destek_model();
        if ($tab == 'destek_cop') {
            $data->tek_sil($destek_id);
        } else {
            $data->tek_cope_tasi($destek_id);
        }
        return $destek_id;
    }

    function menu($tab)
    {
        $destek_model = new Destek_model();
        $data         = $destek_model->tab_menu();
        $link         = '';
        foreach ($data as $datum => $datas) {
            $okunmayan = $destek_model->gelen_okunmayan_sayi($datas[2]) == 0 ? '' : '<span style="color: red;">' . $destek_model->gelen_okunmayan_sayi($datas[2]) . '</span>';
            $active    = $datum == $tab ? 'class="active"' : '';
            $link      .= '<li ' . $active . ' ><a href="/destek?tab=' . $datum . '"><i class="fa fa-' . $datas[1] . '"></i> ' . $datas[0] . ' ' . $okunmayan . ' </a></li>';
        }
        if ($tab == 'destek_ayar') {
            $activeAyar = 'class="active"';
        } else {
            $activeAyar = '';
        }
        if ($this->uye_id() == 1) {
            $admin_ayar = '<li ' . $activeAyar . '><a href="/destek/destek_ayar?tab=destek_ayar"><i class="fa  fa-gears"></i>  Admin Ayarlar</a></li>';
        } else {
            $admin_ayar = '';
        }
        return '<div class="col-md-3">
            <a href="/destek/yeni_mesaj" class="btn btn-primary btn-block margin-bottom">Yeni Mesaj Gönder</a>
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">DESTEK</h3>
                    <div class="box-tools">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body no-padding">
                    <ul class="nav nav-pills nav-stacked">
                    ' . $link . $admin_ayar . '
                    </ul>
                </div>
                <!-- /.box-body -->
            </div>
        </div>';
    }

    function destek_satir($item, $tab)
    {
        $okunduStyle = $item->okundu == 1 ? 'style="color:#B0C4DE"' : '';
        $konu        = $item->okundu == 1 ? $item->konu : '<b>' . $item->konu . '</b>';
        $item->name  = empty($item->name) ? $item->ad : $item->name;
        $veri        =
            '<tr id="destek_satir_' . $item->destek_users_id . '">
                   <td><input id="destek_id[]" name="destek_id[]" value="' . $item->destek_users_id . '"  type="checkbox"></td>
                   <td class="mailbox-name"><a ' . $okunduStyle . ' href="/destek/mesaj_oku?mesaj_id=' . $item->destek_users_id . '&tab=' . $tab . '">' . $item->name . '</a></td>
                   <td class="mailbox-subject">' . $konu . '</td>
                   <td class="mailbox-attachment"></td>
                   <td align="right" class="mailbox-date">' . $item->d_cd . '</td>
             </tr>';
        return $veri;
    }

    function mesajlar($tab, $sil)
    {
        $destek_model = new Destek_model();
        $data         = $destek_model->tab_menu();
        $tur          = $data[$tab][2];
        $mesajlar     = $destek_model->mesajlar($tur, $sil);
        return $mesajlar;
    }

    function ingilizceYap($metin)
    {
        $search  = array(' ', 'Ç', 'ç', 'Ğ', 'ğ', 'ı', 'İ', 'Ö', 'ö', 'Ş', 'ş', 'Ü', 'ü', '&Ccedil;', '&#286;', '&#304;', '&Ouml;', '&#350;', '&Uuml;', '&ccedil;', '&#287;', '&#305;', '&ouml;', '&#351;', '&uuml;');
        $replace = array('-', 'C', 'c', 'G', 'g', 'i', 'I', 'O', 'o', 'S', 's', 'U', 'u', 'C', 'G', 'I', 'O', 'S', 'U', 'c', 'g', 'i', 'o', 's', 'u');
        $metin   = str_replace($search, $replace, $metin);
        return $metin;
    }

    function destek_mesaj_kaydet($konu, $mesaj, $dosya, $uye_id)
    {
        $mail         = new MailController();
        $destek_model = new Destek_model();
        $gon_id       = $this->uye_id();
        $ayar         = $destek_model->destek_ayar();
        $mesaj_id     = $destek_model->destek_mesaj_kaydet($konu, $mesaj, $uye_id, $gon_id);
        $alan         = $destek_model->alan($uye_id);
        $alan_isim    = empty($alan->name) ? $alan->ad : $alan->name;
        if (!empty($dosya)) {
            $size       = round($dosya->getClientSize() / 1000000, 2);
            $type       = strtolower($dosya->getClientOriginalExtension());
            $isim       = str_replace('.' . $type, '', $dosya->getClientOriginalName());
            $dosya_isim = self::ingilizceYap($isim);
            $dosya->move(public_path('/uploads'), $dosya_isim);
            if ($size < 21 && $size > 0) {
                $destek_model->destek_dosya_kaydet($mesaj_id, $dosya_isim, $uye_id, $gon_id, $size, $type, $isim);
            } else {
                return redirect()->to('destek/yeni_mesaj?msg=' . $this->dosyaBuyuk);;
            }
        }
        if (!empty($alan->tel) && $ayar->sms_aktiflik == 1) {
            $tel[] = $alan->tel;
            self::smsGonder($_SERVER['SERVER_NAME'] . ' size mesaj gönderdi, sisteme giriş yaparak inceleyebilirsiniz.', $tel, $ayar->sms_user, $ayar->sms_sifre, $ayar->sms_baslik);
        }
        $mail->mailGonder('mail.destek', $alan->email, $alan_isim, 'konu', $konu . '<br>' . $mesaj);

        return redirect()->to('destek/yeni_mesaj?msg=' . $this->gonderildi);
    }

    function destek_dosya_indir($destek_dosya_id)
    {
        $destek_dosya_model = new Destek_dosya_model();
        $dosyaSorgu         = $destek_dosya_model->where('id', $destek_dosya_id);
        $dosya_sayi         = $dosyaSorgu->count();
        if ($dosya_sayi > 0) {
            $dosya   = $dosyaSorgu->first();
            $izinler = [
                $dosya->uye_id, $dosya->gon_id
            ];
            if (in_array($this->uye_id(), $izinler)) {
                return response()->download(public_path('/uploads/' . $dosya->dosya_isim), $dosya->dosya_org_isim . '.' . $dosya->type);
            } else {
                return 'Dosya erişiminize izniniz bulunmuyor.';
            }

        } else {
            return 'Dosya mevcut değil.';
        }
    }

    function destek_ayar_kaydet($data)
    {
        $destek_model            = new Destek_model();
        $data                    = (object)$data;
        $data->destek_mail       = empty($data->destek_mail) ? '' : $data->destek_mail;
        $data->sms_user          = empty($data->sms_user) ? '' : $data->sms_user;
        $data->sms_sifre         = empty($data->sms_sifre) ? '' : $data->sms_sifre;
        $data->destek_admin_isim = empty($data->destek_admin_isim) ? 'Admin' : $data->destek_admin_isim;
        $data->sms_aktiflik      = empty($data->sms_aktiflik) ? 0 : $data->sms_aktiflik;
        $data->sms_baslik        = empty($data->sms_baslik) ? 0 : $data->sms_baslik;
        $veri                    = [
            'destek_mail'       => $data->destek_mail,
            'sms_user'          => $data->sms_user,
            'sms_sifre'         => $data->sms_sifre,
            'destek_admin_isim' => $data->destek_admin_isim,
            'sms_aktiflik'      => $data->sms_aktiflik,
            'sms_baslik'        => $data->sms_baslik,
        ];
        $destek_model->destek_ayar_kaydet($veri);
        return redirect()->to('destek/destek_ayar?msg=' . $this->basariliGuncelleme);
    }

    function smsGonder($mesaj, $tel, $user, $password, $baslik)
    {

        if (empty($mesaj)) {

            $mesaj = self::ingilizceYap(strip_tags(trim(Input::get('mesaj'))));
        } else {
            $mesaj = $mesaj;
        }
        $telDizi = $tel;
        array_unique($telDizi);
        $mesajData['user']      = array(
            'name' => $user,
            'pass' => $password
        );
        $mesajData['msgBaslik'] = $baslik;
        $mesajData['msgData'][] = array(
            'tel' => $telDizi,
            'msg' => $mesaj,
        );
        self::MesajPaneliGonder($mesajData);
    }

    function MesajPaneliGonder($request)
    {
        $request = "data=" . base64_encode(json_encode($request));
        $ch      = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://api.mesajpaneli.com/json_api/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode(base64_decode($result), TRUE);
    }

}