<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 20/12/2017
 * Time: 09:48
 */

namespace Helpers\Cache;

require_once('AbstractCache.php');

class ActivityCache extends AbstractCache
{

    function check($activityId) {
        return file_exists($this->path($activityId));
    }

    function write($activityId, $obj) {
        $fp = fopen($this->path($activityId), 'w');
        fwrite($fp, json_encode($obj));
        fclose($fp);
    }

    function read($activityId) {
        if(!$this->check($activityId)) {
            throw new CacheHitException("Failed to find item for Activity $activityId");
        }
        $json = file_get_contents($this->path($activityId));
        return json_decode($json,true);
    }

    function clear() {
        $results = array_map('unlink', glob( $this->path("*")));

        if(in_array(false, $results)) {
            throw new \Exception("Not all cache files could be deleted...");
        }
    }

    function delete($activityId) {
        return unlink($this->path($activityId));
    }

    function invalidate() {
        $cacheDir = \Helpers\Path::join(array(
            $this->cachePath,
            "Activities"));
        $files = glob($cacheDir . DIRECTORY_SEPARATOR . "*.json");
        $now   = time();

        $allSucessful = true;

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * 2) { // 2 days
                    $allSucessful &= unlink($file);
                }
            }
        }

        return $allSucessful;
    }

    private function path($activityId) {
        return \Helpers\Path::join(array(
            $this->cachePath,
            "Activities",
            "activity_" . $activityId . ".json"
        ));
    }
}