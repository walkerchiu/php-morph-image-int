<?php

namespace WalkerChiu\MorphImage\Models\Entities;

use WalkerChiu\Core\Models\Entities\Lang;

class ImageLang extends Lang
{
    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.morph-image.images_lang');

        parent::__construct($attributes);
    }
}
