<?php

namespace App\Traits;

use App\Services\HashIdService;

trait HasHashId
{
    public function initializeHasHashId(): void
    {
        if (!in_array('hash_id', $this->appends)) {
            $this->appends[] = 'hash_id';
        }
    }


    /**
     * Get the hashed ID for the model.
     *
     * @return string
     */
    public function getHashIdAttribute(): string
    {
        return app(HashIdService::class)->encode($this->id);
    }

    /**
     * Retrieve the model by its hashed ID.
     *
     * @param string $hash_id
     * @return static
     */
    public static function findByHash(string $hash_id): static
    {
        $id = app(HashIdService::class)->decode($hash_id);
        return static::findOrFail($id);
    }

    /**
     * Resolve route binding using hash ID.
     *
     * @param mixed $value
     * @param string|null $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $id = app(HashIdService::class)->decode($value);
        return $this->findOrFail($id);
    }

    /**
     * Get the value of the model's route key.
     *
     * @return string
     */
    public function getRouteKey(): string
    {
        return $this->hash_id;
    }
}
