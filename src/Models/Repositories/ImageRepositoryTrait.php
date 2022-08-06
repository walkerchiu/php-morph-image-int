<?php

namespace WalkerChiu\MorphImage\Models\Repositories;

trait ImageRepositoryTrait
{
    /**
     * @param Image         $record
     * @param Array|String  $code
     * @param Bool          $is_frontend
     * @return Array
     */
    public function packRecord($record, $code, ?bool $is_frontend = false): array
    {
        if (is_array($code)) {
            $list = [];
            foreach ($code as $lang) {
                $list[$lang] = $this->packRecord($record, $lang);
            }

            return $list;

        } elseif (is_string($code)) {
            if ($is_frontend) {
                return [
                    'filename'   => $record->filename,
                    'serial'     => $record->serial,
                    'identifier' => $record->identifier,
                    'name'       => $record->findLang($code, 'name'),
                    'alt'        => $record->findLang($code, 'alt'),
                    'type'       => $record->type,
                    'size'       => $record->size,
                    'options'    => $record->options,
                    'comments'   => config('wk-morph-image.onoff.morph-comment')
                                        ? $this->getlistOfComments($record)
                                        : []
                ];
            } else {
                return [
                    'id'         => $record->id,
                    'filename'   => $record->filename,
                    'serial'     => $record->serial,
                    'identifier' => $record->identifier,
                    'name'       => $record->findLang($code, 'name'),
                    'alt'        => $record->findLang($code, 'alt'),
                    'type'       => $record->type,
                    'size'       => $record->size,
                    'data'       => $record->data,
                    'options'    => $record->options,
                    'comments'   => config('wk-morph-image.onoff.morph-comment')
                                        ? $this->getlistOfComments($record)
                                        : []
                ];
            }
        }
    }

    /**
     * @param Collection    $records
     * @param Array|String  $code
     * @param Bool          $is_frontend
     * @return Array
     */
    public function getlist($records, $code, ?bool $is_frontend = false): array
    {
        $list = [];
        if (is_array($code)) {
            foreach ($records as $record) {
                foreach ($code as $lang) {
                    if ($is_frontend)
                        $list[$lang][] = $this->packRecord($record, $lang, $is_frontend);
                    else
                        $list[$record->id][$lang] = $this->packRecord($record, $lang, $is_frontend);
                }
            }
        } elseif (is_string($code)) {
            foreach ($records as $record) {
                if ($is_frontend)
                    $list[] = $this->packRecord($record, $code, $is_frontend);
                else
                    $list[$record->id] = $this->packRecord($record, $code, $is_frontend);
            }
        }

        return $list;
    }


    /*
    |--------------------------------------------------------------------------
    | Get icon
    |--------------------------------------------------------------------------
    */

    /**
     * @param Array|String  $code
     * @param Bool          $is_enabled
     * @param Bool          $is_frontend
     * @return Array
     */
    public function getlistOfIcons($code, ?bool $is_enabled, ?bool $is_frontend = false): array
    {
        $records = $this->instance->icons()->where('is_visible', 1)
                                            ->where('size', '!=', NULL)
                                            ->unless(is_null($is_enabled), function ($query) use ($is_enabled) {
                                                    return $query->where('is_enabled', $is_enabled);
                                                })
                                            ->all();

        return $this->getlist($records, $code, $is_frontend);
    }

