<?php

namespace C0DE8\MatchMaker;

/**
 * Class Matcher
 * @package C0DE8\MatchMaker
 */
class Matcher
{

    /**
     * @param $value
     * @param $pattern
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function match($value, $pattern)
    {
//        echo  "\n".__CLASS__ . "\n";
//        var_dump('$value = ' . var_export($value, true));
//        var_dump('$pattern = ' . var_export($pattern, true));

        $args = [];

        if (($p = \ltrim($pattern, ':')) != $pattern) {

            foreach (\explode(' ', $p) as $name) {

                if (\substr($name, -1) === ')') {
                    list($name, $args) = explode('(', $name);
                    $args = \explode(',', rtrim($args, ')'));
                }


                if (\is_callable((new Rules)->get($name))) {

                    if (!\call_user_func_array((new Rules)->get($name), \array_merge([$value], $args))) {
                        return false;
                    }

                } elseif ((new Rules)->get($name) !== $value) {
                    return false;
                }
            }
        } else {
            return $pattern === '' || $value === $pattern;
        }

        return true;
    }

}