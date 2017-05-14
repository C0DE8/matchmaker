<?php

namespace C0DE8\MatchMaker;

use C0DE8\MatchMaker\Exception\InvalidValueTypeException;
use C0DE8\MatchMaker\Exception\KeyMatcherFailException;
use C0DE8\MatchMaker\Exception\KeyMatchFailException;
use C0DE8\MatchMaker\Exception\MatcherException;

/**
 * Class MatchMaker
 * @package C0DE8\Matchmaker
 */
class Manager
{

    /**
     * @param  mixed $value
     * @param  mixed $pattern
     * @return bool
     * @throws InvalidValueTypeException
     * @throws KeyMatcherFailException
     * @throws KeyMatchFailException
     * @throws MatcherException
     * @throws \InvalidArgumentException
     */
    public function matchAgainst($value, $pattern) : bool
    {
        if (is_array($pattern)) {

            // value must be an array OR 'Traversable' instance
            if (!is_array($value) && !$value instanceof \Traversable) {
                throw new InvalidValueTypeException(
                    'value is neither an array nor instance of "Traversable"'
                );
            }

            // get the key matcher closure
            $keyMatcher = (new KeyMatcher())->get($pattern);

            /** @var array  $value **/
            /** @var string $key   **/
            /** @var mixed  $item  **/
            foreach ($value as $key => $item) {
                if (!$keyMatcher($key, $item)) {
                    throw new KeyMatcherFailException(
                        '$keyMatcher FAIL by key [' . $key . '] => item ['
                        . \var_export($item, true) . ' ]'
                    );
                }
            }

            if (!$keyMatcher()) {
                throw new KeyMatcherFailException(
                    '$keyMatcher() call FAIL (returned FALSE) {possibly wrong count}'
                );
            }

        } elseif (!(new Matcher)->match($value, $pattern)) {

            $valueType = \gettype($value);

            if (\is_array($value)) {
                $value = \json_encode($value);
            }

            if (\is_object($value)) {
                $value = \get_class($value);
            }

            throw new MatcherException(
                'Matcher FAIL by value [' . $value . '] (' . $valueType . ') =>'
              . ' [expected pattern/type: '
              . \var_export($pattern, true) . ']'
            );
        }

        return true;
    }

}
