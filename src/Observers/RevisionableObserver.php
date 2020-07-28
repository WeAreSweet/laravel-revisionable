<?php namespace WeAreSweet\LaravelRevisionable\Observers;

use Illuminate\Database\Eloquent\Model;
use WeAreSweet\LaravelRevisionable\Traits\HasRevisions;

/**
 * Class RevisionableObserver
 *
 * @package App\Observers
 */
class RevisionableObserver
{
    /**
     * @param Model|HasRevisions $model
     *
     * @return bool
     */
    public function saving(Model $model)
    {
        return $model->handleNewRevision($model);
    }
}
