<?php
/**
 * User recent product repository.
 */

namespace App\Http\Support\ProductRepository;


use App\Models\Product;
use App\Models\RecentProduct;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

class UserRecentProductRepository
{
    /**
     * @var Product
     */
    private $product;

    /**
     * @var RecentProduct
     */
    private $recentProduct;

    /**
     * StorageProductRepository constructor.
     *
     * @param Product $product
     * @param RecentProduct $recentProduct
     */
    public function __construct(Product $product, RecentProduct $recentProduct)
    {
        $this->product = $product;
        $this->recentProduct = $recentProduct;
    }

    /**
     * Get user favourite products collection.
     *
     * @param User|Authenticatable $user
     * @return Collection
     */
    public function getUserRecentProducts(User $user): Collection
    {
        $userId = $user->id;

        $stockProductConditions = function ($query) {
            $query->where('stock_quantity', '>', 0);
        };

        return $this->product->whereHas('recentProduct', function ($query) use ($userId) {
            $query->where('users_id', $userId);
        })
            ->with(['storageProduct' => $stockProductConditions, 'vendorProduct' => $stockProductConditions])
            ->with('primaryImage', 'productBadge.badge')
            ->get();
    }

    /**
     * Update or add to recent products.
     *
     * @param User|Authenticatable $user
     * @param int $productId
     * @return bool
     */
    public function updateOrCreateUserRecentProduct(User $user, int $productId): bool
    {
        return (bool)$this->recentProduct->updateOrCreate([
            'products_id' => $productId,
            'users_id' => $user->id,
        ]);
    }
}