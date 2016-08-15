<?php

use mindplay\keypack\GUIDPacker;
use mindplay\keypack\IntPacker;
use mindplay\keypack\KeyPacker;
use mindplay\keypack\UUIDPacker;

require dirname(__DIR__) . "/vendor/autoload.php";

/**
 * @param KeyPacker|IntPacker|UUIDPacker|GUIDPacker $packer
 * @param mixed[]                                   $values
 */
function test_packer(KeyPacker $packer, $values)
{
    foreach ($values as $value) {
        eq($packer->unpack($packer->pack($value)), $value, "can pack without redundancy");
    }

    foreach ([0, 1, 4] as $redundancy) {
        $packer->setRedundancy($redundancy, 'super secret salt!');

        foreach ($values as $value) {
            eq($packer->unpack($packer->pack($value)), $value, "can pack with {$redundancy} nibbles of redundancy");
        }
    }
}

test(
    'can pack and unpack integer keys',
    function () {
        $packer = new IntPacker();

        test_packer($packer, [0, 1, 12345, 12345678, PHP_INT_MAX]);
    }
);

test(
    'can pack and unpack UUID keys',
    function () {
        $packer = new UUIDPacker();

        test_packer(
            $packer,
            [
                '7eb6de1e-65ef-4fb7-baff-c0732c1c4614',
                '00000000-f883-4edb-88b1-2fbb51efa841', // leading zeroes
                '20c65976-6189-44e9-a8b0-000000000000', // trailing zeroes
                'ffffffff-ffff-ffff-ffff-ffffffffffff',
                '00000000-0000-0000-0000-000000000000',
            ]
        );
    }
);

test(
    'can pack and unpack GUID keys',
    function () {
        $packer = new GUIDPacker();

        test_packer(
            $packer,
            [
                '7EB6DE1E-65EF-4FB7-BAFF-C0732C1C4614',
                '00000000-F883-4EDB-88B1-2FBB51EFA841', // leading zeroes
                '20C65976-6189-44E9-A8B0-000000000000', // trailing zeroes
                'FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF',
                '00000000-0000-0000-0000-000000000000',
            ]
        );
    }
);

exit(run());
