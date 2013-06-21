<?php
namespace Symfony\Component\Intl\MessageFormatter;

class MessagePatternLexer
{
    const STATE_TEXT         = 0;
    const STATE_ARGUMENT     = 1;
    const STATE_VAR             = 2;
    const STATE_STRING          = 3;
    const STATE_INTERPOLATION   = 4;

    const REGEX_NUMBERED_ARGUMENT = '/(0|[1-9][0-9]*)/';
    const REGEX_NAMED_ARGUMENT    = '/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/A';
    const REGEX_ARGUMENT_TYPE     = '/(choice|plural|select|selectordinal|number|date|time|spellout|ordinal|duration)/';
    const REGEX_ARGUMENT_STYLE    = '/(short|medium|long|full|integer|currency|percent|)/';
    const REGEX_KEYWORD           = '';
    const REGEX_PLURAL_OFFSET     = '/offset:\s*(\d+)/';
    const REGEXP_PLURAL_SELECTOR  = '/(?:=\d+|)/';

    public function tokenize($pattern)
    {
        $this->pattenr = str_replace(array("\r\n", "\r"), "\n", $pattern);
        $this->cursor = 0;
        $this->lineno = 1;
        $this->tokens = array();

        while ($this->cursor < $this->end) {

        }
    }
}