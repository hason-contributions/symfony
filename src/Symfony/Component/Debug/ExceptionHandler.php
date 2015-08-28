<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Debug;

use Symfony\Component\Debug\Utils\HtmlUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\Exception\OutOfMemoryException;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * ExceptionHandler converts an exception to a Response object.
 *
 * It is mostly useful in debug mode to replace the default PHP/XDebug
 * output with something prettier and more useful.
 *
 * As this class is mainly used during Kernel boot, where nothing is yet
 * available, the Response content is always HTML.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ExceptionHandler
{
    private $debug;
    private $charset;
    private $handler;
    private $caughtBuffer;
    private $caughtLength;
    private $fileLinkFormat;
    private $flattener;

    public function __construct($debug = true, $charset = null, $fileLinkFormat = null, ExceptionFlattener $flattener = null)
    {
        if (false !== strpos($charset, '%')) {
            // Swap $charset and $fileLinkFormat for BC reasons
            $pivot = $fileLinkFormat;
            $fileLinkFormat = $charset;
            $charset = $pivot;
        }
        $this->debug = $debug;
        $this->charset = $charset ?: ini_get('default_charset') ?: 'UTF-8';
        $this->fileLinkFormat = $fileLinkFormat ?: ini_get('xdebug.file_link_format') ?: get_cfg_var('xdebug.file_link_format');
        $this->flattener = $flattener;
    }

    /**
     * Registers the exception handler.
     *
     * @param bool        $debug          Enable/disable debug mode, where the stack trace is displayed
     * @param string|null $charset        The charset used by exception messages
     * @param string|null $fileLinkFormat The IDE link template
     *
     * @return ExceptionHandler The registered exception handler
     */
    public static function register($debug = true, $charset = null, $fileLinkFormat = null)
    {
        $handler = new static($debug, $charset, $fileLinkFormat);

        $prev = set_exception_handler(array($handler, 'handle'));
        if (is_array($prev) && $prev[0] instanceof ErrorHandler) {
            restore_exception_handler();
            $prev[0]->setExceptionHandler(array($handler, 'handle'));
        }

        return $handler;
    }

    /**
     * Sets a user exception handler.
     *
     * @param callable $handler An handler that will be called on Exception
     *
     * @return callable|null The previous exception handler if any
     */
    public function setHandler($handler)
    {
        if (null !== $handler && !is_callable($handler)) {
            throw new \LogicException('The exception handler must be a valid PHP callable.');
        }
        $old = $this->handler;
        $this->handler = $handler;

        return $old;
    }

    /**
     * Sets the format for links to source files.
     *
     * @param string $format The format for links to source files
     *
     * @return string The previous file link format.
     */
    public function setFileLinkFormat($format)
    {
        $old = $this->fileLinkFormat;
        $this->fileLinkFormat = $format;

        return $old;
    }

    /**
     * Sends a response for the given Exception.
     *
     * To be as fail-safe as possible, the exception is first handled
     * by our simple exception handler, then by the user exception handler.
     * The latter takes precedence and any output from the former is cancelled,
     * if and only if nothing bad happens in this handling path.
     */
    public function handle(\Exception $exception)
    {
        if (null === $this->handler || $exception instanceof OutOfMemoryException) {
            $this->failSafeHandle($exception);

            return;
        }

        $caughtLength = $this->caughtLength = 0;

        ob_start(array($this, 'catchOutput'));
        $this->failSafeHandle($exception);
        while (null === $this->caughtBuffer && ob_end_flush()) {
            // Empty loop, everything is in the condition
        }
        if (isset($this->caughtBuffer[0])) {
            ob_start(array($this, 'cleanOutput'));
            echo $this->caughtBuffer;
            $caughtLength = ob_get_length();
        }
        $this->caughtBuffer = null;

        try {
            call_user_func($this->handler, $exception);
            $this->caughtLength = $caughtLength;
        } catch (\Exception $e) {
            if (!$caughtLength) {
                // All handlers failed. Let PHP handle that now.
                throw $exception;
            }
        }
    }

    /**
     * Sends a response for the given Exception.
     *
     * If you have the Symfony HttpFoundation component installed,
     * this method will use it to create and send the response. If not,
     * it will fallback to plain PHP functions.
     *
     * @param \Exception $exception An \Exception instance
     */
    private function failSafeHandle(\Exception $exception)
    {
        if (class_exists('Symfony\Component\HttpFoundation\Response', false)
            && __CLASS__ !== get_class($this)
            && ($reflector = new \ReflectionMethod($this, 'createResponse'))
            && __CLASS__ !== $reflector->class
        ) {
            $response = $this->createResponse($exception);
            $response->sendHeaders();
            $response->sendContent();
            @trigger_error(sprintf("The %s::createResponse method is deprecated since 2.8 and won't be called anymore when handling an exception in 3.0.", $reflector->class), E_USER_DEPRECATED);

            return;
        }

        $this->sendPhpResponse($exception);
    }

    /**
     * Sends the error associated with the given Exception as a plain PHP response.
     *
     * This method uses plain PHP functions like header() and echo to output
     * the response.
     *
     * @param \Exception|FlattenException $exception An \Exception instance
     */
    public function sendPhpResponse($exception)
    {
        if (!$exception instanceof FlattenException) {
            $exception = null !== $this->flattener ? $this->flattener->flatten($exception) : FlattenException::create($exception);
        }

        if (!headers_sent()) {
            header(sprintf('HTTP/1.0 %s', $exception->getStatusCode()));
            foreach ($exception->getHeaders() as $name => $value) {
                header($name.': '.$value, false);
            }
            header('Content-Type: text/html; charset='.$this->charset);
        }

        echo $this->decorate($this->getContent($exception), $this->getStylesheet($exception));
    }

    /**
     * Creates the error Response associated with the given Exception.
     *
     * @param \Exception|FlattenException $exception An \Exception instance
     *
     * @return Response A Response instance
     *
     * @deprecated since 2.8, to be removed in 3.0.
     */
    public function createResponse($exception)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 2.8 and will be removed in 3.0.', E_USER_DEPRECATED);

        if (!$exception instanceof FlattenException) {
            $exception = null !== $this->flattener ? $this->flattener->flatten($exception) : FlattenException::create($exception);
        }

        return Response::create($this->decorate($this->getContent($exception), $this->getStylesheet($exception)), $exception->getStatusCode(), $exception->getHeaders())->setCharset($this->charset);
    }

    /**
     * Gets the HTML content associated with the given exception.
     *
     * @param FlattenException $exception A FlattenException instance
     *
     * @return string The content as a string
     */
    public function getContent(FlattenException $exception)
    {
        switch ($exception->getStatusCode()) {
            case 404:
                $title = 'Sorry, the page you are looking for could not be found.';
                break;
            default:
                $title = 'Whoops, looks like something went wrong.';
        }

        $content = '';
        if ($this->debug) {
            try {
                $count = count($exception->getAllPrevious());
                $total = $count + 1;
                foreach ($exception->toArray() as $position => $e) {
                    $ind = $count - $position + 1;
                    $class = HtmlUtils::abbrClass($e['class']);
                    $message = nl2br($this->escapeHtml($e['message']));
                    $content .= sprintf(<<<EOF
                        <h2 class="block_exception clear_fix">
                            <span class="exception_counter">%d/%d</span>
                            <span class="exception_title">%s%s:</span>
                            <span class="exception_message">%s</span>
                        </h2>
                        <div class="block">
                            <ol reversed="reversed" class="traces list_exception">

EOF
                        , $ind, $total, $class, $this->formatPath($e['trace'][0]['file'], $e['trace'][0]['line']), $message);
                    foreach ($e['trace'] as $trace) {
                        $content .= '       <li>';
                        if ($trace['function']) {
                            $content .= sprintf('%s%s%s(%s)', HtmlUtils::abbrClass($trace['class']), $trace['type'], $trace['function'], $this->formatArgs($trace['args']));
                        }
                        if (isset($trace['file']) && isset($trace['line'])) {
                            $content .= '<br>'.$this->formatPath($trace['file'], $trace['line']);
                        }
                        $content .= "</li>\n";
                    }

                    $content .= "    </ol>\n</div>\n";
                }
            } catch (\Exception $e) {
                // something nasty happened and we cannot throw an exception anymore
                if ($this->debug) {
                    $title = sprintf('Exception thrown when handling an exception (%s: %s)', get_class($e), $this->escapeHtml($e->getMessage()));
                } else {
                    $title = 'Whoops, looks like something went wrong.';
                }
            }
        }

        return <<<EOF
            <div id="sf-exception-content" class="sf-exception">
                <h1>$title</h1>
                $content
            </div>
EOF;
    }

    /**
     * Gets the stylesheet associated with the given exception.
     *
     * @param FlattenException $exception A FlattenException instance
     *
     * @return string The stylesheet as a string
     */
    public function getStylesheet(FlattenException $exception)
    {
        return file_get_contents(__DIR__.'/Resources/views/exception.css');
    }

    private function decorate($content, $css)
    {
        return strtr(file_get_contents(__DIR__.'/Resources/views/exception_full.html'), array(
            '{{ charset }}' => $this->charset,
            '{{ content }}' => $content,
            '{{ css }}' => $css,
        ));
    }

    /**
     * Returns an UTF-8 and HTML encoded string.
     *
     * @deprecated since version 2.7, to be removed in 3.0.
     */
    protected static function utf8Htmlize($str)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 2.7 and will be removed in 3.0.', E_USER_DEPRECATED);

        return htmlspecialchars($str, ENT_QUOTES | (PHP_VERSION_ID >= 50400 ? ENT_SUBSTITUTE : 0), 'UTF-8');
    }

    /**
     * HTML-encodes a string.
     */
    private function escapeHtml($str)
    {
        return htmlspecialchars($str, ENT_QUOTES | (PHP_VERSION_ID >= 50400 ? ENT_SUBSTITUTE : 0), $this->charset);
    }

    /**
     * @internal
     */
    public function catchOutput($buffer)
    {
        $this->caughtBuffer = $buffer;

        return '';
    }

    /**
     * @internal
     */
    public function cleanOutput($buffer)
    {
        if ($this->caughtLength) {
            // use substr_replace() instead of substr() for mbstring overloading resistance
            $cleanBuffer = substr_replace($buffer, '', 0, $this->caughtLength);
            if (isset($cleanBuffer[0])) {
                $buffer = $cleanBuffer;
            }
        }

        return $buffer;
    }
}
