<?php

namespace App\Http\ViewComposers;

use App\Http\Controllers\Admin\Support\Badges\ProductBadges;
use App\Http\Support\Price\ProductPrice;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FavouriteProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategoriesComposer
{
    /**
     * @var Category
     */
    private $category;

    /**
     * @var Brand
     */
    private $brand;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var ProductPrice
     */
    private $productPrice;
    /**
     * @var ProductBadges
     */
    private $productBadges;


    /**
     * CategoriesComposer constructor.
     * @param Category $category
     * @param Brand $brand
     * @param Product $product
     * @param ProductPrice $productPrice
     * @param ProductBadges $productBadges
     */
    public function __construct(Category $category, Brand $brand, Product $product, ProductPrice $productPrice, ProductBadges $productBadges)
    {

        $this->category = $category;
        $this->brand = $brand;
        $this->product = $product;
        $this->productPrice = $productPrice;
        $this->productBadges = $productBadges;
    }

    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('allCategories', $this->getCategoriesTree())
            ->with('allBrands', $this->getBrands())
            ->with('allFavourites', $this->getFavourites());
    }

    /**
     * Retrieve root's children.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getCategoriesTree(): Collection
    {
        $categories = $this->category->withDepth()->get()->toTree();

        return $categories->count() ? $categories[0]->children : collect();
    }

    /**
     * Retrieve brands
     *
     * @return \Illuminate\Support\Collection
     */
    private function getBrands(): Collection
    {
        return $this->brand->orderBy('priority', 'asc')->get();
    }

    /**
     * Retrieve user favourite products.
     *
     * @return Collection
     */
    private function getFavourites()
    {
        $user = auth('web')->user();

        if ($user) {
            $favourites = $this->retrieveFavouriteProducts($user);

            return $favourites->count() ? $this->formProductData($favourites) : null;
        } else {
            return null;
        }
    }

    private function retrieveFavouriteProducts(User $user)
    {
        return $this->product
            ->whereHas('favouriteProduct', function ($query) use ($user) {
                $query->where('users_id', $user->id);
            })
            ->with('primaryImage')
            ->with(['storageProduct' => function ($query) {
                $query->where('stock_quantity', '>', 0);
            }])
            ->with(['vendorProduct' => function ($query) {
                $query->where('stock_quantity', '>', 0);
            }])
            ->with('productBadge.badge')
            ->get();
    }

    /**
     * Prepare data for each product
     *
     * @param Collection $products
     * @return Collection
     */
    private function formProductData(Collection $products)
    {
        $rate = $this->productPrice->getRate();

        $productImagePathPrefix = Storage::disk('public')->url('images/products/small/');

        return $products->each(function ($product) use ($rate, $productImagePathPrefix) {

            $price = $this->productPrice->getPrice($product);

            $product->image = $product->primaryImage ? $productImagePathPrefix . $product->primaryImage->image : null;

            $product->price = $price ? number_format($price, 2, '.', ',') : null;
            $product->priceUah = $price && $rate ? number_format($price * $rate, 2, '.', ',') : null;

            $product->stockStatus = $product->storageProduct->count() ? 1 : ($product->vendorProduct->count() ? 0 : null);

            $product->badges = $this->productBadges->createBadges($product->productBadge);

        });

    }
}