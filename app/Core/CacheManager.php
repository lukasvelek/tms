<?php

namespace App\Core;

use App\Constants\CacheCategories;
use App\Entities\UserEntity;

/**
 * CacheManager allows the application to cache data
 * 
 * @author Lukas Velek
 */
class CacheManager {
    private const SERIALIZE = true;
    private const ADVANCED_CACHE_PROTECTION = true;

    private FileManager $fm;
    private string $category;
    
    /**
     * The CacheManager constructor
     * 
     * @param bool $serialize True if cache should be serialized and false if not
     * @param string $category Cache category
     */
    public function __construct(string $category, string $logdir, string $cachedir) {
        $this->fm = new FileManager($logdir, $cachedir);

        $this->category = $category;
    }

    public function loadClient(int $id, callable $callback) {
        $cacheData = $this->loadFromCache();

        if(empty($cacheData)) {
            $result = $callback();

            $cacheData[$id] = $result;

            $this->saveToCache($cacheData);
        }

        if(array_key_exists($id, $cacheData)) {
            $result = $cacheData[$id];
        } else {
            $result = $callback();

            $cacheData[$id] = $result;

            $this->saveToCache($cacheData);
        }

        return $result;
    }

    public function loadUser(int $id, callable $callback) {
        $cacheData = $this->loadFromCache();

        if(empty($cacheData)) {
            $result = $callback();

            $cacheData[$id] = $result;

            $this->saveToCache($cacheData);
        }

        if(array_key_exists($id, $cacheData)) {
            $result = $cacheData[$id];
        } else {
            $result = $callback();

            $cacheData[$id] = $result;

            $this->saveToCache($cacheData);
        }

        return $result;
    }

    /**
     * Saves a flash message to cache
     * 
     * @param array $data Flash message data
     */
    public function saveFlashMessage(array $data) {
        $cacheData = $this->loadFromCache();

        $cacheData[] = $data;

        $this->saveToCache($cacheData);
    }

    /**
     * Loads a flash message from cache
     * 
     * @return null|array|string Flash message data
     */
    public function loadFlashMessage() {
        $cacheData = $this->loadFromCache();

        if(empty($cacheData)) {
            return null;
        } else {
            return $cacheData;
        }

        return null;
    }

    /**
     * Saves array to cache
     * 
     * @param array $array Array
     * @return void
     */
    public function saveArrayToCache(array $array) {
        $cacheData = $this->loadFromCache();

        $cacheData[$this->category] = $array;

        $this->saveToCache($cacheData);
    }

    /**
     * Saves string to cache
     * 
     * @param string $text Text
     * @return void
     */
    public function saveStringToCache(string $text) {
        $cacheData = $this->loadFromCache();

        $cacheData[$this->category][] = $text;

        $this->saveToCache($cacheData);
    }

    /**
     * Loads strings from cache
     * 
     * @return null|mixed Cached data
     */
    public function loadStringsFromCache() {
        $cacheData = $this->loadFromCache();

        if(empty($cacheData)) {
            return null;
        }

        return $cacheData[$this->category];
    }

    /**
     * Saves user to cache
     * 
     * @param User $user User instance
     * @return void
     */
    public function saveUserToCache(UserEntity $user) {
        $cacheData = $this->loadFromCache();

        $cacheData[$this->category][$user->getId()] = $user;

        $this->saveToCache($cacheData);
    }

    /**
     * Loads user from cache
     * 
     * @param int $id User ID
     * @return null|User User instance or null
     */
    public function loadUserByIdFromCache(int $id) {
        $cacheData = $this->loadFromCache();

        if(empty($cacheData)) {
            return null;
        }

        if(array_key_exists($id, $cacheData[$this->category])) {
            return $cacheData[$this->category][$id];
        } else {
            return null;
        }
    }

    /**
     * Invalidates cache of some category by deleting the file.
     */
    public function invalidateCache() {
        $filename = $this->createFilename();

        $this->fm->deleteFile(CACHE_DIR . $filename);
    }

    /**
     * Saves a action right to cache
     * 
     * @param int $idUser ID user
     * @param string $key Action name
     * @param int $value 1 if action right is allowed and 0 if not
     */
    public function saveActionRight(int $idUser, string $key, int $value) {
        $cacheData = $this->loadFromCache();

        $cacheData[$idUser][$key] = $value;

        $this->saveToCache($cacheData);
    }

    /**
     * Loads the action right from cache
     * 
     * @param int $idUser ID user
     * @param string $key Action name
     * @return bool|null True if action right is allowed, false if it is not allowed, null if the entry does not exist
     */
    public function loadActionRight(int $idUser, string $key) {
        $cacheData = $this->loadFromCache();

        if(empty($cacheData)) {
            return null;
        }

        if(!array_key_exists($idUser, $cacheData)) {
            return null;
        }

        foreach($cacheData as $idUser => $keys) {
            if(!array_key_exists($key, $keys)) {
                return null;
            } else {
                return $keys[$key] ? true : false;
            }
        }
    }

