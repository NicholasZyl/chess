# Chess
The project is a part of an engineering thesis written to demonstrate in practice Domain Driven Design and Behavior Driven Development approaches. It is an implementation of the game of chess, focusing mainly on the core domain logic - the game and its rules. 
## Getting Started
Chess is prepared to be run as a console application or as an HTTP API.
### Console application
To run console application you can use prepared Docker container by running `docker-compose run console`
### Web API
To run WEB API you need to spin up web container by running `docker-compose -d up web`. After this API is accessible on port 8080.
## Running the tests
Application was built with BDD approach. For automated tests [Behat](http://behat.org) and [PhpSpec](http://www.phpspec.net/en/stable/) frameworks were used. To run acceptance tests you need to run `vendor/bin/behat` and to run specifications `vendor/bin/phpspec run`.
 ## Contributing
Please read [CONTRIBUTING.md](https://github.com/NicholasZyl/chess/blob/master/CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.
## Authors
* Mikołaj Żyłkowski (_[NicholasZyl](https://github.com/NicholasZyl)_) - Engineering Thesis author
## License
This project is licensed under the MIT License - see the [LICENSE.md](https://github.com/NicholasZyl/chess/blob/master/LICENSE.md) file for details.