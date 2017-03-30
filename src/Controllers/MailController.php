<?php

namespace Acr\Destek\Controllers;

use Mail;
use Auth;
use View;

class MailController
{
    function mailGonder($view = null, $mail, $isim = null, $subject = null, $ekMesaj = null)
    {
        $user = array(
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
        } else {
            $user_name = '';
        }
// use Mail::send function to send email passing the data and using the $user variable in the closure
        Mail::send('acr_destek::' . $view, $data, function ($message) use ($user, $user_name) {
            $message->from('info@konaksar.com', $user_name);
            $message->to($user['email'], $user['isim'])->subject($user['subject']);
        });
    }
}

?>