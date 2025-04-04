<?php

namespace App\Services;

use Hashids\Hashids;

class HashIdService
{
    protected $hashids;

    public function __construct()
    {
        // Usa una salt segura desde tus variables de entorno
        $this->hashids = new Hashids(config('hashids.salt'), config('hashids.length'));
    }

    public function encode($id)
    {
        return $this->hashids->encode($id);
    }

    public function decode($hash)
    {
        $decoded = $this->hashids->decode($hash);
        return !empty($decoded) ? $decoded[0] : null;
    }
}
