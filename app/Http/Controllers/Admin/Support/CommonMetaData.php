<?php
/**
 * Insert common meta data.
 */

namespace App\Http\Controllers\Admin\Support;


use App\Models\MetaData;

class CommonMetaData
{
    /**
     * @var MetaData
     */
    private $metaData;

    /**
     * MetaData constructor.
     *
     * @param \App\Models\MetaData $metaData
     */
    public function __construct(MetaData $metaData)
    {

        $this->metaData = $metaData;
    }

    /**
     * Insert common meta data in database.
     *
     * @return void
     */
    public function insertCommonMetadata()
    {
        $this->metaData->insert(require database_path('setup/meta.php'));
    }
}