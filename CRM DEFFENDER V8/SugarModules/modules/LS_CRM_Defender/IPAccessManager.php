<?php

class IPAccessManager
{

    private $filename;

    public function __construct()
    {
//        $this->filename = $GLOBALS['suitecrm_version'][0] === '8' ? '../../.htaccess' : '.htaccess';
        $this->filename = '../../.htaccess';
        define('ALLOW_SPECIFIED_IPS_AND_BLOCK_OTHERS', 'ALLOW');
        define('REMOVE_SPECIFIED_IPS_AND_ALLOW_ALL', 'REMOVE');
    }

    public function allowIPs($type, $ips = [])
    {

        if (count($ips) === 0) {
            throw new Exception("No IPs provided to allow.");
        }
        // Open the file and lock it
        $handle = fopen($this->filename, 'c+');
        if (!$handle) {
            $GLOBALS['log']->error("Could not open the .htaccess file.");
        }

        if (flock($handle, LOCK_EX)) { // Acquire an exclusive lock
            // Read the file content
            $fileContent = '';
            while (($line = fgets($handle, 4096)) !== FALSE) {
                $fileContent .= $line;
            }

            // Remove existing CRM Defender section
            $fileContent = $this->removeOldIPRules($fileContent);

            // Add new rules
            $newRules = $this->generateRules($ips);

            // Write new content to the file
            ftruncate($handle, 0); // Clear the file
            rewind($handle); // Reset the pointer to the start

            if ($type === ALLOW_SPECIFIED_IPS_AND_BLOCK_OTHERS)
                fwrite($handle, $fileContent . $newRules);
            else
                fwrite($handle, $fileContent);

            chmod($this->filename, 0644); // Set correct permissions
            fflush($handle); // Ensure everything is written
            flock($handle, LOCK_UN); // Unlock the file
        } else {
            $GLOBALS['log']->error("Couldn't lock the .htaccess file.");
        }

        fclose($handle);
    }

    private function removeOldIPRules($content)
    {
        // Define the start and end markers for CRM Defender section
        $startMarker = "# CRM DEFENDER BEGIN RESTRICTIONS";
        $endMarker = "# CRM DEFENDER END RESTRICTIONS";

        // Use regex to strip out the existing CRM Defender section
        $pattern = "/$startMarker.*?$endMarker/s";
        return preg_replace($pattern, '', $content);
    }

    private function generateRules($ips)
    {
        // Define the new CRM Defender section
        $text = "\n# CRM DEFENDER BEGIN RESTRICTIONS\n";

        foreach ($ips as $ip) {
            $ip_address_parts = explode(".", $ip);
            $a = $ip_address_parts[0];
            $b = $ip_address_parts[1];
            $c = $ip_address_parts[2];
            $d = $ip_address_parts[3];

            $text .= "
# IP ADDRESS BEGIN: $ip
SetEnvIF REMOTE_ADDR \"^$a\\.$b\\.$c\\.$d\$\" AllowAccess
SetEnvIF X-FORWARDED-FOR \"^$a\\.$b\\.$c\\.$d\$\" AllowAccess
SetEnvIF X-CLUSTER-CLIENT-IP \"^$a\\.$b\\.$c\\.$d\$\" AllowAccess";
        }

        // Add rules to allow only from specified IPs and block others
        $text .= "
Order deny,allow
Deny from all
Allow from env=AllowAccess

# CRM DEFENDER END RESTRICTIONS\n";

        return $text;
    }
}
