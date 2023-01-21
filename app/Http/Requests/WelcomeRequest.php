<?php

namespace App\Http\Requests;

use App\enums\SortByPublicationDate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WelcomeRequest extends FormRequest
{
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
        return [
            'sort.published_at' => [
                'nullable',
                Rule::in([SortByPublicationDate::OLD_TO_NEW->value, SortByPublicationDate::NEW_TO_OLD->value]),
            ]
        ];
    }
}

