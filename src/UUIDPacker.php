<?php

namespace mindplay\keypack;

use InvalidArgumentException;

/**
 * This class packs/unpacks UUID keys to/from shorter strings.
 */
class UUIDPacker extends KeyPacker
{
    /**
     * Pack a given UUID to a shorter string
     *
     * @param int $uuid UUID key
     *
     * @return string packed key
     */
    public function pack($uuid)
    {
        $hex = str_replace('-', '', strtolower($uuid));

        if (strlen($hex) !== 32) {
            throw new InvalidArgumentException("invalid UUID: {$uuid}");
        }

        return $this->packValue('hex', $hex);
    }

    /**
     * Unpack a short UUID key previously packed using `pack()`
     *
     * @param string $key packed key
     *
     * @return string|null UUID string (or NULL on failure)
     */
    public function unpack($key)
    {
        $str = $this->unpackValue('hex', $key);

        if ($str === null) {
            return null; // failure
        }

        $str = str_pad($str, 32, '0', STR_PAD_LEFT);

        return substr($str, 0, 8)
            . '-' . substr($str, 8, 4)
            . '-' . substr($str, 12, 4)
            . '-' . substr($str, 16, 4)
            . '-' . substr($str, 20, 12);
    }
}
