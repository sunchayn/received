<?php

namespace App\Http\Requests\Folders;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class RevokeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->route('folder')->isOwnedBy(Auth::user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
