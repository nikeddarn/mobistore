<?php
/**
 * Constraints for vendor invoice retrieve.
 */

namespace App\Http\Support\Invoices\Repositories\User;

class UserInvoiceConstraints
{
    /**
     * @var null|int
     */
    public $invoiceStatus = null;

    /**
     * @var int
     */
    public $userId;

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
     * @return UserInvoiceConstraints
     */
    public function setInvoiceStatus(int $invoiceStatus): UserInvoiceConstraints
    {
        $this->invoiceStatus = $invoiceStatus;
        return $this;
    }

    /**
     * @param int $userId
     * @return UserInvoiceConstraints
     */
    public function setUserId(int $userId): UserInvoiceConstraints
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @param int|array $invoiceType
     * @return UserInvoiceConstraints
     */
    public function setInvoiceType($invoiceType): UserInvoiceConstraints
    {
        $this->invoiceType = $invoiceType;
        return $this;
    }

    /**
     * @param string $invoiceDirection
     * @return UserInvoiceConstraints
     */
    public function setInvoiceDirection(string $invoiceDirection): UserInvoiceConstraints
    {
        $this->invoiceDirection = $invoiceDirection;
        return $this;
    }

    /**
     * @param null $implementedStatus
     * @return UserInvoiceConstraints
     */
    public function setImplementedStatus($implementedStatus): UserInvoiceConstraints
    {
        $this->implementedStatus = $implementedStatus;
        return $this;
    }

    /**
     * @param null $paginate
     * @return UserInvoiceConstraints
     */
    public function setPaginate($paginate): UserInvoiceConstraints
    {
        $this->paginate = $paginate;
        return $this;
    }

    /**
     * @param bool $withRelations
     * @return UserInvoiceConstraints
     */
    public function withRelations(bool $withRelations): UserInvoiceConstraints
    {
        $this->withRelations = $withRelations;
        return $this;
    }
}