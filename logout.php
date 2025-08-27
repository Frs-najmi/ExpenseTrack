<?php

session_start();

session_destroy();

header("Location: smartspend_page.html");
exit;