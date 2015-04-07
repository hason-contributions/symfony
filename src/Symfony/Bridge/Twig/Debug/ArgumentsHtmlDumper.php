<?php

namespace Symfony\Bridge\Twig\Debug;

use Symfony\Component\VarDumper\Cloner\Cursor;
use Symfony\Component\VarDumper\Cloner\Stub;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class ArgumentsHtmlDumper extends HtmlDumper
{
    public function enterHash(Cursor $cursor, $type, $class, $hasChild)
    {
        if (0 === $cursor->depth && Stub::ARRAY_ASSOC === $type) {
            return;
        }

        $this->enterDump($cursor);

        parent::enterHash($cursor, $type, $class, $hasChild);
    }

    public function leaveHash(Cursor $cursor, $type, $class, $hasChild, $cut)
    {
        if (0 === $cursor->depth && Stub::ARRAY_ASSOC === $type) {
            return;
        }

        parent::leaveHash($cursor, $type, $class, $hasChild, $cut);

        $this->leaveDump($cursor);
    }

    public function dumpString(Cursor $cursor, $str, $bin, $cut)
    {
        $this->enterDump($cursor);

        parent::dumpString($cursor, $str, $bin, $cut);

        $this->leaveDump($cursor);
    }

    public function dumpScalar(Cursor $cursor, $type, $value)
    {
        $this->enterDump($cursor);

        parent::dumpScalar($cursor, $type, $value);

        $this->leaveDump($cursor);
    }

    protected function dumpKey(Cursor $cursor)
    {
        if (1 === $cursor->depth && Stub::ARRAY_ASSOC === $cursor->hashType) {
            return;
        }

        parent::dumpKey($cursor);
    }

    private function enterDump(Cursor $cursor)
    {
        if (1 === $cursor->depth && Stub::ARRAY_ASSOC === $cursor->hashType) {
            $this->line .= sprintf('<span id="sf-arg-%s">', $cursor->hashKey);
            $this->dumpLine($cursor->depth);
        }
    }

    private function leaveDump(Cursor $cursor)
    {
        if (1 === $cursor->depth && Stub::ARRAY_ASSOC === $cursor->hashType) {
            $this->line .= sprintf('</span>');
            $this->dumpLine($cursor->depth);
        }
    }
}
