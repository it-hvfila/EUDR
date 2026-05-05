<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class news_it_images extends Model
{
    use HasFactory;
    protected $table = 'news_it_images';
    protected $fillable = ['image'];
}
