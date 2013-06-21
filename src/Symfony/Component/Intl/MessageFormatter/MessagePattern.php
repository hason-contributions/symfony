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
 * Parses and represents ICU MessageFormat patterns.
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class MessagePattern
{
    /**
     * A literal apostrophe must be represented by a double apostrophe pattern
     * character. A single apostrophe always starts quoted literal text.
     *
     * This is the behavior of ICU 4.6 and earlier.
     */
    const APOSTROPHE_DOUBLE_REQUIRED = 1;

    /**
     * A literal apostrophe is represented by either a single or a double
     * apostrophe pattern character.
     *
     * Within a MessageFormat pattern, a single apostrophe only starts quoted
     * literal text if it immediately precedes a curly brace {}, or a pipe
     * symbol | if inside a choice format, or a pound symbol # if inside a plural format.
     *
     * This is the default behavior starting with ICU 4.8.
     */
    const APOSTROPHE_DOUBLE_OPTIONAL = 2;

    const STATE_TEXT         = 0;
    const STATE_ARGUMENT     = 1;

    const REGEX_NUMBERED_ARGUMENT = '/(0|[1-9][0-9]*)/';
    const REGEX_NAMED_ARGUMENT    = '/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/A';
    const REGEX_ARGUMENT_TYPE     = '/(choice|plural|select|selectordinal|number|date|time|spellout|ordinal|duration)/';
    const REGEX_ARGUMENT_STYLE    = '/(short|medium|long|full|integer|currency|percent|)/';
    const REGEX_KEYWORD           = '';
    const REGEX_PLURAL_OFFSET     = '/offset:\s*(\d+)/';
    const REGEXP_PLURAL_SELECTOR  = '/(?:=\d+|)/';

    private $apostropheMode = self::APOSTROPHE_DOUBLE_OPTIONAL;

    /**
     * Parses a MessageFormat pattern string.
     *
     * @param string $pattern The MessageFormatter patter string.
     *
     * @return MessagePattern
     *
     * @throws IllegalArgumentException for syntax errors in the pattern string
     * @throws IndexOutOfBoundsException if certain limits are exceeded
     *         (e.g., argument number too high, argument name too long, etc.)
     * @throws NumberFormatException if a number could not be parsed
     */
    public function parser($pattern)
    {
        $this->pattern = str_replace(array("\r\n", "\r"), "\n", $pattern);
        $this->cursor = 0;
        $this->lineno = 1;
        $this->tokens = array();

        while ($this->cursor < $this->end) {

        }

        return $this;
    }

    private function parseArgument()
    {

    }

    private function parseChoiceStyle()
    {

    }

    private function parsePluralStyle()
    {

    }

    private function parseSelectStyle()
    {

    }
}