<?php

namespace App\Http\Controllers\Storage;

use App\Models\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class StorageController extends Controller
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * StorageController constructor.
     *
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Show list of storages with count of not handled invoices
     *
     * @return View
     */
    public function index()
    {
        return view('content.storage.list.index')
            ->with('storages', $this->getStorages());
    }

    /**
     * Get vendors with unclosed invoices.
     *
     * @return Collection
     */
    private function getStorages(): Collection
    {
        return $this->storage->with(['storageInvoice' => function ($query) {
            $query->where('implemented', 0);
        }])->get();
    }
}
