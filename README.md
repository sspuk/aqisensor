# AQISensor

Project to measure air quality using a raspberry pi and SDS-011 sensor.

First setup the central server. 
- Install and configure PHP and MariaDB on the server.
- You will need to obtain a Google Maps API Key to use the heatmap function.

MariaDB
- Create a Database, add a user and grant privileges on the db for the user.
- Create the intial tables in MariaDB using db/database_create. More information on setting up the database is contained in db_notes.

Web Application.
- Copy over the files in www to the Document Root.
- Copy files credentials_changeme.php to credentials.php. Edit the file and set the db name, db username and password, and the Google API key.

At this point the central server should be setup.

Goto the sensors page and add a new sensor on the location you intend to monitor. Note the machine id.

Once you have cloned the git repo, on the monitoring station, edit file read_sensor.py and change variables
- UPLOAD_URL - to point to the central server location
- MC_ID - To match the machine id at the location you intend to use the device
- Setup a cronjob to run this command every fifteen minutes.

The data is stored on the central server where one can also view the heat maps.
