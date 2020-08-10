<?php

namespace BenMajor\APIDocs;

use Symfony\Component\Yaml\Yaml;

require 'vendor/autoload.php';
require 'src/autoload.php';

# Grab the YAML:
$config = Yaml::parseFile('config.yaml');

# Create our new app:
$app = new App($config);

# Run the app:
$app->run();