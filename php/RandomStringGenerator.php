<?php
/**
	The MIT License (MIT)
	
	Copyright (c) 2015 Ignacio Nieto Carvajal
	
	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
*/

namespace creamy;

/**
 * Class RandomStringGenerator
 * Utility class to generate random strings for tokens/security codes.
 */
class RandomStringGenerator
{
    /** @var string */
    protected $alphabet;

    /** @var int */
    protected $alphabetLength;


    /**
     * @param string $alphabet
     */
    public function __construct($alphabet = '')
    {
        if ('' !== $alphabet) {
            $this->setAlphabet($alphabet);
        } else {
            $this->setAlphabet(
                  implode(range('a', 'z'))
                . implode(range('A', 'Z'))
                . implode(range(0, 9))
            );
        }
    }

    /**
     * @param string $alphabet
     */
    public function setAlphabet($alphabet)
    {
        $this->alphabet = $alphabet;
        $this->alphabetLength = strlen($alphabet);
    }

    /**
     * @param int $length
     * @return string
     */
    public function generate($length)
    {
        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $randomKey = $this->getRandomInteger(0, $this->alphabetLength);
            $token .= $this->alphabet[$randomKey];
        }

        return $token;
    }

    /**
     * @param int $min
     * @param int $max
     * @return int
     */
    public function getRandomInteger($min, $max)
    {
        $range = ($max - $min);

        if ($range < 0) {
            // Not so random...
            return $min;
        }

        $log = log($range, 2);

        // Length in bytes.
        $bytes = (int) ($log / 8) + 1;

        // Length in bits.
        $bits = (int) $log + 1;

        // Set all lower bits to 1.
        $filter = (int) (1 << $bits) - 1;

        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));

            // Discard irrelevant bits.
            $rnd = $rnd & $filter;

        } while ($rnd >= $range);

        return ($min + $rnd);
    }
}