<?php

namespace mindplay\keypack;

/**
 * This class packs/unpacks integer keys to/from shorter strings.
 */
class IntPacker extends KeyPacker
{
    /**
     * Pack a given integer value to a shorter string
     *
     * @param int $int integer key
     *
     * @return string packed key
     */
    public function pack($int)
    {
        $key = $this->packValue('dec', $int);

        return $key;
    }

    /**
     * Unpack a short key previously packed using `pack()`
     *
     * @param string $key short key
     *
     * @return int|null integer value (or NULL on failure)
     */
    public function unpack($key)
    {
        $result = $this->unpackValue('dec', $key);

        return $result === null
            ? null // failure
            : (int) $result;
    }
}
