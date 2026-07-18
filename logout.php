<?php
require_once 'util.php';
session_destroy();
header('Location: index.php');
