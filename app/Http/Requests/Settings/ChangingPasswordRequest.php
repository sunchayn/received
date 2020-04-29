<?php

namespace App\Http\Requests\Settings;

use App\Http\Rules\CurrentPasswordMatch;
use Illuminate\Foundation\Http\FormRequest;

class ChangingPasswordRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'current' => ['bail', 'required', new CurrentPasswordMatch()],
            'password' => 'required|min:6',
            'password_confirmation' => 'same:password',
        ];
    }
}
