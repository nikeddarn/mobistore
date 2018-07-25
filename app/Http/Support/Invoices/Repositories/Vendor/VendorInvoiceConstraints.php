<?php
/**
 * Constraints for vendor invoice retrieve.
 */

namespace App\Http\Support\Invoices\Repositories\Vendor;

class VendorInvoiceConstraints
{
    /**
     * @var null|int
     */
    public $invoiceStatus = null;

    /**
     * @var int
     */
    public $vendorId;

    /**
     * @var array|null
     */
    public $invoicesId = null;

    /**
     * @var null|int|array
     */
    public $invoiceType = null;

    /**
     * @var null|string
     */
    public $invoiceDirection = null;

    /**
     * @var null|int
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
     * @return VendorInvoiceConstraints
     */
    public function setInvoiceStatus(int $invoiceStatus): VendorInvoiceConstraints
    {
        $this->invoiceStatus = $invoiceStatus;
        return $this;
    }

    /**
     * @param int $vendorId
     * @return VendorInvoiceConstraints
     */
    public function setVendorId(int $vendorId): VendorInvoiceConstraints
    {
        $this->vendorId = $vendorId;
        return $this;
    }

    /**
     * @param array $invoicesId
     * @return VendorInvoiceConstraints
     */
    public function setInvoicesId(array $invoicesId): VendorInvoiceConstraints
    {
        $this->invoicesId = $invoicesId;
        return $this;
    }

    /**
     * @param int|array $invoiceType
     * @return VendorInvoiceConstraints
     */
    public function setInvoiceType($invoiceType): VendorInvoiceConstraints
    {
        $this->invoiceType = $invoiceType;
        return $this;
    }

    /**
     * @param string $invoiceDirection
     * @return VendorInvoiceConstraints
     */
    public function setInvoiceDirection(string $invoiceDirection): VendorInvoiceConstraints
    {
        $this->invoiceDirection = $invoiceDirection;
        return $this;
    }

    /**
     * @param int $implementedStatus
     * @return VendorInvoiceConstraints
     */
    public function setImplementedStatus(int $implementedStatus): VendorInvoiceConstraints
    {
        $this->implementedStatus = $implementedStatus;
        return $this;
    }

    /**
     * @param null $paginate
     * @return VendorInvoiceConstraints
     */
    public function setPaginate($paginate): VendorInvoiceConstraints
    {
        $this->paginate = $paginate;
        return $this;
    }

    /**
     * @param bool $withRelations
     * @return VendorInvoiceConstraints
     */
    public function withRelations(bool $withRelations): VendorInvoiceConstraints
    {
        $this->withRelations = $withRelations;
        return $this;
    }
}