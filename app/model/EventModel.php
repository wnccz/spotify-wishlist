<?php
namespace Wnc\Wishlist\Model;

use Nette;
use Nette\Database\Explorer;
use Nette\Security\IIdentity;
use Nette\Security\SimpleIdentity;
use Nette\Utils\Random;

class EventModel 
{
	/** @var Nette\Database\Explorer */
    public $database;

    public function __construct(Explorer $database)
    {
        $this->database = $database;
    }

    public function getEvents($userId) {
        return $this->database->table("event")->where("user_id", $userId);
    }

    public function addEvent($data, $userId) {
        $data["id"] = Random::generate(12);
        $data["user_id"] = $userId;
        $this->database->table("event")->insert($data);
    }

    public function getEvent($id) {
        return $this->database->table("event")->wherePrimary($id)->fetch();
    }
}