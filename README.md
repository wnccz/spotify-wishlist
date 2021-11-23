# spotify-wishlist
Wishlist for playing music on the party.

## How to install project
1. Clone repo
2. Update composer in root folder
3. Create MySQL database (structure is in sql/structure.sql file)
4. Copy config/local.neon.tpl to config/local.neon
5. Copy www/htaccess.tpl to www/.htaccess
6. Setup database in config/local.neon
7. Create App in Spotify API dasboard (https://developer.spotify.com/dashboard/applications)
8. Setup client_id and client_secret in config/local.neon
9. Add absolute address of Sign:Callback action to Project settings/Redirect URI (spotify API dashboard)
10. Add allowed user to User and Access settings (spotify API dashboard)
