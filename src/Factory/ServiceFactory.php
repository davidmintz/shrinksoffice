<?php

namespace App\Factory;

use App\Entity\Service;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Service>
 */
final class ServiceFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {

    }


    public static array $times = [
        'Mon 900',
        'Mon 1000',
        'Mon 1300',
        'Mon 1400',
        'Mon 1600',
        'Tue 1000',
        'Tue 1130',
        'Tue 1400',
        'Tue 1500',
        'Wed 800',
        'Wed 1000',
        'Wed 1100',
        'Wed 1300',
        'Wed 1700',
        'Thu 830',
        'Thu 1000',
        'Thu 1200',
        'Thu 1600',
        'Thu 1730',
        'Fri 1000',
        'Fri 1100',
        'Fri 1200',
        'Fri 1500',
        'Fri 1600',
        'Sat 900',
        'Sat 1000',
        //'Sat 1100',



    ];


    public static function class(): string
    {
        return Service::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'cancelled' => self::faker()->boolean(90),
            'date' => null,//self::faker()->dateTime(),
            'duration' => 45, //self::faker()->numberBetween(1, 32767),
            'fee' => null,self::faker()->numberBetween(1, 32767),
            'notes' => '',self::faker()->text(300),
            'time' => null, // TODO add TIME type manually
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Service $service): void {})
        ;
    }
}
