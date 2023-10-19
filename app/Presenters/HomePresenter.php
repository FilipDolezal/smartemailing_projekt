<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Forms\POSForm;
use App\Models\APIModel;
use App\Models\DatabaseModel;
use App\Models\POSFilter;
use Exception;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

use Nette\Utils\DateTime;

final class HomePresenter extends Presenter
{
    /** @var DatabaseModel @inject */
    public DatabaseModel $db;

    /** @var APIModel @inject */
    public APIModel $api;

    public function actionAddToDatabase()
    {
        $data = $this->api->getPointsOfSale();
        $this->db->setPointsOfSale($data);
        $this->sendPayload("success");
    }

    public function actionGet(bool $open = false, string $date = "now")
    {
        $pos = $this->db->getPointsOfSale(new POSFilter($open, $date));
        $this->sendJson($pos);
    }

    public function createComponentFilterPOS(): POSForm
    {
        $form = new POSForm;
        $form->onSuccess[] = function (Form $form, array $filter) {
            $this->redirect("get", $filter);
        };
        return $form;
    }
}
