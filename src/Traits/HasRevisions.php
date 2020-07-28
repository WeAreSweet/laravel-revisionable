<?php namespace WeAreSweet\LaravelRevisionable\Traits;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use WeAreSweet\LaravelRevisionable\Observers\RevisionableObserver;
use WeAreSweet\LaravelRevisionable\Models\Revision;

/**
 * Trait HasRevisions
 *
 * @property Collection revisions
 *
 * @mixin Model
 *
 * @package WeAreSweet\LaravelRevisionable\Traits
 */
trait HasRevisions
{
    /**
     *
     */
    public static function bootHasRevisions()
    {
        static::observe(RevisionableObserver::class);
    }

    /**
     * @param HasRevisions $model
     *
     * @return bool
     */
    public function handleNewRevision(self $model) {
        if ($model->getDirty()) {
            $revisionData = $model->revisionableAttributes() === ['*']
                ? $model->getDirty()
                : array_intersect_key($model->getDirty(), array_flip($model->revisionableAttributes()));

            // Store revisionable data
            if (!empty($revisionData)) {
                $model->revisions()->create(['data' => $revisionData]);
            }

            // We only save the model if persistRevisions is false.
            if (!$model->persistRevisions()) {
                // Or if there are attributes which can be saved.
                if (
                    $model->revisionableAttributes() !== ['*'] &&
                    $allowedData = array_diff_key($model->getDirty(), array_flip($model->revisionableAttributes()))
                ) {
                    static::withoutEvents(function () use ($allowedData) {
                        return $this->save($allowedData);
                    });
                }

                $model->setRawAttributes($model->getOriginal());
            }
        }

        // Return value is used by observer to determine if the data should be saved on the model or not.
        return $model->persistRevisions();
    }

    /**
     * @return array
     */
    public function pendingRevisions()
    {
        return $this->revisions
            ->whereNull('approved_at')
            ->sortBy('created_at', null, false)
            ->pluck('data')
            ->collapse()
            ->toArray();
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function approveAllRevisions()
    {
        // If nothing to save we can return true
        if (empty($this->pendingRevisions())) return true;

        // Save the model without firing the save event
        $save = static::withoutEvents(function () {
            return $this->update($this->pendingRevisions());
        });

        // Check save was successful and delete all revision for model
        if ($save) {
            $this->revisions()->update(['approved_at' => now()]);
            return true;
        } else {
            throw new Exception('There was an error removing old revisions when approving all changes for model with ID ' . $this->getKey());
        }
    }

    /**
     * @return  bool Persist the revision to the model. If this value is false the approve method on the revision will
     *            need to be called in order to persist the data to the model.
     */
    public function persistRevisions()
    {
        return true;
    }

    /**
     * @return string[] Array of attributes that should be revisionable. Use * to allow all.
     */
    public function revisionableAttributes()
    {
        return ['*'];
    }

    /**
     * @return MorphMany
     */
    public function revisions()
    {
        return $this->morphMany(Revision::class, 'revisionable')
            ->orderByDesc('created_at');
    }
}
