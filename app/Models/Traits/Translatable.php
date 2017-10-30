<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 12.10.17
 * Time: 20:06
 */

namespace App\Models\Traits;

use Illuminate\Support\Facades\App;

trait Translatable
{
    /**
     * Returns a model attribute.
     *
     * @param $key
     * @return string
     */
    public function getAttribute($key)
    {
        if (isset($this->translatable) && in_array($key, $this->translatable)) {
            return $this->getTranslatedAttribute($key);
        }

        return parent::getAttribute($key);
    }

    /**
     * Returns a translatable model attribute based on the application's locale settings.
     *
     * @param $key
     * @return string
     */
    protected function getTranslatedAttribute($key)
    {
        $primaryLocale = App::getLocale();
        $fallbackLocale = config('app.fallback_locale');

        $value =  $this->getAttributeValue($key . '_' . $primaryLocale);

        if(!$value){
            $value = $this->getAttributeValue($key . '_' . $fallbackLocale);
        }
        return $value ? $value : null;
    }

    /**
     * Returns a model attribute.
     *
     * @param $key
     * @return string
     */
    public function setAttribute($key, $value)
    {
        if (isset($this->translatable) && in_array($key, $this->translatable)) {
            parent::setAttribute($key . '_' . config('app.locale'), $value);
        }else{
            parent::setAttribute($key, $value);
        }

        return $this;
    }

    public function transformAttributesByLocale($attributes)
    {
        $locale = App::getLocale();
        if(is_array($attributes)){
            $transformed = [];
            foreach ($attributes as $attribute){
                $transformed[] = $this->transformAttributeName($attribute, $locale);
            }
            return $transformed;
        }elseif(is_string($attributes)){
            return $this->transformAttributeName($attributes, $locale);
        }else{
            return '';
        }
    }

    protected function transformAttributeName(string $attribute, string $locale)
    {
        if (isset($this->translatable) && in_array($attribute, $this->translatable)) {
            return $attribute . '_' . $locale;
        }else{
            return $attribute;
        }
    }

}