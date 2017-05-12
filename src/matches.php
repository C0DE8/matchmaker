<?php
namespace matchmaker;



/**
 * Returns true if $value matches $pattern
 *
 * @param $value
 * @param $pattern
 *
 * @return bool
 *
 * @see https://github.com/ptrofimov/matchmaker - ultra-fresh PHP matching functions
 * @author Petr Trofimov <petrofimov@yandex.ru>
 * @throws \Exception
 */
function matches($value, $pattern)
{
    \function_exists('\matchmaker\key_matcher') || require __DIR__ . '/key_matcher.php';

    if (is_array($pattern)) {

        // value must be an array OR 'Traversable' instance
        if (!is_array($value) && !$value instanceof \Traversable) {
            throw new \Exception('value is neither an array nor instance of "Traversable"');
        }

        // return a \Closure
        $keyMatcher = key_matcher($pattern);

        foreach ($value as $key => $item) {
            if (!$keyMatcher($key, $item)) {
                return false;
            }
        }

        if (!$keyMatcher()) {
            return false;
        }

    } elseif (!matcher($value, $pattern)) {
        return false;
    }

    return true;
}
