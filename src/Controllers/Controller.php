<?php

namespace Acr\Destek\Controllers;

use Acr\Destek\Model\Destek_model;
use Illuminate\Routing\Controller as BaseController;
use Auth;

class Controller extends BaseController
{
    public $uye_id;
    public $kurum_id;

    function uye_id()
    {
        $destek_model = new Destek_model();
        return $this->uye_id = $destek_model->uye_id();
    }

    function kurum_id()
    {
        $destek_model = new Destek_model();
        return $this->kurum_id = $destek_model->kurum_id();
    }
}
