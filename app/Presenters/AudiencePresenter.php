<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Application\BadRequestException;
use Tracy\Debugger;

final class AudiencePresenter extends BasePresenter
{
    
    public function actionDefault($id) {
        $event = $this->template->event = $this->eventModel->getEvent($id);
        if (!$event) {
            throw new BadRequestException('Událost neexistuje', 404);
        }
    }
    public function renderDefault($id) {
        $this->template->eventId = $id;
    }

    public function handleAddToList($eventId, $trackId) {
        $this->itemModel->addToQueue($eventId, $trackId);
        $this->flashMessage("Skladba byla přidána do seznamu přání.", "success");
	$this->redrawControl("flashes");
	$this->payload->clearForm = true;
    }

    public function handleGetSongs($q) {
        $data = [];
        foreach ($this->spotifyService->searchSongs($q)->tracks->items as $item) {
            $data[] = [
                "id" => $item->id,
                "name" => $item->artists[0]->name . " - ". $item->name,
                "image" => $item->album->images[1]->url,
                "song_name" => $item->name,
                "artist_name" => $item->artists[0]->name,
                "album_name" => $item->album->name
            ];
        }
        $this->sendJson($data);
    }
}
