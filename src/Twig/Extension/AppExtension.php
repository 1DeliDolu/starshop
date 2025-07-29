<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\AppExtensionRuntime;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{
    
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [AppExtensionRuntime::class, 'doSomething']),
        ];
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_iss_location_data', [AppExtensionRuntime::class, 'getIssLocationData']),
        ];
    }
}
