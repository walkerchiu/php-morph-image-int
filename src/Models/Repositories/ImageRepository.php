<?php

namespace WalkerChiu\MorphImage\Models\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use WalkerChiu\Core\Models\Forms\FormHasHostTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryHasHostTrait;
use WalkerChiu\Core\Models\Services\PackagingFactory;

class ImageRepository extends Repository
{
    use FormHasHostTrait;
    use RepositoryHasHostTrait;

    protected $instance;



    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->instance = App::make(config('wk-core.class.morph-image.image'));
    }

    /**
     * @param String  $host_type
     * @param Int     $host_id
     * @param String  $code
     * @param Array   $data
     * @param Bool    $is_enabled
     * @param String  $target
     * @param Bool    $target_is_enabled
     * @param Bool    $auto_packing
     * @return Array|Collection|Eloquent
     */
    public function list(?string $host_type, ?int $host_id, string $code, array $data, $is_enabled = null, $target = null, $target_is_enabled = null, $auto_packing = false)
    {
        if (
            empty($host_type)
            || empty($host_id)
        ) {
            $instance = $this->instance;
        } else {
            $instance = $this->baseQueryForRepository($host_type, $host_id, $target, $target_is_enabled);
        }
        if ($is_enabled === true)      $instance = $instance->ofEnabled();
        elseif ($is_enabled === false) $instance = $instance->ofDisabled();

        $data = array_map('trim', $data);
        $repository = $instance->with(['langs' => function ($query) use ($code) {
                                    $query->ofCurrent()
                                          ->ofCode($code);
                                }])
                                ->whereHas('langs', function ($query) use ($code) {
                                    return $query->ofCurrent()
                                                 ->ofCode($code);
                                })
                                ->when(
                                    config('wk-morph-image.onoff.morph-tag')
                                    && !empty(config('wk-core.class.morph-tag.tag')
                                ), function ($query) {
                                    return $query->with(['tags', 'tags.langs']);
                                })
                                ->when($data, function ($query, $data) {
                                    return $query->unless(empty($data['id']), function ($query) use ($data) {
                                                return $query->where('id', $data['id']);
                                            })
                                            ->unless(empty($data['morph_type']), function ($query) use ($data) {
                                                return $query->where('morph_type', $data['morph_type']);
                                            })
                                            ->unless(empty($data['morph_id']), function ($query) use ($data) {
                                                return $query->where('morph_id', $data['morph_id']);
                                            })
                                            ->unless(empty($data['filename']), function ($query) use ($data) {
                                                return $query->where('filename', $data['filename']);
                                            })
                                            ->unless(empty($data['serial']), function ($query) use ($data) {
                                                return $query->where('serial', $data['serial']);
                                            })
                                            ->unless(empty($data['identifier']), function ($query) use ($data) {
                                                return $query->where('identifier', $data['identifier']);
                                            })
                                            ->when(isset($data['type']), function ($query) use ($data) {
                                                return $query->where('type', $data['type']);
                                            })
                                            ->unless(empty($data['size']), function ($query) use ($data) {
                                                return $query->where('size', $data['size']);
                                            })
                                            ->when(isset($data['is_visible']), function ($query) use ($data) {
                                                return $query->where('is_visible', $data['is_visible']);
                                            })
                                            ->unless(empty($data['name']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'name')
                                                          ->where('value', 'LIKE', "%".$data['name']."%");
                                                });
                                            })
                                            ->unless(empty($data['alt']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'alt')
                                                          ->where('value', 'LIKE', "%".$data['alt']."%");
                                                });
                                            })
                                            ->unless(empty($data['description']), function ($query) use ($data) {
                                                return $query->whereHas('langs', function ($query) use ($data) {
                                                    $query->ofCurrent()
                                                          ->where('key', 'description')
                                                          ->where('value', 'LIKE', "%".$data['description']."%");
                                                });
                                            })
                                            ->unless(empty($data['categories']), function ($query) use ($data) {
                                                return $query->whereHas('categories', function ($query) use ($data) {
                                                    $query->ofEnabled()
                                                          ->whereIn('id', $data['categories']);
                                                });
                                            })
                                            ->unless(empty($data['tags']), function ($query) use ($data) {
                                                return $query->whereHas('tags', function ($query) use ($data) {
                                                    $query->ofEnabled()
                                                          ->whereIn('id', $data['tags']);
                                                });
                                            })
                                            ->unless(
                                                empty($data['orderBy'])
                                                && empty($data['orderType'])
                                            , function ($query) use ($data) {
                                                return $query->orderBy($data['orderBy'], $data['orderType']);
                                            }, function ($query) {
                                                return $query->orderBy('updated_at', 'DESC');
                                            });
                                }, function ($query) {
                                    return $query->orderBy('updated_at', 'DESC');
                                });

        if ($auto_packing) {
            $factory = new PackagingFactory(config('wk-morph-image.output_format'), config('wk-morph-image.pagination.pageName'), config('wk-morph-image.pagination.perPage'));
            $factory->setFieldsLang(['name', 'alt', 'description']);
            return $factory->output($repository);
        }

        return $repository;
    }

    /**
     * @param Image         $instance
     * @param Array|String  $code
     * @return Array
     */
    public function show($instance, $code): array
    {
        $data = [
            'id'    => $instance ? $instance->id : '',
            'basic' => []
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

        return $data;
    }


    /**
     * @param FormData  $fileSource
     * @param Int       $id
     * @param Int       $order
     * @return String
     */
    public function createNewFileName($fileSource = null, ?int $id, $order = 0): string
    {
        $ext = is_null($fileSource) ? '' : '.'. $fileSource->getClientOriginalExtension();

        if (is_null($id)) {
            if (!empty($this->instance)) {
                return $this->instance->id .'_'. Carbon::now()->timestamp .'_'. $order . $ext;
            }
        } else {
            return $id .'_'. Carbon::now()->timestamp . $ext;
        }
    }

    /**
     * @param String    $directory
     * @param FormData  $fileSource
     * @param Int       $id
     * @param Int       $order
     * @return String
     */
    public function uploadImage(string $directory, $fileSource, int $id, $order = 0): string
    {
        $fileName = $this->createNewFileName($fileSource, $id, $order);
        $path = Storage::putFileAs($directory, $fileSource, $fileName);

        if ($this->find($id)->update(['filename' => $fileName]))
            return $path;
    }

    /**
     * @param Image|Int|Array  $data
     * @return Bool
     */
    public function removeImage($data): bool
    {
        $class = config('wk-core.class.morph-image.image');
        if ($data instanceOf $class)
            return (bool) $data->delete();
        elseif (is_integer($data))
            return (bool) $this->deleteByIds([$data]);
        elseif (is_array($data))
            return (bool) $this->deleteByIds($data);
    }
}
