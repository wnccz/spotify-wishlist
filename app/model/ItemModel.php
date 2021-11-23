<?php
namespace Wnc\Wishlist\Model;

use Nette;
use Nette\Database\Explorer;
use Nette\Security\IIdentity;
use Nette\Security\SimpleIdentity;
use Nette\Utils\Random;
use Wnc\Wishlist\spotifyService;

class ItemModel 
{
	/** @var Nette\Database\Explorer */
    public $database;

    /** @var Wnc\Wishlist\spotifyService */
    public $spotify;

    public function __construct(Explorer $database, spotifyService $spotify)
    {
        $this->database = $database;
        $this->spotify  = $spotify;
    }

    public function addToQueue($eventId, $trackId) {
        $track = $this->spotify->getTrackById($trackId);
        $data = [
            "event_id" => $eventId,
            "track_id" => $trackId,
            "name" => $track->name,
            "artist_name" => $track->artists[0]->name,
            "album_name" => $track->album->name,
            "album_image_url" => $track->album->images[1]->url
        ];
        $this->database->table("item")->insert($data);
    }

    public function getItemsFromEvent($eventId) {
        return $this->database->table("item")->where("event_id", $eventId)->order("id");
    }

    public function getItemById($itemId) {
        return $this->database->table("item")->wherePrimary($itemId);
    }

    public function removeItem($itemId) {
        $this->database->table("item")->wherePrimary($itemId)->delete();
    }

}