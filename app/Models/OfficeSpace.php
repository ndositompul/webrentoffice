<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class OfficeSpace extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'thumbnail',
        'is_open',
        'is_full_booked',
        'price',
        'duration',
        'address',
        'about',
        'slug',
        'city_id',
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function photos(): HasMany //1 office memiliki > 1 photo
    {
        return $this->hasMany(OfficeSpacePhoto::class);
    }

    public function benefits(): HasMany //1 office memiliki > 1 benefit
    {
        return $this->hasMany(OfficeSpaceBenefit::class);
    }

    public function city(): BelongsTo //belongsTo sebuah model untuk mengetahui kantor dimiliki oleh kota asalnya
    {
        return $this->belongsTo(City::class);
    }
}
