<?php

namespace App\Helpers;

use App\Models\RunOn\Metrika;

use App\Models\Bid;
use App\Models\User;
use App\Models\Option;
use App\Models\Region;
use Illuminate\Database\Eloquent\Collection;
// use Illuminate\Support\Collection;

class RunOn
{
    function __construct()
    {
    }
    public function claimsMatch(Collection $claims, $region)
    {
        $option = Option::getKeyValue();
        $userKeyed = $claims->mapWithKeys(function ($item) {
            return [$item['user_id']];
        });
        // $usersBalance = User::whereIn('id', $userKeyed)->sum('balance');

        $avg_r = $claims->avg('consumption');
        $max_r = $claims->max('consumption');
        $min_r = $claims->min('consumption');

        $claim = $claims->map(function ($item) use ($avg_r, $min_r, $max_r, $region, $option) {
            $isRegion = false;
            foreach ($item->regions as $regItem) {
                if ($regItem['id'] == $region->id) {
                    if (isset($regItem['rate'])) {
                        if (
                            $regItem['rate'] >= ceil($item->direction->cost_price +
                            ($item->direction->cost_price * ($item->direction->extra / 100)))
                        ) {
                            $insuranceAmount = $regItem['rate'] + ($regItem['rate'] * ($option['insurance_rate'] / 100));
                            if ($item->is_insurance && $item->user->balance >= $insuranceAmount) {
                                $item->consumption = ceil($insuranceAmount);
                            } else {
                                $item->consumption = ceil($regItem['rate']);
                            }
                        }
                    }
                    $isRegion = true;
                    break;
                }
            }
            $directionAmount = $item->direction->cost_price + ($item->direction->cost_price * ($item->direction->extra / 100));

            if (($isRegion || count($item->regions) == 0)
                && $item->user->balance >= $item->consumption
                && $item->consumption >= $directionAmount
            ) {
                $insuranceAmount = ceil($item->consumption + ($item->consumption * ($option['insurance_rate'] / 100)));
                if ($item->is_insurance && $item->user->balance >= $insuranceAmount) {
                    $item->consumption = $insuranceAmount;
                }
                $item->weight = mt_rand($min_r / 2, $item->consumption + $max_r)
                    / (($item->deals_today_count + 1) / 2) + ($isRegion ? $avg_r / ($min_r / $max_r) : 1);
                //$rndSqrt = sqrt(rand(1, $item->consumption));
                //$koef = (($item->consumption / $max_r)) * ($min_r / $max_r);
                //$koef / ($item->deals_today_count + $rndSqrt);
                // $item->weight = (($item->consumption / $max_r) * ($min_r / $max_r)) * ($item->deals_today_count + $sqrt);
                return $item;
            }
        })->sortByDesc('weight')->first();
        return $claim;
    }
}
