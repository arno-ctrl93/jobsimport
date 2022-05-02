<?php

/************************************
Entry point of the project.
To be run from the command line.
************************************/

define('SQL_HOST', 'mariadb');
define('SQL_USER', 'root');
define('SQL_PWD', 'root');
define('SQL_DB', 'cmc_db');
define('RESSOURCES_DIR', __DIR__ . '/../resources/');


function __autoload(string $classname) {
    include_once(__DIR__ . '/' . $classname . '.php');
}


echo sprintf("Starting...\n");

$arrayofflux = [RESSOURCES_DIR . 'jobteaser.json', RESSOURCES_DIR . 'regionsjob.xml'];

/* import jobs from regionsjob.xml */
$jobsImporter = new JobsImporter(SQL_HOST, SQL_USER, SQL_PWD, SQL_DB, $arrayofflux);
$count = $jobsImporter->importJobs();

echo sprintf("> %d jobs imported.\n", $count);


/* list jobs */
$jobsLister = new JobsLister(SQL_HOST, SQL_USER, SQL_PWD, SQL_DB);
$jobs = $jobsLister->listJobs();

echo sprintf("> all jobs (%d):\n", count($jobs));
foreach ($jobs as $job) {
    echo sprintf(" %d: %s - %s - %s\n", $job['id'], $job['reference'], $job['title'], $job['publication']);
}


echo sprintf("Done.\n");