    /**
     * Saves a bulk action right to cache
     * 
     * @param int $idUser ID user
     * @param string $key Bulk action name
     * @param int $value 1 if bulk action right is allowed and 0 if not
     */
    public function saveBulkActionRight(int $idUser, string $key, int $value) {
        $cacheData = $this->loadFromCache();

        $cacheData[$idUser][$key] = $value;

        $this->saveToCache($cacheData);
    }

    /**
     * Loads the bulk action right from cache
     * 
     * @param int $idUser ID user
     * @param string $key Bulk action name
     * @return bool|null True if bulk action right is allowed, false if it is not allowed, null if the entry does not exist
     */
    public function loadBulkActionRight(int $idUser, string $key) {
        $cacheData = $this->loadFromCache();

        if(empty($cacheData)) {
            return null;
        }

        if(!array_key_exists($idUser, $cacheData)) {
            return null;
        }

        foreach($cacheData as $idUser => $keys) {
            if(!array_key_exists($key, $keys)) {
                return null;
            } else {
                return $keys[$key] ? true : false;
            }
        }
    }

    /**
     * Saves a metadata right to cache
     * 
     * @param int $idUser ID user
     * @param string $key Metadata name
     * @param int $value 1 if metadata right is allowed and 0 if not
     */
    public function saveMetadataRight(int $idUser, int $idMetadata, string $key, int $value) {
        $cacheData = $this->loadFromCache();

        $cacheData[$idUser][$idMetadata][$key] = $value;

        if($cacheData != null) {
            $this->saveToCache($cacheData);
        }
    }

    /**
     * Loads the metadata right from cache
     * 
     * @param int $idUser ID user
     * @param string $key Metadata name
     * @return bool|null True if metadata right is allowed, false if it is not allowed, null if the entry does not exist
     */
    public function loadMetadataRight(int $idUser, int $idMetadata, string $key) {
        $cacheData = $this->loadFromCache();

        if(empty($cacheData)) {
            return null;
        }

        if(!array_key_exists($idUser, $cacheData)) {
            return null;
        }

        foreach($cacheData as $idUser => $metadata) {
            if(!array_key_exists($idMetadata, $metadata)) {
                return null;
            }

            foreach($metadata as $idMetadata => $keys) {
                if(!array_key_exists($key, $keys)) {
                    return null;
                } else {
                    return $keys[$key] ? true : false;
                }
            }
        }
    }

    /**
     * Generates a filename for the cache file
     * 
     * @return string Filename
     */
    public function createFilename() {
        $name = date('Y-m-d') . $this->category;

        if(isset($_SESSION['id_current_user'])) {
            $name .= $_SESSION['id_current_user'];
        }

        $dirname = '';

        if(!is_dir($this->fm->cacheFolder . $dirname)) {
            mkdir($this->fm->cacheFolder . $dirname);
        }

        if(!is_dir($this->fm->cacheFolder . $dirname . '/' . $this->category . '/')) {
            mkdir($this->fm->cacheFolder . $dirname . '/' . $this->category . '/');
        }

        $file = $dirname . '/' . $this->category . '/' . md5($name) . '.tmp';

        return $file;
    }

    /**
     * Loads data from cache
     * 
     * @return array|false $data Cache data or false if no data exists
     */
    private function loadFromCache() {
        $filename = $this->createFilename();

        $data = $this->fm->readCache($filename);

        if($data === FALSE) {
            return [];
        }

        if(self::SERIALIZE) {
            if(self::ADVANCED_CACHE_PROTECTION) {
                $data = unserialize(base64_decode($data));
            } else {
                $data = unserialize($data);
            }
        }

        return $data;
    }

    /**
     * Saves data to cache
     * 
     * @param array $data Data to be cached
     */
    private function saveToCache(array $data) {
        $filename = $this->createFilename();

        if(self::SERIALIZE) {
            if(self::ADVANCED_CACHE_PROTECTION) {
                $data = base64_encode(serialize($data));
            } else {
                $data = serialize($data);
            }
        }

        $this->fm->writeCache($filename, $data);
    }

    /**
     * Returns a temporary object
     * 
     * @param string $category Cache category
     * @return CacheManager self
     */
    public static function getTemporaryObject(string $category, bool $isAjax = false) {
        if($isAjax) {
            return new self($category, LOG_DIR, CACHE_DIR);
        } else {
            return new self($category, LOG_DIR, CACHE_DIR);
        }
    }

    /**
     * Invalidates all types of cache
     */
    public static function invalidateAllCache() {
        foreach(CacheCategories::$all as $cc) {
            $cm = new self($cc, LOG_DIR, CACHE_DIR);

            $cm->invalidateCache();
        }
    }
}

?>