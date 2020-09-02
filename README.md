# FermentingDatabase
Code to create an interactive webpage with a MySQL database for storing fermenting project data

# Installation
1. Install Apache and PHP
2. Clone repo in /var/www/html
3. cd FermentingDatabase
4. Install phpmyadmin here (digitalocean.com has good instructions) and update username, servername, and password in test_php.php
5. Add the database ferment_data
6. Add table All_Records to ferment data
7. Add the following columns (varchar of type utf8-general ci, all may be null)
  a. brew_name_time, varchar(255), default null
  b. done, BOOLEAN, default null
  c. mode, varchar(255), default null
  d. timestamp, varchar(255), default current_timestamp()
8. Visit the site at {your ip}/FermentingDatabase and start brewing!
