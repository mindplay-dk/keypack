<?php

namespace mindplay\keypack;

use InvalidArgumentException;

/**
 * This class packs/unpacks GUID keys to/from shorter strings.
 */
class GUIDPacker extends KeyPacker
{
    /**
     * Pack a given GUID to a shorter string
     *
     * @param int $guid GUID key
     *
     * @return string packed key
     */
    public function pack($guid)
    {
        $hex = str_replace('-', '', strtolower($guid));

        if (strlen($hex) !== 32) {
            throw new InvalidArgumentException("invalid GUID: {$guid}");
        }

        return $this->packValue('hex', $hex);
    }

    /**
     * Unpack a short GUID key previously packed using `pack()`
     *
     * @param string $key packed key
     *
     * @return string|null GUID string (or NULL on failure)
     */
    public function unpack($key)
    {
        $str = $this->unpackValue('hex', $key);

        if ($str === null) {
            return null; // failure
        }

        $str = str_pad(strtoupper($str), 32, '0', STR_PAD_LEFT);

        return substr($str, 0, 8)
            . '-' . substr($str, 8, 4)
            . '-' . substr($str, 12, 4)
            . '-' . substr($str, 16, 4)
            . '-' . substr($str, 20, 12);
    }
}
