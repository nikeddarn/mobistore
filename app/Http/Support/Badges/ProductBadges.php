<?php

/**
 * Create product badges.
 */

namespace App\Http\Controllers\Admin\Support\Badges;

use App\Contracts\Shop\Badges\BadgeTypes;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProductBadges implements BadgeTypes
{
    public function createBadges(Collection $productBadges): array
    {
        $badges = [];

        $AllBadgesSettings = config('shop.badges');

        $productBadges->each(function ($productBadge) use (&$badges, $AllBadgesSettings) {

            $badgeSettings = $AllBadgesSettings[$productBadge->badges_id];

            if ($badgeSettings['ttl'] === 0 || $productBadge->updated_at->addDays($badgeSettings['ttl']) >= Carbon::now()) {
                $badges[] = [
                    'title' => $productBadge->badge->title,
                    'class' => $badgeSettings['class'],
                ];
            } else if ($badgeSettings['ttl'] !== 0){
                $productBadge->delete();
            }

        });

        return $badges;
    }
}