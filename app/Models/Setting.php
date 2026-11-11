<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Setting extends Model {
    protected $guarded=[];
    public static function get(string $key, string $default='0'): string {
        return (string) (self::where('key',$key)->first()?->value ?? env('SIMULATION_MODE') && $key==='simulation_mode' ? '1' : $default);
    }
    public static function simulationEnabled(): bool {
        $db = self::where('key','simulation_mode')->first();
        if ($db) return $db->value === '1';
        return filter_var(env('SIMULATION_MODE', false), FILTER_VALIDATE_BOOLEAN);
    }
}
