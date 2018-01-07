<?php

namespace C0DE8\MatchMaker;

/**
 * Class Matcher
 * @package C0DE8\MatchMaker
 */
class Matcher
{

    /**
     * @param  $value
     * @param  $pattern
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function match($value, $pattern) : bool
    {
        $args = [];

        if (($tmpPattern = \ltrim($pattern, ':')) != $pattern) {

            foreach (\explode(' ', $tmpPattern) as $name) {

                if (\substr($name, -1) === ')') {
                    [$name, $args] = \explode('(', $name);
                    $args          = \explode(',', rtrim($args, ')'));
                }

                $rules = new Rules();

                if (\is_callable($rules->get($name))) {

                    if (!\call_user_func_array($rules->get($name), \array_merge([$value], $args))) {
                        return false;
                    }

                } elseif ($rules->get($name) !== $value) {
                    return false;
                }
            }

            return true;
        }

        return ($pattern === '' || $value === $pattern);
    }

}