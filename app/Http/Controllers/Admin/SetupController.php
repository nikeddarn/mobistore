<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SetupController extends Controller
{
    /**
     * Create new categories or show form to confirm recreation categories if exist.
     *
     * @return \Illuminate\View\View
     */
    public function categories()
    {
        if (Category::all()->count()) {
            return view('content.admin.setup.categories_setup');
        } else {
            $this->buildCategories();
            return redirect('/setup/message')->with($this->successMessage());
        }

    }

    /**
     * Create new categories if request is checked.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmCreateCategories(Request $request)
    {
        if ($request->get('create_categories')) {
            $this->buildCategories();
            return redirect('/setup/message')->with($this->successMessage());
        } else {
            return redirect('/setup/message')->with($this->declineMessage());
        }
    }

    /**
     * Show setup message.
     *
     * @return \Illuminate\View\View
     */
    public function message()
    {
        return view('content.admin.setup.message')->with('setup_message', session('setup_message'));
    }


    /**
     * Create success message.
     *
     * @return array
     */
    private function successMessage()
    {
        return [
            'setup_message' => 'Your request was successfully executed.'
        ];
    }

    /**
     * Create decline message.
     *
     * @return array
     */
    private function declineMessage()
    {
        return [
            'setup_message' => 'Your request was declined.'
        ];
    }

    /**
     * Create tree of categories.
     *
     * @return \App\Models\Category Root node.
     */
    private function buildCategories()
    {
        return Category::create($this->baseCategoryTree());
    }

    /**
     * Tree of categories.
     *
     * @return array
     */
    private function baseCategoryTree()
    {
        return [
            'title_ru' => 'продукция',
            'title_ua' => 'продукция',
            'title_en' => 'products',
            'folder' => 'products',

            'children' => [
                [
                    'title_ru' => 'экраны',
                    'title_ua' => 'экраны',
                    'title_en' => 'screens',
                    'folder' => 'screens',
                ],
                [
                    'title_ru' => 'тачскрины',
                    'title_ua' => 'тачскрины',
                    'title_en' => 'touchscreens',
                    'folder' => 'touchscreens',
                ],
                [
                    'title_ru' => 'аксессуары',
                    'title_ua' => 'аксессуары',
                    'title_en' => 'accessory',
                    'folder' => 'accessory',

                    'children' => [
                        [
                            'title_ru' => 'наушники',
                            'title_ua' => 'наушники',
                            'title_en' => 'headphones',
                            'folder' => 'headphones',
                        ],
                    ],
                ],
            ],
        ];
    }
}
