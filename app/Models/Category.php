<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
  use HasFactory;
  use InteractsWithMedia;
  
  protected $guarded = [];
  /**
   * @var array<string, string>
   */
  protected $casts = [
    'is_visible' => 'boolean',
  ];
  
  public function children() : HasMany
  {
    return $this->hasMany( self::class, 'parent_id' );
  }
  
  public function parent() : BelongsTo
  {
    return $this->belongsTo( self::class, 'parent_id' );
  }
  
  public function products() : BelongsToMany
  {
    return $this->belongsToMany( Product::class, 'category_product', 'category_id', 'product_id' );
  }
}