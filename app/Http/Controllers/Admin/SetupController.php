<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SetupController extends Controller
{

    /**
     * Fill libraries. Create root.
     */
    public function setup()
    {
        $this->fillLibraries();

        if (!User::all()->count()) {
            $this->insertRootUser();
        }
    }

    /**
     * Create new categories or show form to confirm categories recreation if exist.
     *
     * @return \Illuminate\View\View
     */
    public function categories()
    {
        if (Category::all()->count() || Brand::all()->count()) {
            return view('content.admin.setup.setup_confirmation')->with('message', 'All data will be destroyed. Proceed anyway?');
        } else {
            $this->fillDatabase();
            return view('content.admin.setup.message')->with($this->successMessage());
        }

    }

    private function fillLibraries()
    {
        if (!Role::all()->count()) {
            Role::insert(
                [
                    ['title' => 'root'],
                ]
            );
        }
    }

    private function insertRootUser()
    {
        User::create([
            'name' => 'Nikeddarn',
            'email' => 'nikeddarn@gmail.com',
            'password' => bcrypt('assodance'),
            'roles_id' => Role::where('title', 'root')->first()->id,
        ]);
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
        $this->buildModels();
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
        DB::statement("DELETE FROM `models`");
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

    /**
     * Create list of brands.
     *
     * @return void
     */
    private function buildModels()
    {
        $modelsByBrands = require database_path('setup/models.php');

        foreach ($modelsByBrands as $brand => $modelsBySeries) {
            $brandId = Brand::where('title', $brand)->first()->id;
            foreach ($modelsBySeries as $series => $models) {
                foreach ($models as $model) {
                    DeviceModel::create(['brands_id' => $brandId, 'series' => $series, 'model' => $model]);
                }
            }
        }
    }
}