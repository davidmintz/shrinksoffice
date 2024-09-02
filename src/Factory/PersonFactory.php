<?php

namespace App\Factory;

use App\Entity\Person;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Person>
 */
final class PersonFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return Person::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $fee = self::faker()->boolean(60) ? 25000 :
            self::faker()->randomElement([20000, 15000, 17500]);
        $phone = self::faker()->phoneNumber();
        $stripped = preg_replace('/\D/','',$phone);
        $stripped = preg_replace('/^1/','',$stripped);
        $phone = $stripped;
        return [
            'active' => self::faker()->boolean(80),
            'address1' => self::faker()->streetAddress(),
            'address2' => self::faker()->passthrough(''),
            'email' => self::faker()->email(),
            'fee' => $fee,
            'firstname' => self::faker()->firstName(),
            'lastname' => self::faker()->lastName(),
            'middlename' => '', //strtoupper(self::faker()->boolean(25) ? self::faker()->text(1) : '')),
            'phone' => $phone,
             'city' => self::faker()->city(),
            'postal_code' => self::faker()->postcode(),
            'state' => 'NJ',
            'notes' => self::faker()->boolean(20) ? self::faker()->sentence() : '',
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Person $person): void {})
        ;
    }
}
