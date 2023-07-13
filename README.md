# SYMFONY REST API PROJECT

### PHP 8.1 or above required

--> composer install

--> symfony console doctrine:database:create

-->symfony console doctrine:migrations:migrate


### Launch the project

--> symfony serve -d

(or)

--> symfony server:start

### Fixtures

--> symfony console doctrine:fixtures:load AppFixtures