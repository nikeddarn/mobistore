<?php
/**
 * Constraints for vendor invoice retrieve.
 */

namespace App\Http\Support\Invoices\Repositories\Storage;

class StorageInvoiceConstraints
{
    /**
     * @var null|int
     */
    public $invoiceStatus = null;

    /**
     * @var int
     */
    public $storageId;

    /**
     * @var null|int|array
     */
    public $invoiceType = null;

    /**
     * @var null|string
     */
    public $invoiceDirection = null;

    /**
     * @var null|bool
     */
    public $implementedStatus = null;

    /**
     * @var null|int
     */
    public $paginate = null;

    /**
     * @var bool
     */
    public $withRelations = null;

    /**
     * @param int|null $invoiceStatus
     * @return StorageInvoiceConstraints
     */
    public function setInvoiceStatus(int $invoiceStatus): StorageInvoiceConstraints
    {
        $this->invoiceStatus = $invoiceStatus;
        return $this;
    }

    /**
     * @param int $storageId
     * @return StorageInvoiceConstraints
     */
    public function setStorageId(int $storageId): StorageInvoiceConstraints
    {
        $this->storageId = $storageId;
        return $this;
    }

    /**
     * @param int|array $invoiceType
     * @return StorageInvoiceConstraints
     */
    public function setInvoiceType($invoiceType): StorageInvoiceConstraints
    {
        $this->invoiceType = $invoiceType;
        return $this;
    }

    /**
     * @param string $invoiceDirection
     * @return StorageInvoiceConstraints
     */
    public function setInvoiceDirection(string $invoiceDirection): StorageInvoiceConstraints
    {
        $this->invoiceDirection = $invoiceDirection;
        return $this;
    }

    /**
     * @param null $implementedStatus
     * @return StorageInvoiceConstraints
     */
    public function setImplementedStatus($implementedStatus): StorageInvoiceConstraints
    {
        $this->implementedStatus = $implementedStatus;
        return $this;
    }

    /**
     * @param null $paginate
     * @return StorageInvoiceConstraints
     */
    public function setPaginate($paginate): StorageInvoiceConstraints
    {
        $this->paginate = $paginate;
        return $this;
    }

    /**
     * @param bool $withRelations
     * @return StorageInvoiceConstraints
     */
    public function withRelations(bool $withRelations): StorageInvoiceConstraints
    {
        $this->withRelations = $withRelations;
        return $this;
    }
}