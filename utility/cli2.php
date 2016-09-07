<?php

$msg = implode('|',$_SERVER)."\n\nController: ".$controller."\n\nTask: ".$task."\n\nArgs: ".$args."\n\nDate/time: ".date('Y-m-d H:i:s.u');

mail('r4l.tech@junemedia.com','watch dog script',$msg);

?>
