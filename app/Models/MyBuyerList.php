<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyBuyerList extends Model
{
    protected $fillable = ['user_id', 'name'];

    public function buyers()
    {
        return $this->hasMany(MyBuyerListBuyer::class, 'my_buyer_list_id');
    }
}
