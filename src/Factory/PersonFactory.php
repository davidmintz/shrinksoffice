<?php

namespace App\Factory;

use App\Entity\Person;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Faker\Provider\en_US\Address;

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
        $phone =  substr(preg_replace('/\D/','',$phone), -10);
        $first = self::faker()->firstName();
        $last = self::faker()->lastName();
        $alias = substr($first,0,1) . substr($last,0,1);

        return [
            'active' => self::faker()->boolean(80),
            'address' => self::faker()->streetAddress(),
            'secondary_address' => Address::secondaryAddress(),  //self::faker()->secondaryAddress() ,
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
            'alias' => $alias,
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
