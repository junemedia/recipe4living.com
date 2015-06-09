<?php

$msg = implode('|',$_SERVER)."\n\nController: ".$controller."\n\nTask: ".$task."\n\nArgs: ".$args."\n\nDate/time: ".date('Y-m-d H:i:s.u');

mail('samirp@silvercarrot.com,leonz@silvercarrot.com,williamg@silvercarrot.com','watch dog script',$msg);

?>
