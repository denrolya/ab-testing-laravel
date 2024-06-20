<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property integer $targeting_ratio
 * @property string $slug
 * @property ABTest $abTest
 */
class ABTestVariant extends Model
{
    use HasFactory;

    protected $table = 'ab_test_variants';

    protected $fillable = ['name', 'targeting_ratio'];

    protected $appends = ['slug'];

    public function abTest(): BelongsTo
    {
        return $this->belongsTo(ABTest::class, 'ab_test_id');
    }

    public function getSlugAttribute(): string
    {

        return Str::snake("ab_test {$this->abTest->name} {$this->abTest->id} variant {$this->name}");
    }
}
