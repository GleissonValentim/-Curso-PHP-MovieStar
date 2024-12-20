<?php

session_start();

$BASE_URL = "http://" . $_SERVER["SERVER_NAME"] . str_replace(basename($_SERVER["SCRIPT_NAME"]), '', $_SERVER["SCRIPT_NAME"]);

