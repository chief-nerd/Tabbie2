Development environment installation instructions
=================================================
These instructions are for XAMPP on Linux.

1. Install XAMPP from https://www.apachefriends.org/.

2. Add `/opt/lampp/bin` to your `PATH` environment variable:

        $ PATH=$PATH:/opt/lampp/bin

3. Install Composer from https://getcomposer.org/.

4. Go to the tabbie2.git directory.

5. Run these commands:

        $ ./init
        $ php composer.phar install
        $ php requirements.php

6. If you want to create a new user account for database access in MySQL:

        $ sudo /opt/lampp/bin/mysql
        mysql> CREATE USER 'username'@'localhost' IDENTIFIED BY 'password';
        mysql> GRANT ALL ON *.* TO 'username'@'localhost';
        mysql> \q

7. Create the database:

        $ mysql -uusername -p # prompts for password you made up above
        mysql> CREATE DATABASE yourdbname;
        mysql> \q

8. Run the migration:

        $ ./yii migrate

9. Open in your browser: http://localhost/

   If you encounter an error about `vendor/bower/dist` not existing, it might
   have been installed as `vendor/bower-asset` instead. I found a symbolic link
   helped:
        $ cd vendor
        $ ln -s bower-asset bower
