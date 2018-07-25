<?php

namespace App\Http\Controllers\Storage;

use App\Models\Storage;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class StorageProductController extends Controller
{
    /**
     * @var Storage
     */
    private $retrievedStorage;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * StorageProductController constructor.
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Show storage products.
     *
     * @param int $storageId
     * @return View
     * @throws Exception
     */
    public function index(int $storageId)
    {
        $this->retrievedStorage = $this->storage->where('id', $storageId)->first();

        if (!$this->retrievedStorage) {
            throw new Exception('Storage is not defined.');
        }

        return view('content.storage.products.index')->with([
            'storageProducts' => $this->getStorageProducts(),
            'storageId' => $this->retrievedStorage->id,
            'storageTitle' => $this->retrievedStorage->title,
        ]);
    }

    /**
     * Get storage products.
     *
     * @return LengthAwarePaginator
     */
    private function getStorageProducts(): LengthAwarePaginator
    {
        return $this->retrievedStorage->storageProduct()->orderBy('stock_quantity')->with('product')->paginate();
    }
}
