<?php

namespace Acr\Destek\Controllers;

use Mail;
use Auth;
use View;
use Acr\Destek\Model\Destek_model;

class MailController
{
    function mailGonder($view = null, $mail, $isim = null, $subject = null, $ekMesaj = null)
    {
        $destek_model = new Destek_model();
        $ayar         = $destek_model->destek_ayar();
        $user         = array(
            'email'   => $mail,
            'isim'    => $isim,
            'subject' => $subject
        );
// the data that will be passed into the mail view blade template
        $data = array(
            'ek'   => $ekMesaj,
            'isim' => $user['isim'],
        );
        if (Auth::check()) {
            $user_name = empty(Auth::user()->name) ? Auth::user()->ad : Auth::user()->name;
            $from      = Auth::user()->id == 1 ? $ayar->destek_mail : Auth::user()->email;
            if (Auth::user()->id == 1) {
                $user_name = $ayar->destek_admin_isim;
            }
        } else {
            $user_name = '';
            $from      = $ayar->destek_mail;
        }
// use Mail::send function to send email passing the data and using the $user variable in the closure
        Mail::send('acr_destek::' . $view, $data, function ($message) use ($user, $user_name, $from) {
            $message->from($from, $user_name);
            $message->to($user['email'], $user['isim'])->subject($user['subject']);
        });
    }
}

?>