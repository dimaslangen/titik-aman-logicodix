<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SosAlert extends Model { protected $guarded=[]; protected $casts=['lat'=>'float','lng'=>'float']; }
