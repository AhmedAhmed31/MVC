<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Dumper;

use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Component\Translation\Exception\RuntimeException;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * FileDumper is an implementation of DumperInterface that dump a message catalogue to file(s).
 *
 * Options:
 * - path (mandatory): the directory where the files should be saved
 *
 * @author Michel Salib <michelsalib@hotmail.com>
 */
abstract class FileDumper implements DumperInterface
{
    /**
     * A template for the relative paths to files.
     *
     * @var string
     */
    protected $relativePathTemplate = '%domain%.%locale%.%extension%';

    /**
     * Sets the template for the relative paths to files.
     *
     * @param string $relativePathTemplate A template for the relative paths to files
     */
    public function setRelativePathTemplate(string $relativePathTemplate)
    {
        $this->relativePathTemplate = $relativePathTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function dump(MessageCatalogue $messages, array $options = [])
    {
        if (!\array_key_exists('path', $options)) {
            throw new InvalidArgumentException('The file dumper needs a path option.');
        }

        $hasMessageFormatter = class_exists(\MessageFormatter::class);

        // save a file for each domain
        foreach ($messages->getDomains() as $domain) {
            if ($hasMessageFormatter) {
                $defaultDomain = $domain . MessageCatalogue::INTL_DOMAIN_SUFFIX;
                $altDomain = $domain;
            } else {
                $defaultDomain = $domain;
                $altDomain = $domain . MessageCatalogue::INTL_DOMAIN_SUFFIX;
            }
            $defaultPath = $options['path'] . '/' . $this->getRelativePath($defaultDomain, $messages->getLocale());
            $altPath = $options['path'] . '/' . $this->getRelativePath($altDomain, $messages->getLocale());

            if (!file_exists($defaultPath) && file_exists($altPath)) {
                [$defaultPath, $altPath] = [$altPath, $defaultPath];
            }

            if (!file_exists($defaultPath)) {
                $directory = \dirname($defaultPath);
                if (!file_exists($directory) && !@mkdir($directory, 0777, true)) {
                    throw new RuntimeException(sprintf('Unable to create directory "%s".', $directory));
                }
            }

            if (file_exists($altPath)) {
                // clear alternative translation file
                file_put_contents($altPath, $this->formatCatalogue(new MessageCatalogue($messages->getLocale()), $altDomain, $options));
            }

            file_put_contents($defaultPath, $this->formatCatalogue($messages, $domain, $options));
        }
    }

    /**
     * Gets the relative file path using the template.
     */
    private function getRelativePath(string $domain, string $locale): string
    {
        return strtr($this->relativePathTemplate, [
            '%domain%' => $domain,
            '%locale%' => $locale,
            '%extension%' => $this->getExtension(),
        ]);
    }

    /**
     * Gets the file extension of the dumper.
     *
     * @return string file extension
     */
    abstract protected function getExtension();

    /**
     * Transforms a domain of a message catalogue to its string representation.
     *
     * @return string representation
     */
    abstract public function formatCatalogue(MessageCatalogue $messages, string $domain, array $options = []);
}
