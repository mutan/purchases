1. `git clone https://github.com/mutan/rpg-helper`
2. `cd rpg-helper`
3. `composer install`
4. `yarn`


* `php bin/console doctrine:database:drop --force`
* `php bin/console doctrine:database:create`
* `php bin/console make:migration`
* `php bin/console doctrine:migrations:migrate`
* `php bin/console doctrine:fixtures:load`
