<?php

namespace App\Validators;

use App\Http\Requests\StorePostRequest;
use App\Services\TextInputFilterService;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class PostValidator
{
    /**
     * @param  array  $attributes
     * @return array
     *
     * @throws ValidationException
     */
    public function validate(array $attributes): array
    {
        $attributes = $this->prepareForValidation($attributes);

        return validator($attributes,
            array_merge(StorePostRequest::RULES, [
                'id' => ['required', 'integer'],
                'publishedAt' => ['required'],
            ])
        )->validate();
    }

    /**
     * @param  array  $attributes
     * @return array
     */
    protected function prepareForValidation(array $attributes): array
    {
        return array_merge($attributes, [
            'title' => TextInputFilterService::storeFilter(Arr::get($attributes, 'title', '')),
            'description' => TextInputFilterService::storeFilter(Arr::get($attributes, 'description', '')),
        ]);
    }
}
