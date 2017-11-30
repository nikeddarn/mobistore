<?php

namespace App\Http\Controllers\Comment;

use App\Models\Product;
use App\Models\ProductComment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductCommentsController extends Controller
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
     * ProductCommentsController constructor.
     * @param ProductComment $productComment
     * @param Product $product
     */
    public function __construct(ProductComment $productComment, Product $product)
    {

        $this->productComment = $productComment;
        $this->product = $product;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules());

        if ($validator->fails()) {
            return redirect(request()->server('HTTP_REFERER') . '#review')
                ->withErrors($validator)
                ->withInput();
        }

        $this->productComment->create($this->createCommentData($request));

        if ($request->get('rating') > 0) {
            $this->updateProductRating($request);
        }

        return redirect(request()->server('HTTP_REFERER') . '#review');
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

    private function createCommentData(Request $request): array
    {
        $commentData = [];

        $user = auth('web')->user();
        if ($user) {
            $commentData['users_id'] = $user->id;
        } else {
            $commentData['name'] = $request->get('name');
        }

        $commentData['products_id'] = $request->get('product_id');

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
}
