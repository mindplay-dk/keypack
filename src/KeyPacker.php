<?php

namespace mindplay\keypack;

use InvalidArgumentException;
use mindplay\nbase\NBaseConverter;

/**
 * This class provides a base implementation for services that pack/unpack keys to shorter strings.
 */
abstract class KeyPacker
{
    /**
     * @var NBaseConverter
     */
    private $converter;

    /**
     * @var string type of notation to use for packed keys
     *
     * @see NBaseConverter::$notations
     */
    private $notation = 'legible';

    /**
     * @var integer redundancy level, number of nibbles (half bytes)
     */
    private $redundancy = 0;

    /**
     * @var string salt (applies to encode() and decode(), if redundancy is used)
     */
    private $salt = '';

    /**
     * @param string              $notation  notation to use (available notations are defined by NBaseConverter)
     * @param NBaseConverter|null $converter converter used to pack/unpack values
     */
    public function __construct($notation = 'legible', NBaseConverter $converter = null)
    {
        $this->converter = $converter ?: new NBaseConverter();

        if (! isset($this->converter->notations[$notation])) {
            throw new InvalidArgumentException("undefined notation type: {$notation}");
        }

        $this->notation = $notation;
    }

    /**
     * If you wish to salt packed keys, you should set this to a random sequence of letters
     * and numbers, unique to your application.
     *
     * The `$redundancy` argument specifies a checksum size (in number of half-bytes) seeded by `$salt`,
     * which will be calculated and added into the packed key - the same salt, rendundancy level and
     * notation type will be required to successfully unpack the resulting packed key.
     *
     * Higher redundancy results in longer strings and increased safety against brute-force attacks,
     * for example, a value of 6 gives a redundancy of 2^(4*6) => 16 million.
     *
     * @param int    $redundancy amount of redundancy (in half-bytes)
     * @param string $salt       application-specific secret salt
     */
    public function setRedundancy($redundancy, $salt)
    {
        if ($redundancy > 40 || $redundancy < 0) {
            throw new InvalidArgumentException("invalid redundancy setting: {$redundancy} - valid range is 0..40");
        }

        $this->redundancy = $redundancy;
        $this->salt = $salt;
    }

    /**
     * Internally pack an arbitrary-notation input value to the target notation for packed keys.
     *
     * @param string     $value_notation input value notation type
     * @param int|string $value          an integer (or arbitrary-length integer value in string format)
     *
     * @return string a packed string representation
     *
     * @see unpackValue()
     */
    protected function packValue($value_notation, $value)
    {
        $hex = $this->converter->convert($value, $value_notation, 'hex');

        if ($this->redundancy) {
            $hex .= $this->checksum($hex);
        }

        return $this->converter->convert($hex, 'hex', $this->notation);
    }

    /**
     * Unpack a packed key to a specified abitrary target notation.
     *
     * @param string $value_notation unpacked value notation type
     * @param string $packed_value   a packed ID produced by packValue()
     *
     * @return string|null the unpacked key, in the form of an integer in string format
     *                     (or NULL, if a redundancy check was performed and failed.
     *
     * @see packValue()
     */
    protected function unpackValue($value_notation, $packed_value)
    {
        $hex = $this->converter->convert($packed_value, $this->notation, 'hex');

        if ($this->redundancy) {
            $checksum = substr($hex, -$this->redundancy);

            $hex = substr($hex, 0, -$this->redundancy);

            if ($hex === '') {
                $hex = '0'; // work-around for edge-case: truncated leading zero
            }

            if ($checksum !== $this->checksum($hex)) {
                return null; // checksum failed!
            }
        }

        return $this->converter->convert($hex, 'hex', $value_notation);
    }

    /**
     * @param string $hex string of hex digits
     *
     * @return string checksum of given hex digits
     */
    protected function checksum($hex)
    {
        return substr(sha1($this->salt . ltrim($hex, '0')), 0, $this->redundancy);
    }
}
