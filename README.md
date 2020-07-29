# CI_medleycache
**Medley cache library for CodeIgniter**

This library provides quasi clouded cache service based on Telegram. 
The main thing is quite simple. Telegram channel is used as a file storage, but every post in your channel is a cache file content from your CI application. 
Filenames of cached files are holded in another cache system (file based, Redis, Memcached, MongoDB). 
Using this library lets you avoid such limitations as disk space and "i nodes" on servers' file system. All of data is located on Telegram cloud.

**Required settings:**

1. Register a Telegram bot (using @botfather) and save the bot credentials
2. Register a Telegram channel (must be public and have uri like "t.me/<unique_channel_name>", use obfuscated name for it), add your previously created bot as administrator to this channel
3. Choose cache system for storage returned by Telegram file names (file cache is not recommended, because it can lead to exhaustion of i nodes, if your project involves a large number of cache files. For this purposes is better to use Redis, Memcache or MongoDB)
After installation of this library all of your applications' cache will be stored on Telegram and key-value storage. This way lets you to scale and/or move your application at any time. This is quite convenient for very large applications like huge web databases.

**Installation**
1. Copy all files from application dir to your project
2. Edit *application/config/medleycache.php* according to your prefs. I extremely recommend to use Redis for store metadata, because it's scalable. Try not to use file cache.
3. Chmod *application/cache* to 777

**Use in application example**

*In constructor write:*

```
$this->load->library(['medleycache']);
```

*In controller write:*
```
$key = 'your_unique_cache_key';

$data = 'some data need to be cached';

if (!$cachedData = $this->medleycache->get($key)) {

  $this->medleycache->save($key, $data, 0);
  
}
```

As you can see it's simple as usual CI cache.
