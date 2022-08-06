<?php

namespace WalkerChiu\MorphImage\Models\Forms;

use WalkerChiu\Core\Models\Forms\FormRequest;

class ImageDeleteFormRequest extends FormRequest
{
    /**
     * @Override Illuminate\Foundation\Http\FormRequest::getValidatorInstance
     */
    protected function getValidatorInstance()
    {
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
            'host_type'  => trans('php-morph-image::system.host_type'),
            'host_id'    => trans('php-morph-image::system.host_id'),
            'morph_type' => trans('php-morph-image::system.morph_type'),
            'morph_id'   => trans('php-morph-image::system.morph_id')
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return Array
     */
    public function rules()
    {
        return [
            'id'         => ['required','integer','min:1','exists:'.config('wk-core.table.morph-image.images').',id'],
            'host_type'  => 'required_with:host_id|string',
            'host_id'    => 'required_with:host_type|integer|min:1',
            'morph_type' => 'required_with:morph_id|string',
            'morph_id'   => 'required_with:morph_type|integer|min:1'
        ];
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
            'id.integer'               => trans('php-core::validation.integer'),
            'id.min'                   => trans('php-core::validation.min'),
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
            'morph_id.min'             => trans('php-core::validation.min')
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

            $image = config('wk-core.class.morph-image.image')::where('id', $data['id'])
                        ->unless(empty($data['host_type']), function ($query) use ($data) {
                            return $query->where('host_type', $data['host_type']);
                        })
                        ->unless(empty($data['host_id']), function ($query) use ($data) {
                            return $query->where('host_id', $data['host_id']);
                        })
                        ->unless(empty($data['morph_type']), function ($query) use ($data) {
                            return $query->where('morph_type', $data['morph_type']);
                        })
                        ->unless(empty($data['morph_id']), function ($query) use ($data) {
                            return $query->where('morph_id', $data['morph_id']);
                        })
                        ->first();
            if (empty($image))
                $validator->errors()->add('id', trans('php-core::validation.not_allowed'));
        });
    }
}
