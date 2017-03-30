<?php

namespace Acr\Destek;


use Acr\Destek\Model\Destek_dosya_model;
use Symfony\Component\HttpFoundation\Request;
use Acr\Destek\Controllers\Controller;
use Acr\Destek\Model\Destek_model;
use Acr\Destek\Controllers\MailController;


class Destek extends Controller
{
    protected $basarili           = '<div class="alert alert-success">Başarıyla Eklendi</div>';
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
        $destek_model    = new Destek_model();
        $data            = $destek_model->tab_menu();
        $gelen_okunmayan = $destek_model->gelen_okunmayan_sayi();
        $gelen_div       = $tab == 'inbox' ? '<span class="label label-primary pull-right">' . $gelen_okunmayan . '</span>' : '';
        $link            = '';
        foreach ($data as $datum => $datas) {
            $active = $datum == $tab ? 'class="active"' : '';
            $link   .= '<li ' . $active . ' ><a href="/destek?tab=' . $datum . '"><i class="fa fa-' . $datas[1] . '"></i> ' . $datas[0] . ' ' . $gelen_div . ' </a></li>';
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
                    ' . $link . '
                    </ul>
                </div>
                <!-- /.box-body -->
            </div>
        </div>';
    }

    function destek_satir($item, $tab)
    {
        $item->name = empty($item->name) ? $item->ad : $item->name;
        $veri       =
            '<tr id="destek_satir_' . $item->destek_users_id . '">
                   <td><input id="destek_id[]" name="destek_id[]" value="' . $item->destek_users_id . '"  type="checkbox"></td>
                   <td class="mailbox-name"><a href="/destek/mesaj_oku?mesaj_id=' . $item->destek_users_id . '&tab=' . $tab . '">' . $item->name . '</a></td>
                   <td class="mailbox-subject"><b>' . $item->konu . '</b></td>
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
        $mesaj_id     = $destek_model->destek_mesaj_kaydet($konu, $mesaj, $uye_id, $gon_id);
        if (!empty($dosya)) {
            $isim       = $dosya->getClientOriginalName();
            $size       = round($dosya->getClientSize() / 1000000, 2);
            $type       = strtolower($dosya->getClientOriginalExtension());
            $dosya_isim = self::ingilizceYap($isim);
            $dosya->move(public_path('/uploads'), $dosya_isim);
            $destek_model->destek_dosya_kaydet($mesaj_id, $dosya_isim, $uye_id, $gon_id, $size, $type, $isim);
        }
        $mail->mailGonder('mail.destek', 'acarbey15@gmail.com', 'Görülen iSim', 'konu', 'Bu bir destek MEsajı');
        return redirect()->to('destek/yeni_mesaj?msg=' . $this->gonderildi);
    }

    function destek_dosya_indir($destek_dosya_id)
    {
        $destek_dosya_model = new Destek_dosya_model();
        $dosyaSorgu         = $destek_dosya_model->where('id', $destek_dosya_id);
        $dosya_sayi         = $dosyaSorgu->count();
        $dosya              = $dosyaSorgu->first();

        if ($dosya_sayi > 0) {
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
}