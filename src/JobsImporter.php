<?php

class JobsImporter
{
    private $db;

    private $files;

    public function __construct($host, $username, $password, $databaseName, $files)
    {
        
        $this->files = $files;

        //var_dump($this->files);
        //var_dump($this->file);
        
        /* connect to DB */
        try {
            $this->db = new PDO('mysql:host=' . $host . ';dbname=' . $databaseName, $username, $password);
        } catch (Exception $e) {
            die('DB error: ' . $e->getMessage() . "\n");
        }
    }

    public function importJobs()
    {
        $count = 0;
        /* remove existing items */
        $this->db->exec('DELETE FROM job');

        /* parse XML file */
        //var_dump($this->files);
        
        foreach ($this->files as $f)
        {
            //var_dump($this->file);
            //do a if with regex expression comparaison on file to know if the end is .xml or .json   
            if (preg_match('/\.xml$/', $f))
            {
                //print("XML file\n");
                //var_dump($f);
                $xml = simplexml_load_file($f);
                //$xml = simplexml_load_file($this->file);
                foreach ($xml->item as $item){
                    //var_dump($item);
                    $this->db->exec('INSERT INTO job (reference, title, description, url, company_name, publication) VALUES ('
                        . '\'' . addslashes($item->ref) . '\', '
                        . '\'' . addslashes($item->title) . '\', '
                        . '\'' . addslashes($item->description) . '\', '
                        . '\'' . addslashes($item->url) . '\', '
                        . '\'' . addslashes($item->company) . '\', '
                        . '\'' . addslashes($item->pubDate) . '\')'
                    );
                    $count++;
                }
            }
            else
            {
                //print("JSON file\n");
                $json = file_get_contents($f);
                $json = json_decode($json, true);
                //var_dump($json);
                foreach ($json["offers"] as $item){
                    //print("1\n");
                    //var_dump($item["reference"]);
                    //var_dump($item["title"]);
                    //var_dump($item["description"]);
                    //var_dump($item["link"]);
                    //var_dump($item["companyname"]);
                    //var_dump($item["publishedDate"]);
                    //echo date('Y-m-d H:i:s', strtotime($item["publishedDate"]));
                    $result = $this->db->exec('INSERT INTO job (reference, title, description, url, company_name, publication) VALUES ('
                        . '\'' . addslashes($item["reference"]) . '\', '
                        . '\'' . addslashes($item["title"]) . '\', '
                        . '\'' . addslashes($item["description"]) . '\', '
                        . '\'' . addslashes($item["link"]) . '\', '
                        . '\'' . addslashes($item["companyname"]) . '\', '
                        . '\'' . addslashes(date('Y-m-d H:i:s', strtotime($item["publishedDate"]))) . '\')'
                    );
                    //var_dump($result);
                    $count++;
                }
            }
        }

        
        //$xml = simplexml_load_file($this->file);
        

        /* import each item */
        /*$count = 0;
        foreach ($xml->item as $item) {
            $this->db->exec('INSERT INTO job (reference, title, description, url, company_name, publication) VALUES ('
                . '\'' . addslashes($item->ref) . '\', '
                . '\'' . addslashes($item->title) . '\', '
                . '\'' . addslashes($item->description) . '\', '
                . '\'' . addslashes($item->url) . '\', '
                . '\'' . addslashes($item->company) . '\', '
                . '\'' . addslashes($item->pubDate) . '\')'
            );
            $count++;
        }*/
        //var_dump($count);
        return $count;
    }
}
