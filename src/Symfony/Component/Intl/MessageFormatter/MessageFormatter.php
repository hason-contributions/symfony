<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Intl\MessageFormatter;

use Symfony\Component\Intl\Globals\IntlGlobals;
use Symfony\Component\Intl\Exception\MethodArgumentValueNotImplementedException;

/**
 * Replacement for PHP's native {@link \MessageFormatter} class.
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class MessageFormatter
{
    /**
     * The locale to use when formatting arguments
     *
     * @var string
     */
    protected $locale;

    /**
     * The pattern string to stick arguments into
     *
     * @var string
     */
    protected $pattern;

    /**
     * The error code from the last operation
     *
     * @var integer
     */
    protected $errorCode = IntlGlobals::U_ZERO_ERROR;

    /**
     * The error message from the last operation
     *
     * @var string
     */
    protected $errorMessage = 'U_ZERO_ERROR';

    /**
     * Construktor.
     *
     * @param string $locale  The locale to use when formatting arguments.
     * @param string $pattern The pattern string to stick arguments into.The pattern uses an 'apostrophe-friendly' syntax.
     *
     * @see http://www.php.net/manual/en/messageformatter.create.php
     *
     * @throws MethodArgumentValueNotImplementedException  When $locale different than "en" is passed
     */
    public function __construct($locale, $pattern)
    {
        if ('en' !== $locale) {
            throw new MethodArgumentValueNotImplementedException(__METHOD__, 'locale', $locale, 'Only the locale "en" is supported');
        }

        $this->locale = $locale;
        $this->pattern = $pattern;
    }

    /**
     * Constructs a new Message Formatter.
     *
     * @param string $locale  The locale to use when formatting arguments
     * @param string $pattern The pattern string to stick arguments into. The pattern uses an 'apostrophe-friendly' syntax.
     *
     * @return MessageFormatter
     *
     * @see http://www.php.net/manual/en/messageformatter.create.php
     *
     * @throws MethodArgumentValueNotImplementedException  When $locale different than "en" is passed
     */
    public static function create($locale, $pattern)
    {
        return new static($locale, $pattern);
    }

    /**
     * Quick format message.
     *
     * @param string $locale  The locale to use for formatting locale-dependent parts.
     * @param string $pattern The pattern string to insert things into. The pattern uses an 'apostrophe-friendly' syntax.
     * @param array $args     The array of values to insert into the format string.
     *
     * @return string|Boolean The formatted pattern string or FALSE if an error occurred
     *
     * @see http://www.php.net/manual/en/messageformatter.formatmessage.php
     *
     * @throws MethodArgumentValueNotImplementedException  When $locale different than "en" is passed
     */
    public static function formatMessage($locale, $pattern, array $args)
    {
        return static::create($locale, $pattern)->format($args);
    }

    /**
     * Format the message.
     *
     * @param array $args Arguments to insert into the format string
     *
     * @return string|Boolean The formatted pattern string or FALSE if an error occurred
     *
     * @see http://www.php.net/manual/en/messageformatter.format.php
     */
    public function format(array $args)
    {
        // @todo
    }

    /**
     * Get the error code from last operation.
     *
     * @return integer The error code, one of UErrorCode values. Initial value is U_ZERO_ERROR.
     *
     * @see http://www.php.net/manual/en/messageformatter.geterrorcode.php
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Get the error text from the last operation.
     *
     * @return string Description of the last error.
     *
     * @see http://www.php.net/manual/en/messageformatter.geterrormessage.php
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Get the locale for which the formatter was created.
     *
     * @return string The locale name.
     *
     * @see http://www.php.net/manual/en/messageformatter.getlocale.php
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Get the pattern used by the formatter
     *
     * @return string The pattern string for this message formatter.
     *
     * @see http://www.php.net/manual/en/messageformatter.getpattern.php
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Quick parse input string.
     *
     * @param string $locale  The locale to use for parsing locale-dependent parts.
     * @param string $pattern The pattern with which to parse the value.
     * @param string $source  The string to parse, conforming to the pattern.
     *
     * @return array|Boolean An array containing items extracted, or FALSE on error.
     *
     * @see http://www.php.net/manual/en/messageformatter.parsemessage.php
     *
     * @throws MethodArgumentValueNotImplementedException  When $locale different than "en" is passed
     */
    public static function parseMessage($locale, $pattern, $source)
    {
        return static::create($locale, $pattern)->parse($source);
    }

    /**
     * Parse input string according to pattern.
     *
     * @param string $value The string to parse.
     *
     * @return array|Boolean An array containing the items extracted, or FALSE on error.
     *
     * @see http://www.php.net/manual/en/messageformatter.parse.php
     */
    public function parse($value)
    {
        // @todo
    }

    /**
     * Set the pattern used by the formatter.
     *
     * @param string $pattern The pattern string to use in this message formatter. The pattern uses an 'apostrophe-friendly' syntax.
     *
     * @return Boolean Returns TRUE on success or FALSE on failure.
     *
     * @see http://www.php.net/manual/en/messageformatter.setpattern.php
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return true;
    }
}