# Coffee machine API

## How do I want to approach this project?

Because of the project size, I am going to create a new API using [Lumen](https://lumen.laravel.com/).

Thinking on coffee machines, I have decided to store the state of the machine in the filesystem but the code is ready to specify any other mechanism.

## Endpoints

- `/api/v1/machineStatus` [`GET`]
- `/api/v1/makeEspresso` [`POST`]
- `/api/v1/makeDoubleEspresso` [`POST`]

## How to test the solution?

- Clone this project.
- Update `resources/data/beans_container.json` and `resources/data/water_container.json` with any specific data. 
- Execute `php -S localhost:8000 -t public`.
- Use `curl` or any other tool like Postman to test the solution. 

### Future improvements

- Refactor code to not rely on stdClass and use specific models.
- Add `addBeans` endpoint.
- Add `addWater` endpoint.
- Add `addMachine` endpoint.
- Add `upgradeBeansContainer` endpoint.
- Add `upgradeWaterContainer` endpoint.
- Allow control many coffee machines with the same API.
- Add authentication.
