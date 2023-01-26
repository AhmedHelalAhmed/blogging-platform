<?php

namespace App\Validators;

use App\Http\Requests\StorePostRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Stevebauman\Purify\Facades\Purify;

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
            'title' => Purify::clean(Arr::get($attributes, 'title', '')),
            'description' => Purify::clean(Arr::get($attributes, 'description', '')),
        ]);
    }
}
