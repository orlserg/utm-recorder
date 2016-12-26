<?php

namespace App\Modules\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
