<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Guzzle\Http\Exception\ClientErrorResponseException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\BadResponseException;

class CopyUrlFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Copy:UrlFiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copies Url Files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
   

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    // function __construct() {
    //     $this->client = new \GuzzleHttp\Client();
    // }
    public function handle()
    {
         // Ask user for the source and path
      
         $src = $this->ask("Please enter source file of url'\s.");
         $dst = $this->ask('Please enter the local destination path');

         $fileArray = $this->source_n_destination($src, $dst, [], 0);
         //get Url from file
      
        // dd($a);
        


    }
    public function source_n_destination($src, $dst, $list, $index){
        try{
            $urls =  $this->readfile($src);
            //Copy file from url to destination
            foreach($urls as $key=>$url){
    
            $file = $this->copyfile($url , $dst);
            if($file != 'null'){
                $success =1;
            }else{
                $success = 0;
            }
            $index++;
    
             // Creating an array of files information
             $listItem = $this->list_item($index,$url,$dst,$file,$success);
            print_r($listItem);
             array_push($list, $listItem);// It inserts one or two values at the end of the array above
    
            }

        }catch(Exception $e){


        }
       
    }
    public function readfile($src){
        $handle = fopen($src, "r");
        $contents = fread($handle, filesize($src));
        $urls = explode(PHP_EOL, $contents);
        fclose($handle);
        return $urls;
    }
    public function copyfile($url, $dst){
                    // Initialize the cURL session 
                    $ch = curl_init($url); 
                    
                    // file will be save 
                    $dir = $dst.'\\';                    
                    // Gets the basename of an url
                    $file_name = basename($url); 
                    
                    // Save file into file location 
                    $save_file_loc = $dir . $file_name; 

                    // Open file  
                    $fp = fopen($save_file_loc, 'wb');  
                    // It set an option for a cURL transfer 
                    curl_setopt($ch, CURLOPT_FILE, $fp); 
                    curl_setopt($ch, CURLOPT_HEADER, 0); 
                    
                    // Perform a cURL session 
                    curl_exec($ch); 
                    
                    // Closes a cURL session and frees all resources 
                    curl_close($ch); 
                    
                    // Close file 
                    fclose($fp); 
                    $this->info('Success:'. $file_name); 
                    return $file_name;

    }
    public function list_item($index,$url,$dst,$file,$success){
        $listItem = [
            "sn" => $index,
            "source" => $url."/".$file,
            "destination" => $dst."/".$file,
            "status" => $success ? "Success" : "Failure"
        ];
        return $listItem;
    }

}
