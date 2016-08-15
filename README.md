mindplay/keypack
================

This library packs integer, UUID and GUID keys to shorter strings, e.g. for use in user-facing URLs.

[![PHP Version](https://img.shields.io/badge/php-5.4%2B-blue.svg)](https://packagist.org/packages/mindplay/keypack)
[![Build Status](https://travis-ci.org/mindplay-dk/keypack.svg?branch=master)](https://travis-ci.org/mindplay-dk/keypack)

#### Usage

Three classes, `IntPacker`, `UUIDPacker` and `GUIDPacker` are provided, each capable of packing/unpacking
a particular type of ID. Each class has essentially the same interface, but accepting/returning different
types of keys.

Here's an example of packing/unpacking a UUID to a shorter string:

```php
$packer = new UUIDPacker();

echo $packer->pack("7eb6de1e-65ef-4fb7-baff-c0732c1c4614"); // => "py6dWN6dgKR8cGVz73zDiT"

echo $packer->unpack("py6dWN6dgKR8cGVz73zDiT"); // => "7eb6de1e-65ef-4fb7-baff-c0732c1c4614" 
```

You can pack keys using different notations. The default is `legible`, which produces human-readable
and reasonably short keys - [see here for more options](https://github.com/mindplay-dk/nbase). 

You can also add redundancy to packed keys, to prevent typos make it harder to guess a packed key.

Here's an example packing an integer key to `base64` with 4 bytes of redundancy:

```php
$packer = new IntPacker('base64');

$packer->setRedundancy(4, 'super secret salt');

echo $packer->pack(123456); // => "7yg6HR"

echo $packer->unpack("7yg6HR"); // => (int) 123456
```

Note that `unpack()` will return `null` if the redundancy check fails.
