<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

protected $table = 'candidate';


protected $fillable = [
        'name',
        'source',
        'owner',
        'created_at',
        'created_by'
];

public function user(){
    return $this->belongsTo('App\User', 'created_by');
}
}
