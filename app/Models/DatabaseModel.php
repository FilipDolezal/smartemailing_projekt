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

    /**
     * @return  PointOfSale[]
     */
    public function getPointsOfSale(POSFilter $filter): array
    {
        $selection = $this->db
            ->table("points_of_sale")
            ->where($filter->toArray())
            ->group(":points_of_sale_hours.pos_id");

        return array_map(
            function (ActiveRow $info) {
                $hours = $info->related("points_of_sale_hours", "pos_id");
                $data = array_merge(
                    $info->toArray(),
                    ["opening_hours" => $hours->fetchAssoc("id")]
                );
                return $data;
            },
            $selection->fetchAll()
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

class POSFilter
{
    private DateTime $date;
    private bool $open;

    public function __construct(
        bool $open = false,
        string $date = "now",
    ) {
        $this->open = $open;
        $this->date = new DateTime($date);
    }

    public function toArray(): array
    {
        if (!$this->open) return [];

        $day = $this->date->format("N");
        $hours = $this->date->format("H:i");

        return [
            "? BETWEEN 
                SUBSTRING_INDEX(:points_of_sale_hours.hours, '-', 1) 
            AND 
                SUBSTRING_INDEX(:points_of_sale_hours.hours, '-', -1)"
            => $hours,

            "? BETWEEN 
                :points_of_sale_hours.day_from 
            AND 
                :points_of_sale_hours.day_to"
            => $day
        ];
    }

    public function __invoke(): array
    {
        return $this->toArray();
    }
}
