<?php

declare(strict_types=1);

namespace App\Presenters;

use Exception;
use Contributte\FormsBootstrap\BootstrapForm;
use Contributte\FormsBootstrap\Enums\RenderMode;
use Nette\Application\BadRequestException;

final class HomepagePresenter extends BasePresenter
{
    public function actionDefault() {
        if (!$this->user->isLoggedIn()) {
            $this->redirect("Sign:in");
            $this->terminate();
        }
    }

    
    public function actionQueue($id) {
        if (!$this->user->isLoggedIn()) {
            $this->redirect("Sign:in");
            $this->terminate();
        }
        $event = $this->eventModel->getEvent($id);
        if (!$event) {
            throw new BadRequestException('Událost neexistuje', 404);
        }
        if ($event->user_id != $this->user->identity->id) {
            throw new BadRequestException('Nedostatečná oprávnění', 403);
        }
    }

    public function renderDefault() {
        $this->template->upcomingEvents = $this->eventModel->getEvents($this->user->id)->where("date > DATE_SUB(NOW(), INTERVAL 1 DAY)");
        $this->template->pastEvents = $this->eventModel->getEvents($this->user->id)->where("date <= DATE_SUB(NOW(), INTERVAL 1 DAY)");
    }

    public function renderQueue($id) {
        $this->template->eventId = $id;
        $this->template->items = $this->itemModel->getItemsFromEvent($id);
    }
    
    public function handleAddToQueue($itemId) {
        $track = $this->itemModel->getItemById($itemId)->fetch();
        $token = $this->spotifyService->getAccessToken($this->user->identity);
        
        try {
            $this->spotifyService->addToQueue($track->track_id, $token);
            $this->itemModel->removeItem($itemId);
            $this->flashMessage("Položka byla úspěšně vložená do fronty spotify.", "success");
	} catch (\Wnc\NoActiveDeviceException $e) {
	    $this->flashMessage("Nebyl nalezen žádný aktivní přehrávač spotify. Zapněte aplikaci spotify a spusťte přehrávání.", "danger");
	} catch (Exception $e) {
            $this->flashMessage("Nepodařilo se přidat položku do playlistu: ". $e->getMessage(), "danger");
        }
        
        $this->redrawControl("queue");
        $this->redrawControl("flashes");
    }

    public function handleRefresh() {
        $this->redrawControl("queue");
    }

    public function handleDeleteItem($itemId) {
        $this->itemModel->removeItem($itemId);
        $this->flashMessage("Položka byla úspěšně smazaná ze seznamu přání.", "success");
        $this->redrawControl("queue");
        $this->redrawControl("flashes");
    }

    public function createComponentAddEventForm() {
        $form = new BootstrapForm();
        $form->renderMode = RenderMode::VERTICAL_MODE;
        $form->ajax = true;
        $form->onSuccess[] = [$this, "addEventFormSuccess"];
        $form->addText("name", "Název akce: ");
        $form->addText("date", "Datum: ")->setHtmlType("date");
        $form->addText("location", "Místo konání: ");
        $form->addSubmit("add", "Přidat");
        return $form;
    }

    public function addEventFormSuccess($from, $values) {
        unset($values["add"]);
        $this->eventModel->addEvent($values, $this->user->id);
        $this->flashMessage("Událost byla úspěšně přidána", "success");
        $this->redrawControl("form");
        $this->redrawControl("list");
        $this->redrawControl("flashes");
    } 
}
