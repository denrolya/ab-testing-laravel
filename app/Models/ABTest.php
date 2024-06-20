<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $status
 * @property Collection $variants
 */
class ABTest extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_RUNNING = 'running';
    public const STATUS_STOPPED = 'stopped';

    protected $table = 'ab_tests';

    protected $fillable = ['name', 'status'];

    public function variants(): HasMany
    {
        return $this->hasMany(ABTestVariant::class, 'ab_test_id');
    }

    public function isRunning(): bool
    {
        return $this->status === self::STATUS_RUNNING;
    }

    /**
     * @throws \Exception
     */
    public function start(): void
    {
        if ($this->status === self::STATUS_NOT_STARTED) {
            $this->status = self::STATUS_RUNNING;
            $this->save();
        } else {
            throw new \Exception('Cannot start a test that has already been started or stopped.');
        }
    }

    /**
     * @throws \Exception
     */
    public function stop(): void
    {
        if ($this->status === self::STATUS_RUNNING) {
            $this->status = self::STATUS_STOPPED;
            $this->save();
        } else {
            throw new \Exception('Cannot stop a test that is not running.');
        }
    }
}
