<?php namespace WeAreSweet\LaravelRevisionable\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Revision
 *
 * @package App
 */
class Revision extends Model
{
    /**
     * @var string
     */
    protected $table = 'revisions';
    /**
     * @var string[]
     */
    protected $fillable = ['data'];
    /**
     * @var string[]
     */
    protected $casts = ['data' => 'array'];
    /**
     *
     */
    public const UPDATED_AT = 'calculated_at';

    /**
     *
     */
    public function model()
    {
        return $this->morphTo('revisionable');
    }

    /**
     * @return bool
     */
    public function approve()
    {
        $save = static::withoutEvents(function () {
            $this->model->update($this->data);
        });

        return $save ? $this->update(['approved_at' => now()]) : false;

    }
}