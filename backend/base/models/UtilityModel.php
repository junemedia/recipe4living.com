<?php

/**
 *      Glue which holds the network of bluCommerce sites together, innit?
 */
class BackendUtilityModel extends BluModel {

        private $_messageStack = Array();

        private $_godMode = false;

        /**
         * Server watchdog process
         */
        public function watchdog()
        {
                if (CLI !== true) {
                        echo 'You *must* be in a CLI environment to run this command';
                        return false;
                }

                // Startup - grab a connection to each database
                if ($databases = BluApplication::getSetting('databases')) {
                } else {
                        $databases = array(
                                array(
                                        'databaseHost' => $this->_settings['app']['databaseHost'],
                                        'databaseUser' => $this->_settings['app']['databaseUser'],
                                        'databasePass' => $this->_settings['app']['databasePass'],
                                        'databaseName' => $this->_settings['app']['databaseName']
                                )
                        );
                }
                foreach ($databases as $dbKey=>$database) {
                        $dbh = Database::getInstance($database['databaseHost'], $database['databaseUser'], $database['databasePass'], $database['databaseName']);
                        $dbh->allowErrors(true, true);
                        $databaseHandles[] = $dbh;
                }

                $system = new System();
                $cpuinfo = $system->cpu_info();
                $loadThresholdLow = $cpuinfo['cpus'] * 2; // If our RQL is greater than twice the number of CPUs, we're either in trouble, or about to be in trouble
                $loadThresholdHigh = $cpuinfo['cpus'] * 5; // Things are about to go south.
                $swapUse = 0;
                $ramFree = 0;
                $loadAvg = 1;
                $questions = 0;
                $graceTimer = 0;
		$hostname = `hostname`;
		$hostname = trim($hostname);
                // Uh, and that's it for the startup. Easy, huh?
                while (true) { // Go forever, and ever, and ever.
                        if ($graceTimer > 0) {
                                $graceTimer--;
                                exec("/etc/init.d/httpd start");
                        }

                        // Check DB health
                        foreach ($databaseHandles as $dbh) {
                                $trouble = false;
                                $dbh->setQuery('SELECT 1');
                                if ($dbh->loadResult() != 1)
                                        $trouble = true;
                                $stats = $dbh->getStats();
                                $questionDelta = $stats['Questions'] - $questions;
                                $questions = $stats['Questions'];
    //                           Utility::irc_dump($hostname.": ".$stats['raw']." Question Delta : ".$questionDelta, '#r4lsuicidewatch');
                        }

                        // Check general system health
                        $loadav = sys_getloadavg();
                        $loadAvgDelta = $loadav[0] - $loadAvg;
                        $loadAvg = $loadav[0];
                        $memory = $system->memory();

                        // We're interested in free ram and swap use.
                        $swapUseDelta = $memory['swap']['used'] - $swapUse;
                        $ramFreeDelta = $memory['ram']['free'] - $ramFree;
                        $swapUse = $memory['swap']['used'];
                        $ramFree = $memory['ram']['free'];

  //                      Utility::irc_dump($hostname.": Swap used : $swapUse (".$swapUseDelta."), RAM free : $ramFree (".$ramFreeDelta.")", "#r4lsuicidewatch");
//                        Utility::irc_dump($hostname.": 1 Min Load : ".$loadAvg." (".$loadAvgDelta.")", "#r4lsuicidewatch");

                        if ($loadAvg > $loadThresholdHigh || $ramFree < 800000) {
                                Utility::irc_dump($hostname.": Load threshold is over maximum threshold", "#r4lsuicidewatch");
                                if ($graceTimer == 0) {
                                        Utility::irc_dump($hostname.": RESTARTING APACHE", "#r4lsuicidewatch");
                                        exec ("/etc/init.d/httpd stop");
                                        exec ("killall -s9 httpd");
                                        exec ("killall -s9 apache2");
					$dbh->setQuery('UPDATE memcacheReference SET status="uptodate"');
					$dbh->query();
                                        exec ("ipcs -s | grep apache | awk ' { print $2 } ' | xargs ipcrm sem");
                                        exec ("/etc/init.d/httpd start");
                                        Utility::irc_dump($hostname.": Apache restarted!", "#blubolt");
                                        $graceTimer = 6;
                                } else {
                                        Utility::irc_dump($hostname.": IN GRACE PERIOD : ".$graceTimer, "#r4lsuicidewatch");
                                }

                        // Bad things afoot
                        } else if ($loadAvg > $loadThresholdLow) {
                                // There may be bad things afoot, soon
                                Utility::irc_dump($hostname.": Load threshold is getting a bit high. Tell people.", "#r4lsuicidewatch");
                        }

                        sleep(10);
                }

                //$this->_cache->flush();
                $this->_db->query();
        }
}
