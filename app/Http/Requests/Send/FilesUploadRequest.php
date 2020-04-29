<?php

namespace App\Http\Requests\Send;

use Illuminate\Foundation\Http\FormRequest;

class FilesUploadRequest extends FormRequest
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
            'password' => 'required',
            'files' => 'required|array',
            'files.*' => 'file',
        ];
    }
}
