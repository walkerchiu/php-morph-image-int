<?php

namespace WalkerChiu\MorphImage\Models\Forms;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use WalkerChiu\Core\Models\Forms\FormRequest;

class ImageFormRequest extends FormRequest
{
    /**
     * @Override Illuminate\Foundation\Http\FormRequest::getValidatorInstance
     */
    protected function getValidatorInstance()
    {
        $request = Request::instance();
        $data = $this->all();
        if (
            $request->isMethod('put')
            && empty($data['id'])
            && isset($request->id)
        ) {
            $data['id'] = (int) $request->id;
            $this->getInputSource()->replace($data);
        }

        return parent::getValidatorInstance();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return Array
     */
    public function attributes()
    {
        return [
            'host_type'   => trans('php-morph-image::system.host_type'),
            'host_id'     => trans('php-morph-image::system.host_id'),
            'morph_type'  => trans('php-morph-image::system.morph_type'),
            'morph_id'    => trans('php-morph-image::system.morph_id'),

            'serial'      => trans('php-morph-image::system.serial'),
            'identifier'  => trans('php-morph-image::system.identifier'),
            'type'        => trans('php-morph-image::system.type'),
            'size'        => trans('php-morph-image::system.size'),
            'data'        => trans('php-morph-image::system.data'),
            'options'     => trans('php-morph-image::system.options'),
            'is_visible'  => trans('php-morph-image::system.is_visible'),
            'is_enabled'  => trans('php-morph-image::system.is_enabled'),
            'file'        => trans('php-morph-image::system.file'),

            'name'        => trans('php-morph-image::system.name'),
            'description' => trans('php-morph-image::system.description')
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return Array
     */
    public function rules()
    {
        $rules = [
            'host_type'   => 'required_with:host_id|string',
            'host_id'     => 'required_with:host_type|integer|min:1',
            'morph_type'  => 'required_with:morph_id|string',
            'morph_id'    => 'required_with:morph_type|integer|min:1',

            'serial'      => '',
            'identifier'  => 'nullable|max:255',
            'type'        => ['nullable', Rule::in(config('wk-core.class.morph-image.imageType')::getCodes())],
            'size'        => '',
            'data'        => '',
            'options'     => 'nullable|json',
            'is_visible'  => 'nullable|boolean',
            'is_enabled'  => 'boolean',
            'file'        => '',

            'name'        => 'required|string|max:255',
            'description' => ''
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','integer','min:1','exists:'.config('wk-core.table.morph-image.images').',id']]);
        } elseif ($request->isMethod('post')) {
            $rules = array_merge($rules, ['id' => ['nullable','integer','min:1','exists:'.config('wk-core.table.morph-image.images').',id']]);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return Array
     */
    public function messages()
    {
        return [
            'id.required'              => trans('php-core::validation.required'),
            'id.string'                => trans('php-core::validation.string'),
            'id.exists'                => trans('php-core::validation.exists'),
            'host_type.required_with'  => trans('php-core::validation.required_with'),
            'host_type.string'         => trans('php-core::validation.string'),
            'host_id.required_with'    => trans('php-core::validation.required_with'),
            'host_id.integer'          => trans('php-core::validation.integer'),
            'host_id.min'              => trans('php-core::validation.min'),
            'morph_type.required_with' => trans('php-core::validation.required_with'),
            'morph_type.string'        => trans('php-core::validation.string'),
            'morph_id.required_with'   => trans('php-core::validation.required_with'),
            'morph_id.integer'         => trans('php-core::validation.integer'),
            'morph_id.min'             => trans('php-core::validation.min'),
            'identifier.max'           => trans('php-core::validation.max'),
            'type.in'                  => trans('php-core::validation.in'),
            'options.json'             => trans('php-core::validation.json'),
            'is_visible.boolean'       => trans('php-core::validation.boolean'),
            'is_enabled.boolean'       => trans('php-core::validation.boolean'),

            'name.required'            => trans('php-core::validation.required'),
            'name.string'              => trans('php-core::validation.string'),
            'name.max'                 => trans('php-core::validation.max')
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after( function ($validator) {
            $data = $validator->getData();

            $messages = [
                'file.mimes'      => trans('php-core::validation.mimes'),
                'file.max'        => trans('php-core::validation.max'),
                'file.size'       => trans('php-core::validation.size'),
                'file.dimensions' => trans('php-core::validation.dimensions')
            ];
            if ( isset($data['type']) &&
                 in_array($data['type'], config('wk-core.class.morph-image.imageType')::getCodes()) ) {
                $rules = [
                    'file' => ['mimes:'.config('wk-morph-image.images.'.$data['type'].'.mines'),
                               'max:'.config('wk-morph-image.images.'.$data['type'].'.max-size'),
                               'dimensions:min_width='.config('wk-morph-image.images.'.$data['type'].'.min-width').
                                          ',min_height='.config('wk-morph-image.images.'.$data['type'].'.min-height').
                                          ',max_width='.config('wk-morph-image.images.'.$data['type'].'.max-width').
                                          ',max_height='.config('wk-morph-image.images.'.$data['type'].'.max-height')]
                ];
                $validator2 = Validator::make($data, $rules, $messages);
                if ($validator2->fails()) {
                    foreach ($validator2->errors()->all() as $error) {
                        $validator->errors()->add('file', $error);
                    }
                }
            } else {
                $rules = [
                    'file' => ['mimes:'.config('wk-morph-image.images.defalut.mines'),
                               'max:'.config('wk-morph-image.images.defalut.max-size'),
                               'dimensions:min_width='.config('wk-morph-image.images.defalut.min-width').
                                          ',min_height='.config('wk-morph-image.images.defalut.min-height').
                                          ',max_width='.config('wk-morph-image.images.defalut.max-width').
                                          ',max_height='.config('wk-morph-image.images.defalut.max-height')]
                ];
                $validator2 = Validator::make($data, $rules, $messages);
                if ($validator2->fails()) {
                    foreach ($validator2->errors()->all() as $error) {
                        $validator->errors()->add('file', $error);
                    }
                }
            }



            if (
                isset($data['host_type'])
                && isset($data['host_id'])
            ) {
                if ( config('wk-morph-image.onoff.blog') ) {
                    if (
                        !empty(config('wk-core.class.blog.article'))
                        && $data['host_type'] == config('wk-core.class.blog.article')
                    ) {
                        $result = DB::table(config('wk-core.table.blog.articles'))
                                    ->where('id', $data['host_id'])
                                    ->exists();
                        if (!$result)
                            $validator->errors()->add('host_id', trans('php-core::validation.exists'));
                    } elseif (
                        !empty(config('wk-core.class.blog.blog'))
                        && $data['host_type'] == config('wk-core.class.blog.blog')
                    ) {
                        $result = DB::table(config('wk-core.table.blog.blogs'))
                                    ->where('id', $data['host_id'])
                                    ->exists();
                        if (!$result)
                            $validator->errors()->add('host_id', trans('php-core::validation.exists'));
                    } elseif (
                        !empty(config('wk-core.class.blog.category'))
                        && $data['host_type'] == config('wk-core.class.blog.category')
                    ) {
                        $result = DB::table(config('wk-core.table.blog.categories'))
                                    ->where('id', $data['host_id'])
                                    ->exists();
                        if (!$result)
                            $validator->errors()->add('host_id', trans('php-core::validation.exists'));
                    }
                }
                if (
                    config('wk-morph-image.onoff.site')
                    && !empty(config('wk-core.class.site.site'))
                    && $data['host_type'] == config('wk-core.class.site.site')
                ) {
                    $result = DB::table(config('wk-core.table.site.sites'))
                                ->where('id', $data['host_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('host_id', trans('php-core::validation.exists'));
                }
                if (
                    config('wk-morph-image.onoff.group')
                    && !empty(config('wk-core.class.group.group'))
                    && $data['host_type'] == config('wk-core.class.group.group')
                ) {
                    $result = DB::table(config('wk-core.table.group.groups'))
                                ->where('id', $data['host_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('host_id', trans('php-core::validation.exists'));
                }
                if (
                    config('wk-morph-image.onoff.morph-category')
                    && !empty(config('wk-core.class.morph-category.category'))
                    && $data['host_type'] == config('wk-core.class.morph-category.category')
                ) {
                    $result = DB::table(config('wk-core.table.morph-category.categories'))
                                ->where('id', $data['host_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('host_id', trans('php-core::validation.exists'));
                }
            }



            if (
                isset($data['morph_type'])
                && isset($data['morph_id'])
            ) {
                if ( config('wk-morph-image.onoff.blog') ) {
                    if (
                        !empty(config('wk-core.class.blog.article'))
                        && $data['morph_type'] == config('wk-core.class.blog.article')
                    ) {
                        $result = DB::table(config('wk-core.table.blog.articles'))
                                    ->where('id', $data['morph_id'])
                                    ->exists();
                        if (!$result)
                            $validator->errors()->add('morph_id', trans('php-core::validation.exists'));
                    } elseif (
                        !empty(config('wk-core.class.blog.blog'))
                        && $data['morph_type'] == config('wk-core.class.blog.blog')
                    ) {
                        $result = DB::table(config('wk-core.table.blog.blogs'))
                                    ->where('id', $data['morph_id'])
                                    ->exists();
                        if (!$result)
                            $validator->errors()->add('morph_id', trans('php-core::validation.exists'));
                    } elseif (
                        !empty(config('wk-core.class.blog.category'))
                        && $data['morph_type'] == config('wk-core.class.blog.category')
                    ) {
                        $result = DB::table(config('wk-core.table.blog.categories'))
                                    ->where('id', $data['morph_id'])
                                    ->exists();
                        if (!$result)
                            $validator->errors()->add('morph_id', trans('php-core::validation.exists'));
                    }
                }
                if (
                    config('wk-morph-image.onoff.site')
                    && !empty(config('wk-core.class.site.site'))
                    && $data['morph_type'] == config('wk-core.class.site.site')
                ) {
                    $result = DB::table(config('wk-core.table.site.sites'))
                                ->where('id', $data['morph_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('morph_id', trans('php-core::validation.exists'));
                }
                if (
                    config('wk-morph-image.onoff.group')
                    && !empty(config('wk-core.class.group.group'))
                    && $data['morph_type'] == config('wk-core.class.group.group')
                ) {
                    $result = DB::table(config('wk-core.table.group.groups'))
                                ->where('id', $data['morph_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('morph_id', trans('php-core::validation.exists'));
                }
                if (
                    config('wk-morph-image.onoff.morph-category')
                    && !empty(config('wk-core.class.morph-category.category'))
                    && $data['morph_type'] == config('wk-core.class.morph-category.category')
                ) {
                    $result = DB::table(config('wk-core.table.morph-category.categories'))
                                ->where('id', $data['morph_id'])
                                ->exists();
                    if (!$result)
                        $validator->errors()->add('morph_id', trans('php-core::validation.exists'));
                }
                if ( config('wk-morph-image.onoff.mall-shelf') ) {
                    if (
                        !empty(config('wk-core.class.mall-shelf.catalog'))
                        && $data['morph_type'] == config('wk-core.class.mall-shelf.catalog')
                    ) {
                        $result = DB::table(config('wk-core.table.mall-shelf.catalogs'))
                                    ->where('id', $data['morph_id'])
                                    ->exists();
                        if (!$result)
                            $validator->errors()->add('morph_id', trans('php-core::validation.exists'));
                    } elseif (
                        !empty(config('wk-core.class.mall-shelf.product'))
                        && $data['morph_type'] == config('wk-core.class.mall-shelf.product')
                    ) {
                        $result = DB::table(config('wk-core.table.mall-shelf.products'))
                                    ->where('id', $data['morph_id'])
                                    ->exists();
                        if (!$result)
                            $validator->errors()->add('morph_id', trans('php-core::validation.exists'));

                    } elseif (
                        !empty(config('wk-core.class.mall-shelf.stock'))
                        && $data['morph_type'] == config('wk-core.class.mall-shelf.stock')
                    ) {
                        $result = DB::table(config('wk-core.table.mall-shelf.stocks'))
                                    ->where('id', $data['morph_id'])
                                    ->exists();
                        if (!$result)
                            $validator->errors()->add('morph_id', trans('php-core::validation.exists'));
                    }
                }
                if (isset($data['identifier'])) {
                    $result = config('wk-core.class.morph-image.image')::where('identifier', $data['identifier'])
                                    ->when(isset($data['id']), function ($query) use ($data) {
                                        return $query->where('id', '<>', $data['id']);
                                      })
                                    ->exists();
                    if ($result)
                        $validator->errors()->add('identifier', trans('php-core::validation.unique', ['attribute' => trans('php-morph-image::system.identifier')]));
                }
            }
        });
    }
}
