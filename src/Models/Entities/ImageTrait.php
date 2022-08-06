<?php

namespace WalkerChiu\MorphImage\Models\Entities;

trait ImageTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function images()
    {
        return $this->morphMany(config('wk-core.class.morph-image.image'), 'morph');
    }

    /**
     * @param String  $size
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function icons(?string $size = null)
    {
        return $this->images()->where('type', 'icon')
                    ->when($size, function ($query, $size) {
                                return $query->where('size', $size);
                            });
    }

    /**
     * @param String  $size
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function logos(?string $size = null)
    {
        return $this->images()->where('type', 'logo')
                    ->when($size, function ($query, $size) {
                                return $query->where('size', $size);
                            });
    }

    /**
     * @param String  $size
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function covers(?string $size = null)
    {
        return $this->images()->where('type', 'cover')
                    ->when($size, function ($query, $size) {
                                return $query->where('size', $size);
                            });
    }
}
