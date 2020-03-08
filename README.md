# printify test 

```
composer install
```

```
 bin/console doctrine:migrations:migrate
```

```
bin/console doctrine:fixtures:load
```


## api routes

### GET /users 
get list of users

### POST /user
```
{
  "name": "Ivan Svetlakov"
}
```
Create new user

### POST /product
```
{
  "sku": "2",
  "cost": 100,
  "title": "product 1",
  "product_type_id": 1,
  "user_id": 1
}
```
create new product

### GET /user/{userId}/orders

Get list of orders for given user

### GET /user/{userId}/products

Get list of orders for given user

### POST /order
```
{
  "address": {
  	"type" : "Domestic",
  	"full_name" : "Denis Sieg" ,
                "address" : "123 213",
                "country" : "Latveria", 
                "state" : "Man",
                "city" : "Prague ",
                "zip" : "123321",
                "phone" : "+3312345 "
  },
  "shipping_type": "Standard",
  "user_id": 1,
  "products": [3,3,4]
}
```
Create new order

## Run Unit tests

```
$ php bin/phpunit
```


# What needs to be improved

Actually lots of stuff:
1. table names are poor (like printing_order)
2. more accurate validation for phone, city, etc
3. contain all shipping prices separately in db
4. more unit tests + functuonal
5. to do something with code duplicates
6. ...

But I think that this is still pretty enough for a test.
