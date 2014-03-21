IfmbBundle
==========

The `IfmbBundle` provides integration of [ifthensoftware's Multibanco Payments](https://www.ifthensoftware.com/ProdutoX.aspx?ProdID=5) library into the [Symfony2 framework](http://symfony.com).


Installation
------------

Require [`gordalina/ifmb-bundle`](https://packagist.org/packages/gordalina/ifmb-bundle)
to your `composer.json` file:


```json
{
    "require": {
        "gordalina/ifmb-bundle": "~1"
    }
}
```


Register the bundle in `app/AppKernel.php`:

```php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new Gordalina\Bundle\IfmbBundle\GordalinaIfmbBundle(),
    );
}
```

Enable the bundle's configuration in `app/config/config.yml`:

``` yaml
# app/config/config.yml
gordalina_ifmb:
    anti_phishing_key: 0000-0000-0000-0000
    backoffice_key:    15i6cnl28vj4ock84co0gssggo480cso8oo4wok8oso8c0w4s8
    sandbox:           false # it defaults to %kernel.debug%
```


Configure the bundle's routing in `app/config/routing.yml`:

``` yaml
# app/config/routing.yml
gordalina_ifmb:
    resource: "@GordalinaIfmbBundle/Resources/config/routing.yml"
    prefix:   /<your prefix>
```

The callback URL that you need to provide to ifthensoftware will be your
`http://domain.com/<your prefix>/ifmb/payment-notification`

Usage
-----

This bundle registers a `gordalina_ifmb.refmb` service which is an instance
of `RefMb`. You'll be able to generate Multibanco References from this service.

It also registers a `gordalina_ifmb.client` service which is an instance of
`Client` you can use this service to fetch payments from the gateway.


Testing
-------

Setup the test suite using [Composer](http://getcomposer.org/):

    $ composer install --dev

Run it using PHPUnit:

    $ vendor/bin/phpunit
