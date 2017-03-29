<?php

namespace Acr\Destek\Model;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Auth;

class Destek_model extends Model

{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'destek';
    public    $uye_id;
    public    $kurum_id;

    function uye_id()
    {
        if (Auth::check()) {
            return $this->uye_id = Auth::user()->id;

        } else {
            return $this->uye_id = 0;
        }

    }

    function tab_menu()
    {
        $data = [
            'destek_gelen' => ['Gelen Kutusu', 'inbox', 0],
            'destek_giden' => ['Gönderilenler', 'envelope-o', 1],
            'destek_cop'   => ['Çöp Kutusu', 'trash-o', 2]
        ];
        return $data;
    }

    function kurum_id()
    {
        if (Auth::check()) {
            return $this->kurum_id = Auth::user()->kurum_id;
        } else {
            return $this->kurum_id = 0;
        }
    }

    function gelen_okunmayan_sayi()
    {
        return 0;
    }

    function mesajlar($tab, $sil)
    {
        $data = self::tab_menu();
        $tur  = $data[$tab][2];
        return $sorgu = Destek_users_model::leftJoin('destek', 'destek_users.mesaj_id', '=', 'destek.id')
            ->leftJoin('users', 'users.id', '=', 'destek_users.gon_id')
            ->where('destek_users.uye_id', $this->uye_id())
            ->where('destek_users.tur', $tur)
            ->where('destek_users.sil', $sil)
            ->orderBy('destek.id', 'desc')
            ->select('destek.*', 'users.*', 'destek_users.*', 'users.id as uye_id', 'destek.id as destek_id', 'destek_users.id as destek_users_id', 'users.created_at as users_cd', 'destek.created_at as d_cd', 'destek_users.created_at as du_cd')
            ->paginate(50);
    }

    function mesaj_oku($mesaj_id)
    {
        return Destek_users_model::leftJoin('destek', 'destek_users.mesaj_id', '=', 'destek.id')
            ->leftJoin('users', 'users.id', '=', 'destek_users.gon_id')
            ->leftJoin('destek_dosya', 'destek_dosya.mesaj_id', '=', 'destek_users.mesaj_id')
            ->where('destek_users.uye_id', $this->uye_id())
            ->where('destek_users.id', $mesaj_id)
            ->select('destek_dosya.*', 'destek.*', 'users.*', 'destek_users.*', 'users.id as uye_id', 'destek_dosya.id as destek_dosya_id', 'destek.id as destek_id', 'destek_users.id as destek_users_id', 'users.created_at as users_cd', 'destek.created_at as d_cd', 'destek_users.created_at as du_cd')
            ->first();
    }

    function sil($destek_id)
    {
        Destek_users_model::where('uye_id', $this->uye_id())->whereIn('id', $destek_id)->update(['tur' => 2, 'sil' => 1]);
    }

    function cope_tasi($destek_id)
    {
        Destek_users_model::where('uye_id', $this->uye_id())->whereIn('id', $destek_id)->update(['tur' => 2]);
    }

    function tek_sil($destek_id)
    {
        Destek_users_model::where('uye_id', $this->uye_id())->where('id', $destek_id)->update(['tur' => 2, 'sil' => 1]);
    }

    function tek_cope_tasi($destek_id)
    {
        Destek_users_model::where('uye_id', $this->uye_id())->where('id', $destek_id)->update(['tur' => 2]);
    }

    function gonderen($gon_id)
    {
        $user     = new User();
        $gonderen = $user->where('id', $gon_id)->first()->name;
        return $gonderen;
    }

    function destek_mesaj_kaydet($konu, $mesaj, $uye_id, $gon_id)
    {
        $data = [
            'konu'  => $konu,
            'mesaj' => $mesaj
        ];

        $mesaj_id = Destek_model::insertGetId($data);
        $data2    = [
            'uye_id'   => $uye_id,
            'mesaj_id' => $mesaj_id,
            'gon_id'   => $gon_id,
            'tur'      => 0
        ];
        Destek_users_model::insert($data2);
        return $mesaj_id;
    }

    function destek_dosya_kaydet($mesaj_id, $dosya_isim, $uye_id, $gon_id, $size, $type, $isim)
    {
        $data = [
            'dosya_org_isim' => $isim,
            'dosya_isim'     => $dosya_isim,
            'mesaj_id'       => $mesaj_id,
            'uye_id'         => $uye_id,
            'gon_id'         => $gon_id,
            'size'           => $size,
            'type'           => $type
        ];
        Destek_dosya_model::insert($data);
    }
}