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

                    $fileArray = $this->source_n_destination($src, $dst,[], [], 0,0,0);
                    $this->write_success_csv($fileArray);
    }
    public function source_n_destination($src, $dst, $list,$list1, $index,$index1,$execution_time){
      
                try{
                    $urls =  $this->readfile($src);
                    //Copy file from url to destination
                    foreach($urls as $key=>$url){       
                        //**Records Time **            
                    $time_start = microtime(true);
                    $file = $this->copyfile($url , $dst);                
                    $time_end = microtime(true);
                    $execution_time = ($time_end - $time_start);
                        //**Records Time **    
                    if($file == 1){
                        $success =$file;                      
                        $this->info('Success:'); 
                        $index++;
                        // Creating an array of files information
                        $listItem = $this->list_item($index,$url,$dst,$success, $execution_time);
                        array_push($list, $listItem);// It inserts one or two values at the end of the array above
                }else{   
                    $success =$file;                
                    $this->warn('Failure:'); 
                    $index1++;    
                    // Creating an array of files information
                    $listItem1 = $this->list_failure_item($index1,$url,$dst, $execution_time);
                    array_push($list1, $listItem1);
                    $fleArray1 = $list1;
                    $this->write_failure_csv($fleArray1);               
                }      
            }          
        }catch(Exception $e){
                    // In case of failure creating an array
                    $this->warn('Failure:');
                    $listItem1 = $this->list_failure_item($index1,$url,$dst, $execution_time);
                    array_push($list1, $listItem1);
                    $fleArray1 = $list1;
                    $this->write_failure_csv($fleArray1);
        }     
        return $list;
    }
    public function readfile($src){
                    $handle = fopen($src, "r");
                    $contents = fread($handle, filesize($src));
                    $urls = explode(PHP_EOL, $contents);
                    fclose($handle);
                    return $urls;
    }
    public function copyfile($url, $dst){
                    $status = $this-> does_url_exists($url); 
                   if($status){
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
                   return 1;
                   }else{
                       return 0;
                   }
    }
    public function does_url_exists($url){
                    $ch = curl_init($url); 
                    curl_setopt($ch, CURLOPT_NOBODY, true);
                    curl_exec($ch);
                    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                
                    if ($code == 200) {
                        $status = true;
                    } else {
                        $status = false;
                    }
                    curl_close($ch);
                    return $status;
    }
    public function list_item($index,$url,$dst,$success, $execution_time){
                    $listItem = [
                        "sn" => $index,
                        "source" => $url,
                        "destination" => $dst."/",
                        "status" => "Success",
                        "time" => $execution_time
                    ];
                    return $listItem;
    }

    public function list_failure_item($index1,$url,$dst, $execution_time){
                    $listItem1 = [
                        "sn" => $index1,
                        "source" => $url,
                        "destination" => $dst."/",
                        "status" => "Failure",
                        "time" => $execution_time
                    ];
                    return $listItem1;
    }
    public function write_success_csv($fileArray){
                    //Copies the array into csv file and make available in Desktop
                    $fp = fopen(public_path('storage\file.csv'), 'w');
                    fputcsv($fp, ["SN", "Source", "Destination", "Status","Time"]);

                    foreach ($fileArray as $fields){
                        fputcsv($fp, $fields);
                    }
                    fclose($fp);
  }

  public function write_failure_csv($fileArray){
                    //Copies the array into csv file and make available in Desktop
                    $fp = fopen(public_path('storage\failure.csv'), 'w');
                    fputcsv($fp, ["SN", "Source", "Destination", "Status","Time"]);

                    foreach ($fileArray as $fields){
                        fputcsv($fp, $fields);
                    }
                    fclose($fp);

                }

}
