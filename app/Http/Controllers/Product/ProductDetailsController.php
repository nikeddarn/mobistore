<?php

namespace App\Http\Controllers\Product;

use App\Breadcrumbs\CategoryRouteBreadcrumbsCreator;
use App\Contracts\Shop\Products\Filters\FilterTypes;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
     * @var float
     */
    private $productPrice;

    /**
     * @var float
     */
    private $uahProductPrice;

    /**
     * @var Collection
     */
    private $comments;

    /**
     * @var CategoryRouteBreadcrumbsCreator
     */
    private $categoryRouteBreadcrumbsCreator;

    /**
     * @var Str
     */
    private $str;

    /**
     * ProductDetailsController constructor.
     * @param Request $request
     * @param Product $product
     * @param CategoryRouteBreadcrumbsCreator $categoryRouteBreadcrumbsCreator
     * @param Str $str
     */
    public function __construct(Request $request, Product $product, CategoryRouteBreadcrumbsCreator $categoryRouteBreadcrumbsCreator, Str $str)
    {
        $this->request = $request;
        $this->product = $product;
        $this->categoryRouteBreadcrumbsCreator = $categoryRouteBreadcrumbsCreator;
        $this->str = $str;
    }

    public function index(string $productUrl)
    {
        $this->selectedProduct = $this->retrieveProductData($productUrl);

        $this->productPrice = $this->getProductPrice();

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
            ->select(array_merge($this->product->transformAttributesByLocale(['page_title', 'meta_title', 'meta_description', 'meta_keywords', 'summary']), ['id', 'categories_id', 'brands_id', 'colors_id', 'quality_id', 'rating', 'rating_count', 'updated_at']))
            ->with('category', 'image', 'color', 'quality')
            ->with(['brand' => function ($query) {
                $query->select(['id', 'title', 'image', 'url']);
            }])
            ->with(['deviceModel' => function ($query) {
                $query->select(['id', 'url', 'title', 'series']);
            }])
            ->with('recentComment.user')
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
            'price' => $this->productPrice,
            'quality' => $this->selectedProduct->quality->title,
            'availability' => $this->getProductAvailability(),
            'brand' => $this->selectedProduct->brand->title,
            'model' => $this->selectedProduct->deviceModel->implode('title', ', '),
            'color' => $this->selectedProduct->color->title,
            'category' => $this->selectedProduct->category->title,
        ];

        if ($this->selectedProduct->rating_count >= config('shop.min_rating_count_to_show')) {
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
        $showCommentsCount = config('shop.product_details_comment_count');
        $this->comments = $this->selectedProduct->recentComment->take($showCommentsCount)->map(function ($item) {
            return [
                'comment' => $item->comment,
                'rating' => $item->rating,
                'userName' => isset($item->user) ? $item->user->name : $item->name,
                'userImage' => isset($item->user) ? $item->user->image : null,
            ];
        });

        return [
            'comments' => $this->comments,
            'isUserLoggedIn' => (bool)auth('web')->user(),
            'hasMoreComments' => $this->selectedProduct->recentComment->count() > $showCommentsCount,
        ];
    }

    /**
     * Create array of images urls.
     *
     * @return array
     */
    private function getProductImages(): array
    {
        $imagePathPrefix = Storage::disk('public')->url('images/products/big/');

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
        $description = $this->selectedProduct->meta_description . '. ';
        if (isset($this->uahProductPrice)){
            $description .= $this->str->ucfirst(trans('meta.phrases.buу_for_price', ['price' => $this->uahProductPrice])) . '. ';
        }else{
            $description .= $this->str->ucfirst(trans('meta.phrases.bue')) . '. ';
        }
        $description .= $this->str->ucfirst(trans('meta.phrases.phones')) . '.';

        return [
            'commonMetaData' => [
                'title' => $this->selectedProduct->meta_title,
                'description' => $description,
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
            $baseBreadcrumbs = $this->request->session()->get('breadcrumbs');
        } else {
            $baseBreadcrumbs = $this->categoryRouteBreadcrumbsCreator->createBreadcrumbs($this->getBreadcrumbCreatorItems());
        }

        return [
            'breadcrumbs' => array_merge($baseBreadcrumbs, $this->additionalBreadcrumbs()),
        ];
    }

    /**
     * Create product and comment breadcrumbs.
     *
     * @return array
     */
    private function additionalBreadcrumbs(): array
    {
        return [
            [
                'title' => $this->selectedProduct->breadcrumb ? $this->selectedProduct->breadcrumb : $this->selectedProduct->page_title,
            ],
        ];
    }

    /**
     * Create array of selected items for breadcrumb creator.
     *
     * @return array
     */
    private function getBreadcrumbCreatorItems(): array
    {
        $breadcrumbItems = [];

        $breadcrumbItems[self::CATEGORY] = ($this->selectedProduct->category->ancestors)->push($this->selectedProduct->category);

        if ($this->selectedProduct->brand) {
            $breadcrumbItems[self::BRAND] = collect()->push($this->selectedProduct->brand);
        }

        if ($this->selectedProduct->deviceModel && $this->selectedProduct->deviceModel->count() === 1) {
            $breadcrumbItems[self::MODEL] = $this->selectedProduct->deviceModel;
        }

        return $breadcrumbItems;
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
