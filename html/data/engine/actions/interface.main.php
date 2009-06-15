<?php

// All the dynamic windows are binded on the main interface. So when it updates,
// all the windows are disposed. Thus, cleanup dynupdate cache
gl_dynupdate_cleanup();
callEvent('interface.main');

?>