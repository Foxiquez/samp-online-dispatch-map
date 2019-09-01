# samp-online-dispatch-map
Online dispatch map with socket server for GTA SA:MP
1. Install composer, rename htaccess files in root and public directories, download tiles archive and unzip in public dir.
2. Application must be installed on VPS or VDS, not on simple sites host.
3. Check if the dafault (8080) port open, else open or change this. Also add php hadler to your firewall exceptions.
4. Change index.php in public:
    * remove or uncomment sesseion check at the file beggining;
    * on line 36 change localhost domain on yours;
5. Change file setings in App dir, necessarily change $securityKey to yours own.
6. Start your socket server:
    * open console and enter 'php file', instead of file put the way to Server.php file in App directory;
    * if the php handler not installed global enter in console way to php handler and the way to server file;
7. Game logic for adding/removing units in pawno:
    * add to game server code to connect to socket;
    * write another code with time loop to send info about (adding/removing/read coords) units;
    * you must send your info in JSON format;
    * example: {"key":"KEY", "car1": {"x":"1500", "y":"-1661", "title":"testTitle1", "description":"descr"}, "car2": {"x":"1550", "y":"-1661", "title":"testTitle2", "description":"descr"}} where KEY - your secret key you entered in config file;
8. For questions: vk.com/godworld303 foxiquez.bl@gmail.com - create by Bohdan Lus Foxiquez;
9 . Tiles: https://www.upload.ee/files/10432403/tiles.rar.html
