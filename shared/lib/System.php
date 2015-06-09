<?php
Class System {
	function memory () {
		$results['ram'] = array('total' => 0, 'free' => 0, 'used' => 0, 'percent' => 0);
		$results['swap'] = array('total' => 0, 'free' => 0, 'used' => 0, 'percent' => 0);
		$results['devswap'] = array();

		$bufr = $this->_rfts( '/proc/meminfo' );
		if ( $bufr != "ERROR" ) {
			$bufe = explode("\n", $bufr);
			foreach( $bufe as $buf ) {
				if (preg_match('/^MemTotal:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
					$results['ram']['total'] = $ar_buf[1];
				} else if (preg_match('/^MemFree:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
					$results['ram']['free'] = $ar_buf[1];
				} else if (preg_match('/^Cached:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
					$results['ram']['cached'] = $ar_buf[1];
				} else if (preg_match('/^Buffers:\s+(.*)\s*kB/i', $buf, $ar_buf)) {
					$results['ram']['buffers'] = $ar_buf[1];
				}
			}

			$results['ram']['used'] = $results['ram']['total'] - $results['ram']['free'];
			$results['ram']['percent'] = round(($results['ram']['used'] * 100) / $results['ram']['total']);

			// values for splitting memory usage
			if (isset($results['ram']['cached']) && isset($results['ram']['buffers'])) {
				$results['ram']['app'] = $results['ram']['used'] - $results['ram']['cached'] - $results['ram']['buffers'];
				$results['ram']['app_percent'] = round(($results['ram']['app'] * 100) / $results['ram']['total']);
				$results['ram']['buffers_percent'] = round(($results['ram']['buffers'] * 100) / $results['ram']['total']);
				$results['ram']['cached_percent'] = round(($results['ram']['cached'] * 100) / $results['ram']['total']);
			}

			$bufr = $this->_rfts( '/proc/swaps' );
			if ( $bufr != "ERROR" ) {
				$swaps = explode("\n", $bufr);
				for ($i = 1; $i < (sizeof($swaps)); $i++) {
					if( trim( $swaps[$i] ) != "" ) {
						$ar_buf = preg_split('/\s+/', $swaps[$i], 6);
						$results['devswap'][$i - 1] = array();
						$results['devswap'][$i - 1]['dev'] = $ar_buf[0];
						$results['devswap'][$i - 1]['total'] = $ar_buf[2];
						$results['devswap'][$i - 1]['used'] = $ar_buf[3];
						$results['devswap'][$i - 1]['free'] = ($results['devswap'][$i - 1]['total'] - $results['devswap'][$i - 1]['used']);
						$results['devswap'][$i - 1]['percent'] = round(($ar_buf[3] * 100) / $ar_buf[2]);
						$results['swap']['total'] += $ar_buf[2];
						$results['swap']['used'] += $ar_buf[3];
						$results['swap']['free'] = $results['swap']['total'] - $results['swap']['used'];
						$results['swap']['percent'] = round(($results['swap']['used'] * 100) / $results['swap']['total']);
					}
				}
			}
		}
		return $results;
	}

	function cpu_info () {
		$bufr = $this->_rfts( '/proc/cpuinfo' );
		$results = array("cpus" => 0);

		if ( $bufr != "ERROR" ) {
			$bufe = explode("\n", $bufr);

			$results = array('cpus' => 0, 'bogomips' => 0);
			$ar_buf = array();

			foreach( $bufe as $buf ) {
				$arrBuff = preg_split('/\s+:\s+/', trim($buf));
				if( count( $arrBuff ) == 2 ) {
					$key = $arrBuff[0];
					$value = $arrBuff[1];
					// All of the tags here are highly architecture dependant.
					// the only way I could reconstruct them for machines I don't
					// have is to browse the kernel source.  So if your arch isn't
					// supported, tell me you want it written in.
					switch ($key) {
						case 'model name':
							$results['model'] = $value;
							break;
						case 'cpu MHz':
							$results['cpuspeed'] = sprintf('%.2f', $value);
							break;
						case 'cycle frequency [Hz]': // For Alpha arch - 2.2.x
							$results['cpuspeed'] = sprintf('%.2f', $value / 1000000);
							break;
						case 'clock': // For PPC arch (damn borked POS)
							$results['cpuspeed'] = sprintf('%.2f', $value);
							break;
						case 'cpu': // For PPC arch (damn borked POS)
							$results['model'] = $value;
							break;
						case 'L2 cache': // More for PPC
							$results['cache'] = $value;
							break;
						case 'revision': // For PPC arch (damn borked POS)
							$results['model'] .= ' ( rev: ' . $value . ')';
							break;
						case 'cpu model': // For Alpha arch - 2.2.x
							$results['model'] .= ' (' . $value . ')';
							break;
						case 'cache size':
							$results['cache'] = $value;
							break;
						case 'bogomips':
							$results['bogomips'] += $value;
							break;
						case 'BogoMIPS': // For alpha arch - 2.2.x
							$results['bogomips'] += $value;
							break;
						case 'BogoMips': // For sparc arch
							$results['bogomips'] += $value;
							break;
						case 'cpus detected': // For Alpha arch - 2.2.x
							$results['cpus'] += $value;
							break;
						case 'system type': // Alpha arch - 2.2.x
							$results['model'] .= ', ' . $value . ' ';
							break;
						case 'platform string': // Alpha arch - 2.2.x
							$results['model'] .= ' (' . $value . ')';
							break;
						case 'processor':
							$results['cpus'] += 1;
							break;
						case 'Cpu0ClkTck': // Linux sparc64
							$results['cpuspeed'] = sprintf('%.2f', hexdec($value) / 1000000);
							break;
						case 'Cpu0Bogo': // Linux sparc64 & sparc32
							$results['bogomips'] = $value;
							break;
						case 'ncpus probed': // Linux sparc64 & sparc32
							$results['cpus'] = $value;
							break;
						}
					}
				}

			// sparc64 specific code follows
			// This adds the ability to display the cache that a CPU has
			// Originally made by Sven Blumenstein <bazik@gentoo.org> in 2004
			// Modified by Tom Weustink <freshy98@gmx.net> in 2004

			$sparclist = array('SUNW,UltraSPARC@0,0', 'SUNW,UltraSPARC-II@0,0', 'SUNW,UltraSPARC@1c,0', 'SUNW,UltraSPARC-IIi@1c,0', 'SUNW,UltraSPARC-II@1c,0', 'SUNW,UltraSPARC-IIe@0,0');
			foreach ($sparclist as $name) {
				$buf = $this->_rfts( '/proc/openprom/' . $name . '/ecache-size',1 , 32, false );
				if( $buf != "ERROR" ) {
					$results['cache'] = base_convert($buf, 16, 10)/1024 . ' KB';
				}
			}
			// sparc64 specific code ends

			// XScale detection code
			if ( $results['cpus'] == 0 ) {
				foreach( $bufe as $buf ) {
					$fields = preg_split('/\s*:\s*/', trim($buf), 2);
					if (sizeof($fields) == 2) {
						list($key, $value) = $fields;
						switch($key) {
							case 'Processor':
								$results['cpus'] += 1;
								$results['model'] = $value;
								break;
							case 'BogoMIPS': //BogoMIPS are not BogoMIPS on this CPU, it's the speed, no BogoMIPS available
								$results['cpuspeed'] = $value;
								break;
							case 'I size':
								$results['cache'] = $value;
								break;
							case 'D size':
								$results['cache'] += $value;
								break;
						}
					}
				}
				$results['cache'] = $results['cache'] / 1024 . " KB";
			}
		}
		$keys = array_keys($results);
		$keys2be = array('model', 'cpuspeed', 'cache', 'bogomips', 'cpus');

		while ($ar_buf = each($keys2be)) {
			if (! in_array($ar_buf[1], $keys)) {
				$results[$ar_buf[1]] = 'N.A.';
			}
		}

		$buf = $this->_rfts( '/proc/acpi/thermal_zone/THRM/temperature', 1, 4096, false );
		if ( $buf != "ERROR" ) {
			$results['temp'] = substr( $buf, 25, 2 );
		}

		return $results;
	}

	private function _rfts( $strFileName, $intLines = 0, $intBytes = 4096, $booErrorRep = true ) {
		$strFile = "";
		$intCurLine = 1;

		if( file_exists( $strFileName ) ) {
			if( $fd = fopen( $strFileName, 'r' ) ) {
				while( !feof( $fd ) ) {
					$strFile .= fgets( $fd, $intBytes );
					if( $intLines <= $intCurLine && $intLines != 0 ) {
						break;
					} else {
						$intCurLine++;
					}
				}
				fclose( $fd );
			} else {
				return "ERROR";
			}
		} else {
			return "ERROR";
		}
		return $strFile;
	}

}

?>