    /**
     * @param Array|String  $code
     * @param Entity        $instance
     * @param String        $type
     * @param Bool          $is_frontend
     * @return Array
     */
    public function getEnabledIcon($code, $instance, ?string $type, ?bool $is_frontend = false): array
    {
        $records = $instance->icons()->where('is_visible', 1)
                                     ->where('is_enabled', 1)
                                     ->orderBy('updated_at', 'DESC')
                                     ->all();
        if (is_array($code)) {
            return $this->getlist($records, $code, $is_frontend);
        } elseif (is_string($code)) {
            foreach ($records as $record) {
                if (
                    is_null($type)
                    || ($type == $record->size)
                ) {
                    return $this->packRecord($record, $code, $is_frontend);
                }
            }
        }

        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Get logo
    |--------------------------------------------------------------------------
    */

    /**
     * @param Array|String  $code
     * @param Bool          $is_enabled
     * @param Entity        $instance
     * @param Bool          $is_frontend
     * @return Array
     */
    public function getlistOfLogos($code, ?bool $is_enabled, $instance = null, ?bool $is_frontend = false): array
    {
        $instance = $instance ? $instance : $this->instance;
        $records = $instance->logos()->where('is_visible', 1)
                                    ->where('size', '!=', NULL)
                                    ->unless(is_null($is_enabled), function ($query) use ($is_enabled) {
                                            return $query->where('is_enabled', $is_enabled);
                                        })
                                    ->all();

        return $this->getlist($records, $code, $is_frontend);
    }

    /**
     * @param Array|String  $code
     * @param Entity        $instance
     * @param String        $type
     * @param Bool          $is_frontend
     * @return Array
     */
    public function getEnabledLogo($code, $instance, ?string $type, ?bool $is_frontend = false): array
    {
        $records = $instance->logos()->where('is_visible', 1)
                                     ->where('is_enabled', 1)
                                     ->orderBy('updated_at', 'DESC')
                                     ->all();
        if (is_array($code)) {
            return $this->getlist($records, $code, $is_frontend);
        } elseif (is_string($code)) {
            foreach ($records as $record) {
                if (
                    is_null($type)
                    || ($type == $record->size)
                ) {
                    return $this->packRecord($record, $code, $is_frontend);
                }
            }
        }

        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Get cover
    |--------------------------------------------------------------------------
    */

    /**
     * @param Array|String  $code
     * @param Bool          $is_enabled
     * @param Entity        $instance
     * @param Bool          $is_frontend
     * @return Array
     */
    public function getlistOfCovers($code, ?bool $is_enabled = null, $instance = null, ?bool $is_frontend = false): array
    {
        $instance = $instance ? $instance : $this->instance;
        $records = $instance->covers()->where('is_visible', 1)
                                      ->where('size', '!=', NULL)
                                      ->unless(is_null($is_enabled), function ($query) use ($is_enabled) {
                                            return $query->where('is_enabled', $is_enabled);
                                        })
                                      ->get();

        return $this->getlist($records, $code, $is_frontend);
    }

    /**
     * @param Array|String  $code
     * @param Entity        $instance
     * @param String        $type
     * @param Bool          $is_frontend
     * @return Array
     */
    public function getEnabledCover($code, $instance, ?string $type = null, ?bool $is_frontend = false): array
    {
        $records = $instance->covers()->where('is_visible', 1)
                                      ->where('is_enabled', 1)
                                      ->orderBy('updated_at', 'DESC')
                                      ->get();
        if (is_array($code)) {
            return $this->getlist($records, $code, $is_frontend);
        } elseif (is_string($code)) {
            foreach ($records as $record) {
                if (
                    is_null($type)
                    || ($type == $record->size)
                ) {
                    return $this->packRecord($record, $code, $is_frontend);
                }
            }
        }

        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Get image list to show
    |--------------------------------------------------------------------------
    */

    /**
     * @param Array|String  $code
     * @param Bool          $is_onlySimple
     * @param Bool          $is_enabled
     * @param Entity        $instance
     * @param Bool          $is_frontend
     * @return Array
     */
    public function getlistOfImages($code, ?bool $is_onlySimple = false, ?bool $is_enabled, $instance = null, ?bool $is_frontend = false): array
    {
        $instance = $instance ? $instance : $this->instance;
        $records = $instance->images()->where('is_visible', 1)
                                      ->when($is_onlySimple, function ($query) {
                                            return $query->whereNull('type');
                                        })
                                      ->unless(is_null($is_enabled), function ($query) use ($is_enabled) {
                                            return $query->where('is_enabled', $is_enabled);
                                        })
                                      ->all();

        return $this->getlist($records, $code, $is_frontend);
    }

    /*
    |--------------------------------------------------------------------------
    | For Auto Complete
    |--------------------------------------------------------------------------
    */

    /**
     * @param String  $host_type
     * @param Int     $host_id
     * @param String  $code
     * @param Mixed   $value
     * @param Int     $count
     * @param String  $target
     * @param Bool    $target_is_enabled
     * @return Array
     */
    public function autoCompleteAltOfEnabled(?string $host_type, ?int $host_id, string $code, $value, $count = 10, $target = null, $target_is_enabled = null): array
    {
        $records = $this->instance->lang()::with('morph')
                                            ->ofEnabled()
                                            ->ofVisible()
                                            ->ofCodeAndKey($code, 'alt')
                                            ->whereHasMorph('morph', $this->instance->morph_type, function ($query) use ($host_type, $host_id) {
                                                $query->ofEnabled()
                                                        ->unless(
                                                                empty($host_type)
                                                                || empty($host_id)
                                                            , function ($query) use ($host_type, $host_id) {
                                                                return $query->whereHasMorph('host', $host_type, function ($query) {
                                                                    $query->ofEnabled();
                                                                });
                                                            });
                                            })
                                            ->where('value', 'LIKE', $value .'%')
                                            ->orderBy('updated_at', 'DESC')
                                            ->select('shelf_image_id', 'value')
                                            ->take($count)
                                            ->get();
        $list = [];
        foreach ($records as $record) {
            $list[] = [
                'id'     => $record->host->id,
                'serial' => $record->host->serial,
                'name'   => $record->host->findLang($code, 'name'),
                'alt'    => $record->value
            ];
        }

        return $list;
    }
}
