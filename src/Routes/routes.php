<?php
use Illuminate\Http\Request;

Route::group(['middleware' => ['auth']], function () {
    /* Route::get('/destek/{sayfa}/{yer}', function (Request $request, $sayfa, $yer) {
         $tab      = $request->input('tab');
         $sayfa    = empty($sayfa) ? 'anasayfa' : $sayfa;
         $yer      = empty($yer) ? '' : $yer . '.';
         $mesaj_id = $request->input('mesaj_id');
         $msg      = $request->input('msg');
         return Destek::index($sayfa, $yer, $tab, $mesaj_id, $msg);
     });
     Route::get('/destek/{sayfa}/', function (Request $request, $sayfa) {
         $tab      = $request->input('tab');
         $mesaj_id = $request->input('mesaj_id');
         $sayfa    = empty($sayfa) ? 'anasayfa' : $sayfa;
         $msg      = $request->input('msg');

         return Destek::index($sayfa, null, $tab, $mesaj_id, $msg);
     });
     Route::get('/destek', function (Request $request) {
         $tab      = $request->input('tab');
         $sayfa    = 'anasayfa';
         $tab      = empty($tab) ? 'destek_gelen' : $tab;
         $mesaj_id = $request->input('mesaj_id');
         $msg      = $request->input('msg');
         return Destek::index($sayfa, null, $tab, $mesaj_id, $msg);

     });*/
    Route::post('/destek_sec_sil', function (Request $request) {
        $destek_id = $request->input('destek_id');
        $tab       = $request->input('tab');
        return Destek::sil($destek_id, $tab);
    });
    Route::get('/destek_sil', function (Request $request) {
        $destek_id = $request->input('destek_id');
        $tab       = $request->input('tab');
        Destek::tek_sil($destek_id, $tab);
        return redirect()->to('destek?tab=' . $tab);
    });
    Route::post('/destek_mesaj_kaydet', function (Request $request) {
        $mesaj  = $request->input('mesaj');
        $konu   = $request->input('konu');
        $dosya  = $request->file('attachment');
        $uye_id = $request->input('uye_id');
        return Destek::destek_mesaj_kaydet($konu, $mesaj, $dosya, $uye_id);
    });
    Route::get('/destek_dosya_indir', function (Request $request) {
        $destek_dosya_id = $request->input('dosya_id');
        return Destek::destek_dosya_indir($destek_dosya_id);
    });
    Route::post('/destek_ayar_kaydet', function (Request $request) {
        $data = $request->all();
        return Destek::destek_ayar_kaydet($data);
    });
});
