# kostildemo
Web-service, which allows you to store and view info about demo records from your server. \
This repository is a fork of [Bubuni-Team/kostildemo](https://github.com/Bubuni-Team/kostildemo) \
This fork adds pagination and search.

## Features
 - [x] Pagination (10/25/50 records per page)
 - [x] Search (Map name, player name, account id, date (day.month))
 - [x] Light/Dark theme
 - [ ] Filter (?)
 - [ ] Sorting (?)

## Requirements
 - PHP 7.4 and larger.
 - PHP exts: JSON, PDO, PDO-MySQL.
 
## Installation (not full)
1. Install/check all dependencies on webserver.
1. Download latest release and unzip the archive with the web part.
1. Create a database in MySQL, load the `database.sql` dump into it.
1. Remove `.sample` from configuration file `config.php.sample` and fill it out carefully.

## Credits
[SourceMod plugin](https://github.com/Bubuni-Team/sm-kostildemo)

## License
kostildemo is licensed under the GNU General Public License version 3.