<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Wnc\Wishlist\SpotifyAuthenticator;

abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @var \Wnc\Wishlist\spotifyService @inject */
    public  $spotifyService;

    /** @var \Wnc\Wishlist\Model\EventModel @inject */
    public $eventModel;

    /** @var \Wnc\Wishlist\Model\ItemModel @inject */
    public $itemModel;

    /**
     * @return SpotifyAuthenticator
     */
    public function getUserAuthenticator() {
        return $this->user->authenticator;
    }
}
