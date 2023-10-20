<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\POSForm;
use App\Models\APIModel;
use App\Models\DatabaseModel;
use App\Models\POSFilter;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Application\Attributes\Persistent;

use Nette\Utils\DateTime;

final class HomePresenter extends Presenter
{
    /** @var DatabaseModel @inject */
    public DatabaseModel $db;

    /** @var APIModel @inject */
    public APIModel $api;

    #[Persistent]
    public bool $open = false;

    #[Persistent]
    public string $date = "now";

    /**
     * output v HTML
     * 
     * obahuje formulář pro filtry
     */
    public function actionDefault()
    {
        $this->template->poss = $this->db->getPointsOfSale(
            new POSFilter($this->open, $this->date)
        );
    }

    /**
     * output v JSON formátu
     */
    public function actionGet()
    {
        $pos = $this->db->getPointsOfSale(
            new POSFilter($this->open, $this->date)
        );
        $this->sendJson($pos);
    }

    /**
     * handle pro smazání dat z DB
     */
    public function handleDeleteDataFromDB()
    {
        $this->db->deleteAll();
        $this->redirect("this");
    }

    /**
     * handle pro přidání dat do db
     */
    public function handleAddDataToDB()
    {
        $data = $this->api->getPointsOfSale();
        $this->db->setPointsOfSale($data);
        $this->redirect("this");
    }

    /**
     * vytvoření komponenty pro filtrování výsledků
     */
    public function createComponentFilterPOS(): POSForm
    {
        $form = new POSForm;
        $form->setDefaults([
            "open" => $this->open,
            "date" => $this->date,
            "when" => $this->date == "now" ? "now" : "date"
        ]);
        $form->onSuccess[] = function (Form $form, array $filter) {
            $this->redirect("this", $filter);
        };
        return $form;
    }
}
