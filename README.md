# React-PHP learning

I'm exploring react-php by freely folowing [this tutorial](https://sergeyzhuk.me/2019/02/18/restful-api-with-reactphp-and-mysql/) and [that one](https://sergeyzhuk.me/2018/03/13/using-router-with-reactphp-http/).

This project is usable for learning purpose.

# How to use

To use this project you can clone it, remove all 'business' code and replace it by yours.

Yo have to create entities according to your database schema.
To request on this entities you have to create a repository by entities accordingly to the UserRepository example.

Then you have to create at least one controller, each method matching a route.
To configure witch method match witch route use the `config/controller.yml` example.

To launch the server run `php index.php` and have fun ! 

# License

This project is released under the [WTFPL LICENSE](http://www.wtfpl.net/).
