<?php

namespace WalkerChiu\MorphImage\Models\Repositories;

use WalkerChiu\MorphComment\Models\Repositories\CommentRepositoryTrait;

class ImageRepositoryWithComment extends Repository
{
    use CommentRepositoryTrait;

    /**
     * @param Image         $instance
     * @param Array|String  $code
     * @return Array
     */
    public function show($instance, $code): array
    {
        $data = [
            'id'       => $instance ? $instance->id : '',
            'basic'    => [],
            'comments' => []
        ];

        if (empty($instance))
            return $data;

        $this->setEntity($instance);

        if (is_string($code)) {
            $data['basic'] = [
                'filename'   => $record->filename,
                'serial'     => $record->serial,
                'identifier' => $record->identifier,
                'name'       => $record->findLang($code, 'name'),
                'alt'        => $record->findLang($code, 'alt'),
                'type'       => $record->type,
                'size'       => $record->size,
                'data'       => $record->data,
                'options'    => $record->options,
                'is_visible' => $record->is_visible,
                'is_enabled' => $record->is_enabled,
                'created_at' => $instance->created_at,
                'updated_at' => $instance->updated_at
            ];

        } elseif (is_array($code)) {
            foreach ($code as $language) {
                $data['basic'][$language] = [
                    'filename'   => $record->filename,
                    'serial'     => $record->serial,
                    'identifier' => $record->identifier,
                    'name'       => $record->findLang($language, 'name'),
                    'alt'        => $record->findLang($language, 'alt'),
                    'type'       => $record->type,
                    'size'       => $record->size,
                    'data'       => $record->data,
                    'options'    => $record->options,
                    'is_visible' => $record->is_visible,
                    'is_enabled' => $record->is_enabled,
                    'created_at' => $instance->created_at,
                    'updated_at' => $instance->updated_at
                ];
            }
        }

        $data['comments'] = $this->getlistOfComments($instance);

        return $data;
    }
}
