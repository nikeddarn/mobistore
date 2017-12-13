<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 12.12.17
 * Time: 15:26
 */

namespace App\Models\Traits;


use Illuminate\Database\Eloquent\Builder;

trait CompositeKeys
{
    protected function getKeyForSaveQuery()
    {

        $primaryKeyForSaveQuery = array(count($this->primaryKey));

        foreach ($this->primaryKey as $i => $pKey) {
            $primaryKeyForSaveQuery[$i] = isset($this->original[$this->getKeyName()[$i]])
                ? $this->original[$this->getKeyName()[$i]]
                : $this->getAttribute($this->getKeyName()[$i]);
        }

        return $primaryKeyForSaveQuery;

    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {

        foreach ($this->primaryKey as $i => $pKey) {
            $query->where($this->getKeyName()[$i], '=', $this->getKeyForSaveQuery()[$i]);
        }

        return $query;
    }
}