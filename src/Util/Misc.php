<?php

namespace HapiClient\Util;

/**
 * Utility functions.
 */
class Misc
{
    /**
     * Recursively removes all the keys
     * containing an empty string as value.
     * Also removes empty arrays in the process.
     *
     * @param array $array The array to filter
     *
     * @return array the filtered array
     */
    public static function removeEmptyStrings(array $array)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::removeEmptyStrings($value);

                if (empty($array[$key])) {
                    unset($array[$key]);
                }
            } elseif ('' === $value) {
                unset($array[$key]);
            }
        }

        return $array;
    }

    /**
     * Looks for a Certificate Authority file in the CA folder
     * that matches the host and return the values for the
     * verify option to its full path.
     * If no specific file regarding a host is found, uses
     * curl-ca-bundle.crt by default.
     *
     * @param string $url   The host
     * @param string $caDir The CA folder
     *
     * @return string The Certificate Authority file
     */
    public static function verify(string $url, string $caDir)
    {
        static $hostsByDir = [];

        if (!isset($hostsByDir[$caDir])) {
            $hostsByDir[$caDir] = [];
        }

        $extensions = ['crt', 'pem', 'cer', 'der'];

        // Must be https
        if ('https' !== substr($url, 0, 5)) {
            return false;
        }

        // Default
        $defaultCA = $caDir.'curl-ca-bundle.crt';

        // Look for a host specific CA file
        $host = strtolower(parse_url($url, PHP_URL_HOST));
        if (!$host) {
            return $defaultCA;
        }

        if (array_key_exists($host, $hostsByDir[$caDir])) {
            return $hostsByDir[$caDir][$host];
        }

        $filename = $host;
        do {
            // Look for the possible extensions
            foreach ($extensions as $ext) {
                if (file_exists($verify = $caDir.$filename.'.'.$ext)) {
                    $hostsByDir[$caDir][$host] = $verify;

                    return $verify;
                }
            }

            // Remove a subdomain each time
            $filename = substr($filename, strpos($filename, '.') + 1);
        } while (substr_count($filename, '.') > 0);

        // No specific match
        $hostsByDir[$caDir][$host] = $defaultCA;

        return $defaultCA;
    }
}
