<?php

namespace App\Http\Controllers\Product;

use App\Breadcrumbs\CategoryRouteBreadcrumbsCreator;
use App\Contracts\Shop\Products\Filters\FilterTypes;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductDetailsController extends Controller implements FilterTypes
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Product
     */
    private $product;

    /**
     * Retrieved by url product.
     *
     * @var Product
     */
    private $selectedProduct;
    /**
     * @var CategoryRouteBreadcrumbsCreator
     */
    private $categoryRouteBreadcrumbsCreator;

    /**
     * ProductDetailsController constructor.
     * @param Request $request
     * @param Product $product
     * @param CategoryRouteBreadcrumbsCreator $categoryRouteBreadcrumbsCreator
     */
    public function __construct(Request $request, Product $product, CategoryRouteBreadcrumbsCreator $categoryRouteBreadcrumbsCreator)
    {
        $this->request = $request;
        $this->product = $product;
        $this->categoryRouteBreadcrumbsCreator = $categoryRouteBreadcrumbsCreator;
    }

    public function index(string $productUrl)
    {
        $this->selectedProduct = $this->retrieveProductData($productUrl);

        return response(
            view('content.product.product_details.index')
                ->with($this->productViewData())
                ->with($this->commonMetaData())
                ->with($this->breadcrumbs())
                ->with($this->commentsData())
        )
            ->withHeaders($this->createHeaders());
    }


    /**
     * Retrieve product with related models.
     *
     * @param string $productUrl
     * @return mixed
     */
    private function retrieveProductData(string $productUrl)
    {
        return $this->product
            ->where('url', $productUrl)
            ->select(array_merge($this->product->transformAttributesByLocale(['page_title', 'meta_title', 'meta_description', 'meta_keywords', 'summary']), ['id', 'categories_id', 'brands_id', 'colors_id', 'quality_id', 'rating', 'rating_count',  'updated_at']))
            ->with('category', 'image', 'color', 'quality')
            ->with(['brand' => function ($query) {
                $query->select(['id', 'title', 'image', 'url']);
            }])
            ->with(['deviceModel' => function ($query) {
                $query->select(['id', 'title', 'series']);
            }])
            ->with('comment.user')
            ->firstOrFail();
    }

    /**
     * Create product data for the view.
     *
     * @return array
     */
    private function productViewData(): array
    {
        $productData = [
                'images' => $this->getProductImages(),
                'title' => $this->selectedProduct->page_title,
                'summary' => $this->selectedProduct->summary,
                'id' => $this->selectedProduct->id,
                'price' => $this->getProductPrice(),
                'quality' => $this->selectedProduct->quality->title,
                'availability' => $this->getProductAvailability(),
                'brand' => $this->selectedProduct->brand->title,
                'model' => $this->selectedProduct->deviceModel->implode('title', ', '),
                'color' => $this->selectedProduct->color->title,
                'category' => $this->selectedProduct->category->title,
        ];

        if ($this->selectedProduct->rating_count >= config('shop.min_rating_count_to_show')){
            $productData['rating'] = ceil($this->selectedProduct->rating);
        }

        return [
            'product' => $productData,
        ];
    }

    /**
     * Retrieve comments and user data.
     * @return array
     */
    private function commentsData()
    {
        return [
            'comments' => [],
            'isUserLoggedIn' => (bool)auth('web')->user(),
        ];
    }

    /**
     * Create array of images urls.
     *
     * @return array
     */
    private function getProductImages(): array
    {
        $imagePathPrefix = Storage::disk('public')->url('images/products/');

        return $this->selectedProduct->image->sortByDesc('is_primary')->each(function ($image) use ($imagePathPrefix) {
            $image->image = $imagePathPrefix . $image->image;
        })->pluck('image')->toArray();
    }

    /**
     *
     *
     * @return int
     */
    private function getProductPrice(): int
    {
        return 0;
    }

    private function getProductAvailability(): array
    {
        return [
            'class' => 'success',
            'title' => 'На складе'
        ];
    }

    /**
     * Create meta data for the view.
     *
     * @return array
     */
    private function commonMetaData(): array
    {
        return [
            'commonMetaData' => [
                'title' => $this->selectedProduct->meta_title,
                'description' => $this->selectedProduct->meta_description,
                'keywords' => $this->selectedProduct->meta_keywords,
            ],
        ];
    }

    /**
     * Get breadcrumbs from session if exists or create breadcrumbs from product properties.
     *
     * @return array
     */
    private function breadcrumbs(): array
    {
        if ($this->request->session()->has('breadcrumbs')) {
            $breadcrumbs = $this->request->session()->get('breadcrumbs');
            $this->request->session()->forget('breadcrumbs');
        } else {
            $breadcrumbs = $this->createBreadcrumbs();
        }

        return [
            'breadcrumbs' => $breadcrumbs,
        ];
    }

    /**
     * Create breadcrumbs from product properties.
     *
     * @return array
     */
    private function createBreadcrumbs(): array
    {
        $breadcrumbItems = [];

        $breadcrumbItems[self::CATEGORY] = ($this->selectedProduct->category->ancestors)->push($this->selectedProduct->category);

        if ($this->selectedProduct->brand) {
            $breadcrumbItems[self::BRAND] = collect()->push($this->selectedProduct->brand);
        }

        if ($this->selectedProduct->deviceModel && $this->selectedProduct->deviceModel->count() === 1) {
            $breadcrumbItems[self::MODEL] = $this->selectedProduct->deviceModel;
        }

        return $this->categoryRouteBreadcrumbsCreator->createBreadcrumbs($breadcrumbItems);
    }


    /**
     * Create response headers.
     *
     * @return array
     */
    protected function createHeaders(): array
    {
        return [
            'Last-Modified' => date('D, d M Y H:i:s T', $this->selectedProduct->updated_at->timestamp),
        ];
    }
}
