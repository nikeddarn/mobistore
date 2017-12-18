<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 18.12.17
 * Time: 19:23
 */

namespace App\Http\Controllers\Admin\Support\Badges;


use App\Contracts\Shop\Badges\BadgeTypes;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProductBadges implements BadgeTypes
{
    public function createBadges(Collection $productBadges):array
    {
        $badges = [];

        $productBadges->each(function($productBadge) use (&$badges){
            $badgeSettings = config('shop.badges.' . $productBadge->badges_id);
            if ($productBadge->updated_at->addHours($badgeSettings['ttl']) >= Carbon::now()){
                $badges[] = [
                    'title' => $productBadge->badge->title,
                    'class' => $badgeSettings['class'],
                ];
            }
        });

        return $badges;
    }
}