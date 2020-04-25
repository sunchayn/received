<?php

namespace App\Http\Rules;

use Illuminate\Contracts\Validation\Rule;
use Auth;

class CurrentPasswordMatch implements Rule
{
    public function passes($attribute, $value)
    {
        return \Hash::check(request()->input('current'), Auth::user()->getAuthPassword());
    }

    public function message()
    {
        return ':attribute password is not valid.';
    }
}
