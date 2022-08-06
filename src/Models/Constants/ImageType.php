<?php

namespace WalkerChiu\MorphImage\Models\Constants;

/**
 * @license MIT
 * @package WalkerChiu\MorphImage
 *
 *
 */

class ImageType
{
    /**
     * @return Array
     */
    public static function getCodes(): array
    {
        $items = [];
        $types = self::all();
        foreach ($types as $code => $type) {
            array_push($items, $code);
        }

        return $items;
    }

    /**
     * @param Bool  $onlyVaild
     * @return Array
     */
    public static function options($onlyVaild = false): array
    {
        $items = $onlyVaild ? [] : ['' => trans('php-core::system.null')];

        $types = self::all();
        foreach ($types as $key => $value) {
            $items = array_merge($items, [$key => trans('php-morph-image::system.imageType.'.$key)]);
        }

        return $items;
    }

    /**
     * @return Array
     */
    public static function all(): array
    {
        return [
            'cover' => 'Cover',
            'icon'  => 'Icon',
            'image' => 'Image',
            'logo'  => 'Logo',
            'frame' => 'Frame',

            'eye'         => 'Eye',
            'face'        => 'Face',
            'fingerprint' => 'Fingerprint',
            'imprint'     => 'Imprint',
            'palm'        => 'Palm',
            'object'      => 'Object'
        ];
    }
}
