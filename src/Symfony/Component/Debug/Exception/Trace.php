<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug\Exception;

/**
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class Trace implements \ArrayAccess, \IteratorAggregate
{
    private $trace = array();

    /**
     * Constructor.
     *
     * @param array $trace The stack trace
     */
    public function __construct($trace = array())
    {
        $this->trace = $trace;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->trace);
    }

    public function offsetGet($offset)
    {
        return $this->trace[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->trace[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->trace[$offset]);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->trace);
    }

    /**
     * Returns a value for key at position in stack trace.
     *
     * @param int    $position The position in stack trace
     * @param string $key      The key
     * @param mixed  $default  The default value if the entry and key does not exist
     *
     * @return mixed
     */
    public function get($position, $key, $default = null)
    {
        return isset($this->trace[$position]) && array_key_exists($key, $this->trace[$position]) ? $this->trace[$position][$key] : $default;
    }

    /**
     * Sets a value for key at position in stack trace.
     *
     * @param int    $position The position in stack trace
     * @param string $key      The key
     * @param mixed  $value    The value
     */
    public function set($position, $key, $value)
    {
        $this->trace[$position][$key] = $value;
    }

    /**
     * Merges values for key at position in stack trace.
     *
     * @param int    $position The position in stack trace
     * @param string $key      The key
     * @param mixed  $value    The value
     */
    public function merge($position, $key, $value)
    {
        $this->trace[$position][$key] = array_merge((array) $this->get($position, $key), $value);
    }

    public function toArray()
    {
        return $this->trace;
    }
}
