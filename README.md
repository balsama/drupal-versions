# Drupal Versions
Service which responds with the currently supported versions of Drupal.

## Live url:
http://drupal-versions.herokuapp.com/

To run the service yourself:

1. `$ git clone git@github.com:balsama/drupal-versions.git && cd drupal-versions`
2. `$ composer install`

You'll need to provide your own webserver and point it to the `/web` directory withing this repo.

Example response (generated November 2022):
```json
{
  "majors": {
    "current": {
      "version": 9,
      "minors": {
        "current": {
          "version": 4,
          "latest": "9.4.8",
          "devBranch": "9.4.x-dev"
        },
        "previous": {
          "version": 3,
          "latest": "9.3.22",
          "devBranch": "9.3.x-dev"
        },
        "next": {
          "version": 5,
          "latest": "9.5.0-beta2",
          "devBranch": "9.5.x-dev"
        }
      }
    },
    "previous": {
      "version": null,
      "minors": {
        "current": {
          "version": null,
          "latest": null,
          "devBranch": null
        },
        "previous": {
          "version": null,
          "latest": null,
          "devBranch": null
        },
        "next": {
          "version": null,
          "latest": null,
          "devBranch": null
        }
      }
    },
    "next": {
      "version": 10,
      "minors": {
        "current": {
          "version": null,
          "latest": null,
          "devBranch": null
        },
        "previous": {
          "version": null,
          "latest": null,
          "devBranch": null
        },
        "next": {
          "version": 0,
          "latest": "10.0.0-beta2",
          "devBranch": "10.0.x-dev"
        }
      }
    }
  },
  "status": 200
}
```
