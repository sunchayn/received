<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Facades\Validator;

class SignupRequest
{
    /**
     * The validated data.
     * @var array
     */
    protected $data = [];

    /**
     * The validation errors.
     * @var array
     */
    protected $errors = [];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone_number' => 'required|regex:/^[0-9]{6,}$/|unique:users',
            'country_code' => 'required|regex:/^\+?[0-9]{3,4}$/',
            'password' => 'required|string|min:6',
            'country' => 'sometimes|nullable',
        ];
    }

    /**
     * Redirect the user to the right route with the validation errors.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function sendBackErrors()
    {
        if (request()->ajax()) {
            return response()->json([
                'errors' => $this->errors,
            ], 422);
        } else {
            return redirect()
                ->route('auth.signup')
                ->withErrors($this->errors)
                ->withInput();
        }
    }

    /**
     * Apply the request validation rules and validate it.
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate()
    {
        $validator = Validator::make(request()->all(), $this->rules());

        if ($validator->fails()) {
            $this->errors = $validator->errors();

            return;
        }

        $this->data = $validator->validated();
    }

    /**
     * Determine whether the given request data was valid or not.
     * @return bool
     */
    public function fails()
    {
        return ! empty($this->errors);
    }

    /**
     * Return the validated data.
     * @return array
     */
    public function validated()
    {
        return $this->data;
    }
}
