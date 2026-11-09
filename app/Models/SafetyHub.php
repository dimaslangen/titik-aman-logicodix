<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SafetyHub extends Model { protected $guarded=[]; protected $casts=['lat'=>'float','lng'=>'float','is_24h'=>'boolean']; }
