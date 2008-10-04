
     _______              __                __
    / ___/ /__  ______ __/ /  ___ ____  ___/ /__
   / (_ / / _ \/ __/ // / /__/ _ `/ _ \/ _  (_-<
   \___/_/\___/_/  \_, /____/\_,_/_//_/\_,_/___/
                  /___/

        GloryLands Web-Based Browser MMoRPG
    Version 0.1a - Chaos Milestone, 04 Oct 2008

 =======================================================================
  1.About GloryLands
 =======================================================================

  GloryLands is a web-based Massive Multiplayer Online RPG, Browser game, 
 written entirely in PHP. It aims to be endless and always interesting, 
 using the constant contribution of the Game community.

  It's great advantage is the fully modable structure, that allows everyone
 to add his own flavor on the game:

  - Creative users can design their own maps or quests and make them public
    and playable, only by upload them on the game site!
  - Illustrators and designers can create their own tilesets and extend the
    imagination of the map designers!
  - Developers can create ANY kind of add-ons for the game, and pack them 
    into a package file that can be uploaded and be directly playable on the
    game!

  Also, the game contains some complex engines that can generate new
 unpredictable game items, by mixing the parameters of the pre-existing ones.
 thus expanding even more the existing items!

  If you like this game (hey, even if you *hate* it), please send an
 e-mail message to <wavesoft@users.sourceforge.net>

 =======================================================================
  2.Installing GloryLands from the SVN
 =======================================================================

 -----------------------------------------------------------------------
   2.1 Requirements
 -----------------------------------------------------------------------

 Server:

  - Apache  or IIS webserver (Apache Recommended)
  - PHP5    or later
  - MySQL 5 or later
  - SVN Client (such as TortoiseSVN on windows or svn on linux)
  - A PHP Accelerator is highly recommended, such as:
      - eAccelerator (Tested)
      - Alternative PHP Cache (APC)
      - XCache

 Client:

  - Mozilla-Based browser (highly recommended)
  - Internet Explorer 7 or later
  
  (Not tested yet on other browsers)

 -----------------------------------------------------------------------
   2.1 Installation procedure
 -----------------------------------------------------------------------

 1) Check out the SVN on an off-server directory from the following URL:

    https://glorylandsweb-b.svn.sourceforge.net/svnroot/glorylandsweb-b/trunk

    - In windows (TortoiseSVN) : Right click on an empty directory -> SVN Checkout 
                                 and enter the above URL
    - In linux                 : Navigate on an empty directory and type:

     svn co https://glorylandsweb-b.svn.sourceforge.net/svnroot/glorylandsweb-b/trunk

 2) Create a new database on your SQL server and import the html/sql/glory.sql file

 3) Create a new SQL user with SELECT,DELETE,INSERT,UPDATE priviledges on the new database

 4) Copy the contents of the "html" directory to your server's desired location

    Note: If you do not want to be able to perform later SVN updates on the game's 
          directory, you should use the svn export function.

 5) Edit the config/config.php file to match your settings. Pay special attention to 
    configure correctly the following settings:

    $_CONFIG[GAME][BASE] = "C:/xampp/htdocs/gl-sf"; << This should point on the game's 
                                                       absolute path on your server
    $_CONFIG[GAME][REF_URL] = "/gl-sf";             << This should point on the game's 
                                                       relative URL on your server. 
                                                       (ex. http://localhost/gl-sf)

 6) You now have a clean, running version of the game, but you should install some game
    packages in order to play it. The current SVN version contains some testing data that
    requires the following packages to be installed (those packages are found on the
    dev/packages/tilesets directory):

   Navigate on the http://<game root>/admin/packetman to install those files

   - chars-latest.zip
   - tileset-abandoned-building-v2.zip
   - tileset-z-castle-ext-v2.zip
   - tileset-z-castle-int-v2.zip
   - tileset-z-dragon-misc-v2.zip
   - tileset-z-dungeon-caves-v2.zip
   - tileset-z-field-ext-v2.zip
   - tileset-z-plains-ext-v2.zip
   - tileset-z-snow-v2.zip

 =======================================================================
  3.How to play GloryLands
 =======================================================================

  When the release version will be available, the usage will be a lot simpler,
 since in-game tips and introduction guide will be available. Since then,
 the people that are testing the game should read the following tips, or try
 to find them by themselfs digging into the game interface or source...

 * From the root page, login as admin/admin or player/player and create a new 
   character (The registration screen is not yet ready. If you want to add new 
   users, you should add them in the `users_accounts` table, setting the `name`, 
   `password` (password's MD5 hash), and `level` fields)

 * If you don't see your player, then probbably something is wrong with the 
   model files, click on the small blue button with the thunder icon on your 
   right to change your model.

 * You can move around by moving your mouse on the base of your character and 
   then clicking inside green zone that will be appeared.

 * Type /help on the chat window to view some commands that you can use

 * There are some cases you have to known the object's GUID number. To get it, 
   right click on the object and select the green "?" icon. An information window 
   will be shown, and you will see the item's GUID on the title.

 * Use right click on objects, buttons, or containers for more options
 
 * If you open a container you can drag the item icons and place them on you 
   quick access bar (10 gray slots on the right)

 * Developers might find the /admin page interesting ;)
  
 * Finally, the engine is currently in heavy development so there are cases that 
   something is might not be working. If that happens, try refreshing the page.

 =======================================================================
  4.Author
 =======================================================================

 GloryLands is written by John Haralampidis <wavesoft@users.sourceforge.net>
 Since the game is really big, any help will be really appreciated ;)

 =======================================================================
  5.Copyright Notice
 =======================================================================
 
 The game graphics are found on the internet by free to use (or claiming so) material.
 If you think that there might be a copyright violation please let me know 
 as soon as possible on <wavesoft@users.sourceforge.net>