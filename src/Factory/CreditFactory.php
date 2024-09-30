<?php

namespace App\Factory;

use App\Entity\Credit;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Credit>
 */
final class CreditFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Credit::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'amount' => 0, //self::faker()->numberBetween(1, 32767),
            'date' => null, //self::faker()->dateTime(),
            'notes' => '', //self::faker()->text(255),
            'payer' => null,//PersonFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Credit $credit): void {})
        ;
    }


    public static function maybe(int $chance = 80) : bool
    {
        return self::faker()->boolean($chance);
    }
}
