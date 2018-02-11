<?php

namespace App\Http\Controllers\Comment;

use App\Breadcrumbs\CategoryRouteBreadcrumbsCreator;
use App\Contracts\Shop\Products\Filters\FilterTypes;
use App\Models\Product;
use App\Models\ProductComment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class ProductCommentsController extends Controller implements FilterTypes
{
    /**
     * @var ProductComment
     */
    private $productComment;

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
     * @var Collection
     */
    private $comments;

    /**
     * @var CategoryRouteBreadcrumbsCreator
     */
    private $categoryRouteBreadcrumbsCreator;

    /**
     * @var Request
     */
    private $request;

    /**
     * ProductCommentsController constructor.
     * @param Request $request
     * @param ProductComment $productComment
     * @param Product $product
     * @param CategoryRouteBreadcrumbsCreator $categoryRouteBreadcrumbsCreator
     */
    public function __construct(Request $request, ProductComment $productComment, Product $product, CategoryRouteBreadcrumbsCreator $categoryRouteBreadcrumbsCreator)
    {

        $this->productComment = $productComment;
        $this->product = $product;
        $this->categoryRouteBreadcrumbsCreator = $categoryRouteBreadcrumbsCreator;
        $this->request = $request;
    }

    /**
     * @param int $productId
     * @return mixed
     * @throws \Exception
     */
    public function index(int $productId)
    {
        $this->selectedProduct = $this->retrieveProductData($productId);

        return response(
            view('content.product.product_comments.index')
                ->with($this->productViewData())
                ->with($this->commonMetaData())
                ->with($this->breadcrumbs())
                ->with($this->commentsData())
        )
            ->withHeaders($this->createHeaders());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $this->productComment->create($this->storeCommentData($request));

        // update 'updated_at' for correct define Last-Modified time
        $this->productComment->product()->touch();

        if ($request->get('rating') > 0) {
            $this->updateProductRating($request);
        }

        return redirect()->back();
    }

    /**
     * Retrieve product with related models.
     *
     * @param int $productId
     * @return mixed
     */
    private function retrieveProductData(int $productId)
    {
        return $this->product
            ->where('id', $productId)
            ->select(array_merge($this->product->transformAttributesByLocale(['page_title', 'meta_title', 'meta_description', 'meta_keywords', 'summary']), ['id', 'url', 'categories_id', 'brands_id', 'colors_id', 'quality_id', 'rating', 'rating_count', 'updated_at']))
            ->with('category')
            ->with(['brand' => function ($query) {
                $query->select(['id', 'title', 'image', 'url']);
            }])
            ->with(['deviceModel' => function ($query) {
                $query->select(['id', 'url', 'title', 'series']);
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
        return [
            'product' => [
                'id' => $this->selectedProduct->id,
                'title' => $this->selectedProduct->page_title,
            ],
        ];
    }

    /**
     * Create meta data for the view.
     *
     * @return array
     */
    private function commonMetaData(): array
    {
        $additionalPhrases = trans('meta.phrases.comments');

        return [
            'commonMetaData' => [
                'title' => $this->selectedProduct->page_title . '. ' . $additionalPhrases['show'],
                'description' => $this->selectedProduct->page_title . '. ' . $additionalPhrases['show'] . '. ' . $additionalPhrases['add'],
                'keywords' => $this->selectedProduct->meta_keywords . ', ' . $additionalPhrases['show'] . ', ' . $additionalPhrases['add'],
            ],
        ];
    }

    /**
     * Get breadcrumbs from session if exists or create breadcrumbs from product properties.
     *
     * @return array
     * @throws \Exception
     */
    private function breadcrumbs(): array
    {
        if ($this->request->session()->has('breadcrumbs')) {
            $baseBreadcrumbs = $this->request->session()->get('breadcrumbs');
        } else {
            $baseBreadcrumbs = $this->categoryRouteBreadcrumbsCreator->createBreadcrumbs($this->getBreadcrumbCreatorItems());
        }

        return [
            'breadcrumbs' => array_merge($baseBreadcrumbs, $this->productBreadcrumb()),
        ];
    }

    /**
     * Create product and comment breadcrumbs.
     *
     * @return array
     */
    private function productBreadcrumb(): array
    {
        return [
            [
                'title' => $this->selectedProduct->breadcrumb ? $this->selectedProduct->breadcrumb : $this->selectedProduct->page_title,
                'url' => '/product/' . $this->selectedProduct->url,
            ]
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
     * Retrieve comments and user data.
     * @return array
     */
    private function commentsData()
    {
        $this->comments = $this->selectedProduct->comment->map(function ($item) {
            return [
                'comment' => $item->comment,
                'rating' => $item->rating,
                'userName' => isset($item->user) ? $item->user->name : $item->name,
                'userImage' => isset($item->user) ? $item->user->image : null,
                'date' => date('D, d M Y H:i', $item->updated_at->timestamp),
            ];
        });

        return [
            'comments' => $this->comments,
            'isUserLoggedIn' => (bool)auth('web')->user(),
        ];
    }

    /**
     * Comment validation rules.
     *
     * @return array
     */
    private function validationRules(): array
    {
        return [
            'name' => 'filled|max:32',
            'rating' => 'digits:1|between:0,5',
            'comment' => 'required|max:512',
            'product_id' => 'required|numeric'
        ];
    }

    private function storeCommentData(Request $request): array
    {
        $commentData = [];

        $user = auth('web')->user();
        if ($user) {
            $commentData['users_id'] = $user->id;
        } else {
            $commentData['name'] = $request->get('name');
        }

        $commentData['products_id'] = $request->get('product_id');

        if ($request->get('rating') > 0) {
            $commentData['rating'] = $request->get('rating');
        }

        $commentData['comment'] = $request->get('comment');

        return $commentData;
    }

    /**
     * Update product rating and product rating count.
     *
     * @param Request $request
     */
    private function updateProductRating(Request $request)
    {
        $product = $this->product->where('id', $request->get('product_id'))->first();
        $product->rating = ($product->rating * $product->rating_count + $request->get('rating')) / ($product->rating_count + 1);
        $product->rating_count = $product->rating_count + 1;
        $product->save();
    }

    /**
     * Create response headers.
     *
     * @return array
     */
    protected function createHeaders(): array
    {
        return [
            'Last-Modified' => date('D, d M Y H:i:s T', $this->selectedProduct->comment->max('updated_at')->timestamp),
        ];
    }
}
