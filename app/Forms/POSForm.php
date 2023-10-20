<?php

declare(strict_types=1);

namespace App\Forms;

use Nette\Application\UI\Form;
use Nette\Utils\DateTime;

/**
 * Formulář pro volbu filtrůs
 */
class POSForm extends Form
{

    public function __construct()
    {
        $open = $this->addCheckbox("open", "Zobrazit pouze otevřené prodejní místa");

        $when =  $this->addRadioList("when", "Otevřeno", [
            "now" => "Nyní",
            "date" => "Zvolit datum"
        ]);

        $date = $this->addText("date", "Datum a čas");

        $open
            ->addCondition(Form::EQUAL, true)
            ->toggle("#open-when-toggle");

        $when
            ->setDefaultValue("now")
            ->addConditionOn($open, Form::EQUAL, true)
            ->addRule(Form::REQUIRED, "Zvolte kdy chcete prodejní místo navštívit.")
            ->endCondition()
            ->addCondition(Form::EQUAL, "date")
            ->toggle("#open-date-toggle");


        $date
            ->setHtmlType("datetime-local")
            ->setDefaultValue((new DateTime("now"))->format("Y-m-d\TH:i:s"))
            ->addConditionOn($when, Form::EQUAL, "date")
            ->addRule(Form::REQUIRED, "Vyplňte datum návštěvy prodejního místa.");

        $this->addSubmit("filter", "Zobrazit");
    }

    public function getValues($returnType = null, ?array $controls = null)
    {
        // vyhodnoť jaké filtry jsou zapnuté a v jaké kombinaci
        $v = parent::getValues($returnType, $controls);
        return [
            "open" => $v["open"],
            "date" => $v["open"] ?
                (($v["when"] == "now") ? "now" : $v["date"])
                : "now"
        ];
    }
}
