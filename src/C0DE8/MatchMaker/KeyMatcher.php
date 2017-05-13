<?php

namespace C0DE8\MatchMaker;

use C0DE8\MatchMaker\Exception\InvalidValueTypeException;
use C0DE8\MatchMaker\Exception\KeyMatcherFailException;
use C0DE8\MatchMaker\Exception\KeyMatchFailException;
use C0DE8\MatchMaker\Exception\MatcherException;

/**
 * Class KeyMatcher
 * @package C0DE8\MatchMaker
 */
class KeyMatcher
{

    /**
     * @param  array $pattern
     * @return \Closure
     * @throws \InvalidArgumentException
     * @throws InvalidValueTypeException
     * @throws KeyMatcherFailException
     * @throws KeyMatchFailException
     * @throws MatcherException
     * @throws \InvalidArgumentException
     */
    public function get(array $pattern) : \Closure
    {
        $keys = [];

        foreach ($pattern as $patternKey => $patternValue) {

            $chars = [
                '?' => [0,           1], // optional (but max 1)
                '*' => [0, PHP_INT_MAX], // optional (0 ... PHP_INT_MAX)
                '!' => [1,           1]  // mandatory (exact 1)
            ];

            // is last char in $patternKey on of the modifier keys (?, *, !)
            // implicit $last value assign og last char
            if (isset($chars[$last = \substr($patternKey, -1)])) {

                // assign string without last char to $patternKey
                // and corresponding array by $last from $chars
                $keys[$patternKey = \substr($patternKey, 0, -1)] = $chars[$last];

                // on "}" we have to find the opening "{" for a specific
                // range count of the patternKey
            } elseif ($last === '}') {

                list($patternKey, $range) = \explode('{', $patternKey);

                $range             = \explode(',', rtrim($range, '}'));
                $keys[$patternKey] = (\count($range) === 1)
                                        ? [$range[0], $range[0]]
                                        : [
                                            $range[0] === '' ? 0 : $range[0],
                                            $range[1] === '' ? PHP_INT_MAX : $range[1]
                                          ];

            } else {
                $keys[$patternKey] = $chars[$patternKey[0] === ':' ? '*' : '!'];
            }

            $keys[$patternKey][] = $patternValue; // index 2 (3rd element)
            $keys[$patternKey][] = 0;             // index 3 (4th element)
        }

        // return the recursive \Closure
        return function ($key = null, $value = null) use (&$keys)
        {
            if (null === $key) {

                foreach ($keys as $count) {
                    if ($count[3] < $count[0] || $count[3] > $count[1]) {
                        return false;
                    }
                }

            } else {

                foreach ($keys as $k => &$count) {

                    if ((new Matcher)->match($key, $k)) {

                        if (! (new Manager())->matchAgainst($value, $count[2]) ) {
                            return false;
                        }
                        $count[3]++;
                    }
                }
            }

            return true;
        };
    }

}
