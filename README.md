# README #

This README would normally document whatever steps are necessary to get your application up and running.

### This is the Home Base Repository ###

* Welcome to Home Base, the software with a reasonably cool namespace and no idea where we’re going (well, kind of, more on that later).
  
  Home Base came about as an ap for a client which had a pretty good login, login tracking, and member management.  It was designed around the concept of one-database-one-account, with a subdomain equalling the account.  So, chicago.myservice.com and peoria.myservice.com are two accounts.  The databases might be myservice_chicago and myservice_peoria.  There is a modicum of a meta manager for a superadministrator of the accounts but as of December 19th 2016 it’s not well-defined.  These superadmin settings themselves have some measure of local account-level override.  What Home Base actually DID was manage products, but where I eventually want to take it is to allow the customer to create a service with user portal access, and create that service based on their own design of an RDBMS.  That service could be anything from Slack to Digital Ocean.  I plan to eventually refactor everything into a Zend II or Symfony but for now we’re still procedural (and very fast, I don’t want to lose that).

* Version 0.1

### Nice-to-have Answers ###

* Summary of set up
* Configuration
* Dependencies
* Database configuration
* How to run tests
* Deployment instructions

### Contribution guidelines ###

* Writing tests
* Code review
* Other guidelines

### Who do I talk to? ###

* Repo owner or admin
* Other community or team contact