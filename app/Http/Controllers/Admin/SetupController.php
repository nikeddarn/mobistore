<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SetupController extends Controller
{
    /**
     * Create new categories or show form to confirm categories recreation if exist.
     *
     * @return \Illuminate\View\View
     */
    public function setup()
    {
        if (Category::all()->count() || Brand::all()->count()) {
            return view('content.admin.setup.setup_confirmation')->with('message', 'All data will be destroyed. Proceed anyway?');
        } else {
            $this->fillDatabase();
            return view('content.admin.setup.message')->with($this->successMessage());
        }

    }

    /**
     * Create new categories if request is checked.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function confirmSetup(Request $request)
    {
        if ($request->get('setup_confirmation')) {
            $this->fillDatabase();
            return view('content.admin.setup.message')->with($this->successMessage());
        } else {
            return view('content.admin.setup.message')->with($this->declineMessage());
        }
    }

    /**
     * Destroy old data.
     * Fill in database new data.
     *
     * @return void
     */
    private function fillDatabase()
    {
        $this->destroyOldData();
        $this->buildCategories();
        $this->buildBrands();
    }

    /**
     * Create success message.
     *
     * @return array
     */
    private function successMessage()
    {
        return [
            'setup_message' => 'Database was successfully filled.'
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

    private function destroyOldData()
    {
        DB::statement("DELETE FROM `products`");
        DB::statement("DELETE FROM `brands`");
        DB::statement("DELETE FROM `categories`");
    }

    /**
     * Create tree of categories.
     *
     * @return \App\Models\Category Root node.
     */
    private function buildCategories()
    {
        return Category::create(require database_path('setup/categories.php'));
    }

    /**
     * Create list of brands.
     *
     * @return void
     */
    private function buildBrands()
    {
        Brand::insert(require database_path('setup/brands.php'));
    }
}