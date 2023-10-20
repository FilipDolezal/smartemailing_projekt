<?php

declare(strict_types=1);

namespace App\Models;

use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\GroupedSelection;
use Nette\Database\Table\Selection;
use Nette\Utils\DateTime;

class DatabaseModel
{

    public Explorer $db;

    public function __construct(Explorer $db)
    {
        $this->db = $db;
    }

    public function getPointsOfSale(POSFilter $filter): array
    {
        // pokud nefiltrujeme můžeme vrátit všechny záznamy
        if (!$filter->open) {
            return array_map(
                fn (ActiveRow $row) => [
                    ...$row,
                    "opening_hours" => array_values($row
                        ->related("points_of_sale_hours")
                        ->fetchAssoc("id"))
                ],
                $this->db->table("points_of_sale")->fetchAll()
            );
        }

        // nejdříve vybereme všechny otevírací doby v daný den
        $selection = $this->db
            ->table("points_of_sale_hours")
            ->select("points_of_sale_hours.*")
            ->where(
                "? BETWEEN day_from AND day_to",
                (intval($filter->date->format("N")) - 1)
            );

        // vyfiltrujeme ty, které v daný čas nejsou otevřený
        $open = array_filter($selection->fetchAll(), function ($row) use ($filter) {
            $intervals = explode(",", $row->hours);
            foreach ($intervals as $interval) {
                $times  = preg_split('/(-|–)/', $interval);
                for ($i = 0; $i < sizeof($times); $i++) {
                    $p = explode(":", $times[$i]);
                    $times[$i] = clone $filter->date;
                    $times[$i]->setTime(intval($p[0]), intval($p[1]));
                }

                $bool = ($filter->date >= $times[0] && $filter->date <= $times[1]);
                if ($bool) return true;
            }
            return false;
        });

        // vrátíme vyfiltrované záznamy
        return array_map(
            function (ActiveRow $row) {
                $pos = $row->ref("points_of_sale", "pos_id");
                return [
                    ...$pos,
                    "opening_hours" => array_values($pos
                        ->related("points_of_sale_hours")
                        ->fetchAssoc("id"))
                ];
            },
            $open
        );
    }

    public function setPointsOfSale(array $raw)
    {
        $points_of_sale = array_map(fn ($v) => [
            "id" => $v->id,
            "type" => $v->type,
            "name" => $v->name,
            "address" => $v->address ?? null,
            "lat" => $v->lat,
            "lon" => $v->lon,
            "services" => $v->services,
            "pay_methods" => $v->payMethods,
            "link" => $v->link ?? null,
        ], $raw);

        // insert points of sale records
        $this->db
            ->table("points_of_sale")
            ->insert($points_of_sale);

        $openingHours = array_column($raw, "openingHours", "id");
        $points_of_sale_hours = array();

        foreach ($openingHours as $pos_id => $raw_hours) {
            $hours = array_map(fn ($v) => [
                "pos_id" => $pos_id,
                "day_from" => $v->from,
                "day_to" => $v->to,
                "hours" => $v->hours
            ], $raw_hours);

            $points_of_sale_hours =  [...$points_of_sale_hours, ...$hours];
        }

        // insert points of sale hours records
        $this->db
            ->table("points_of_sale_hours")
            ->insert($points_of_sale_hours);
    }
}

/**
 * Pomocná třída pro filtraci výpisu
 */
final class POSFilter
{
    public DateTime $date;
    public bool $open;

    public function __construct(
        bool $open = false,
        string $date = "now",
    ) {
        $this->open = $open;
        $this->date = new DateTime($date);
    }
}
