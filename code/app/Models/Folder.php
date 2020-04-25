<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Folder
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $slug
 * @property string|null $password
 * @property Carbon|null $shared_at
 *
 * @mixin \Eloquent
 */
class Folder extends Model
{
    protected $guarded = [];

    protected $dates = [
        'shared_at',
    ];

    public function isShared()
    {
        return $this->shared_at !== null && $this->password !== null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isOwnedBy(User $user)
    {
        return $this->user_id == $user->id;
    }
}
