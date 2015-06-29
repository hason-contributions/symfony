<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bridge\Twig\Debug;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\FlattenExceptionProcessorInterface;

/**
 * TwigFlattener adds twig files into FlattenException
 *
 * @author Martin Hasoň <martin.hason@gmail.com>
 */
class TwigFlattenExceptionProcessor implements FlattenExceptionProcessorInterface
{
    private $files = array();
    private $twig;
    private $loadedTemplates;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
        $this->loadedTemplates = new \ReflectionProperty($twig, 'loadedTemplates');
        $this->loadedTemplates->setAccessible(true);
    }

    /**
     * {@inheritdoc}
     */
    public function process(\Exception $exception, FlattenException $flattenException, $master)
    {
        $trace = $flattenException->getTrace();
        $origTrace = $exception->getTrace();

        foreach ($origTrace as $key => $entry) {
            $prevKey = $key - 1;

            if (!isset($origTrace[$prevKey]) || !isset($entry['class']) || 'Twig_Template' === $entry['class'] || !is_subclass_of($entry['class'], 'Twig_Template')) {
                continue;
            }

            $template = $this->getLoadedTemplate($entry['class']);

            if (!$template instanceof \Twig_Template) {
                continue;
            }

            $name = $template->getTemplateName();
            $file = $this->findOrCreateFile($name);

            $data = array('file' => $file);
            if (isset($origTrace[$prevKey]['line'])) {
                $data['line'] = $this->findLineInTemplate($origTrace[$prevKey]['line'], $template);
            }

            $trace->merge($prevKey, 'related_codes', array($data));
        }

        if (isset($trace[-1]) && $exception instanceof \Twig_Error) {
            $name = $exception->getTemplateFile();
            $file = $this->findOrCreateFile($name, $name);

            $trace->merge(-1, 'related_codes', array(array('file' => $file, 'line' => $exception->getTemplateLine())));
        }
    }

    private function getLoadedTemplate($class)
    {
        $loadedTemplates = $this->loadedTemplates->getValue($this->twig);

        return isset($loadedTemplates[$class]) ? $loadedTemplates[$class] : null;
    }

    private function findOrCreateFile($name, $path = null)
    {
        if (isset($this->files[$name])) {
            return $this->files[$name];
        }

        foreach ($this->files as $key => $file) {
            if (isset($file->path) && $file->path == $name) {
                return $file;
            }
        }

        $file = (object) array('name' => $name, 'type' => 'twig');

        $path = $path ?: $this->twig->getLoader()->getCacheKey($name);
        if (is_file($path)) {
            $file->path = $path;
        } else {
            try {
                $file->content = $this->twig->getLoader()->getSource($name);
            } catch (\Twig_Error_Loader $e) {
            }
        }

        return $this->files[$name] = $file;
    }

    private function findLineInTemplate($line, $template)
    {
        foreach ($template->getDebugInfo() as $codeLine => $templateLine) {
            if ($codeLine <= $line) {
                return $templateLine;
            }
        }

        return 1;
    }
}
