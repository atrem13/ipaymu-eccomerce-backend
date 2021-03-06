<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use \App\Http\Traits\UsesUuid;
    use HasFactory;

    protected $fillable = [
        'name', 
        'description', 
        'purchase_price',
        'sell_price',
        'img',
        'path_img',
    ];


}
