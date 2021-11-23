<?php

declare(strict_types=1);

namespace App\Presenters;

use DateInterval;
use DateTime;
use Nette;
use Nette\Http\Url;
use Nette\Utils\DateTime as UtilsDateTime;
use Tracy\Debugger;

final class SignPresenter extends BasePresenter
{
  
    public function actionSpotifyRedirect() {
        $link = $this->link("//Sign:callback"); 
        $this->redirectUrl($this->spotifyService->getAuthUrl($link));
    }

    public function actionCallback($code, $state, $error = null) {
        $link = $this->link("//Sign:callback"); 
        $data = $this->spotifyService->userAuth($code, $link);
        $this->user->login($data->access_token);
        $this->user->identity->refresh_token = $data->refresh_token;
        $date = new UtilsDateTime();
        $date->add(new DateInterval("PT".$data->expires_in."S"));
        $this->user->identity->token_validity = $date;
        $this->redirect("Homepage:default");
        $this->terminate();
    }

    public function actionOut() {
        $this->user->logout();
        $this->redirect("Sign:in");
        $this->terminate();
    }

    
}
