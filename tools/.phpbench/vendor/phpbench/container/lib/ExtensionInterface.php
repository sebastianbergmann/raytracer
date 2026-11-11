<?php

/*
 * This file is part of the PHPBench package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpBench\DependencyInjection;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface ExtensionInterface
{
    /**
     * Register services with the container.
     *
     * @param Container $container
     */
    public function load(Container $container): void;

    /**
     * Configure the parameters which can be accessed by the extension.
     */
    public function configure(OptionsResolver $resolver): void;
}
