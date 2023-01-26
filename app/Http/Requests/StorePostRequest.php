<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Stevebauman\Purify\Facades\Purify;

class StorePostRequest extends FormRequest
{
    const RULES = [
        'title' => ['required', 'string', 'between:3,255'],
        'description' => ['required', 'string', 'between:30,600'],
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return self::RULES;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'title' => Purify::clean($this->input('title')),
            'description' => Purify::clean($this->input('description')),
        ]);
    }
}
