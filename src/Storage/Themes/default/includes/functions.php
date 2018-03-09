<?php

HoneyCMS\Application::getThemesManager()->addVariable("ciao", "<h1>asdasdsad</h1>");
HoneyCMS\Application::getRouter()->get("/", "index.galaxy");
HoneyCMS\Application::getRouter()->get("/test", "test.php");