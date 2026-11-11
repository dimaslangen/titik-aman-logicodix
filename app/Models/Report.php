<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Report extends Model {
    protected $guarded=[];
    protected $casts=['lat'=>'float','lng'=>'float','user_lat'=>'float','user_lng'=>'float','ai_check'=>'array','expires_at'=>'datetime','verified_at'=>'datetime'];
    protected $hidden=['user_id'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function upvotes(): HasMany { return $this->hasMany(ReportUpvote::class); }
    public function scopeActive($q){ return $q->whereIn('status',['UNVERIFIED','VERIFIED','PENDING_MODERATION'])->where(function($x){ $x->whereNull('expires_at')->orWhere('expires_at','>',now()); }); }
    public function isExpired(): bool { return $this->expires_at && $this->expires_at->isPast(); }
    public function toPublicArray(): array {
        return ['id'=>$this->id,'region_slug'=>$this->region_slug,'category'=>$this->category,
        'color'=> $this->category==='hambatan_fisik' ? 'yellow' : 'red_orange',
        'label'=> Report::labels()[$this->category] ?? $this->category,
        'lat'=>$this->lat,'lng'=>$this->lng,'description'=>$this->description,
        'status'=>$this->status,'upvotes_count'=>$this->upvotes_count,
        'expires_at'=>$this->expires_at,'created_at'=>$this->created_at,'is_simulation'=>(bool)$this->is_simulation];
    }
    public static function labels(): array { return ['penerangan_padam'=>'Penerangan Padam / Terbatas','area_sepi'=>'Kondisi Area Sepi / Perlu Perhatian','hambatan_fisik'=>'Hambatan Fisik Jalan']; }
    public static function categories(): array { return array_keys(self::labels()); }
}
