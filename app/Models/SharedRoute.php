<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SharedRoute extends Model { protected $guarded=[]; protected $casts=['route_data'=>'array','expires_at'=>'datetime','origin_lat'=>'float','origin_lng'=>'float','destination_lat'=>'float','destination_lng'=>'float']; }
