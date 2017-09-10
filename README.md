# Currere
Running application synchronisation and visualisation

# Progress
* Started overall architecture with "Connectors", which are API's to various fitness providers (runkeeper, strava for now).
* Implemented a Load script for those connectors
* Incorporated the StravaApi through PHP Composer.

# Todo
* Flesh out Strava functionality
* Create and flesh out RunKeeper functionality
* Create a homogenous Activity class to represent both RunKeeper and Strava activities
* Persist all previous activities in a DB
* Find a way (probably timestamp?) to detect dups between various apps
* Front-end display (Either Chart.js or vis.js)
* WebPack for front-end deps
* Do we need a history of what has been synced in a DB?

# Setup
* Upload to server
* Run PHP Composer to install the back-end dependencies
* Run WebPack to install front-end deps (? **TODO**)
* Configure the client secret, etc. 