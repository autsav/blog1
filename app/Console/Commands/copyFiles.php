<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage as FacadesStorage;
use Illuminate\Support\Facades\Storage;

class CopyFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Copy:Files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copies files from one folder to another';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     parent::__construct();
    // }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

     public function source_n_destination($src, $dst, $list, $index)
     {
        
        try{
            // open the source directory
            $dir = opendir($src);

            //Make the destination directory if not exist
            @mkdir($dst);

            //Loop through the file in source directory
            foreach(scandir($src) as $file)
            {
                if(( $file != '.') && ($file != '..'))
                {
                    if( is_dir($src . '/' . $file)){
                        // recursively calling custom copy funtion for subdirectory
                        $tempList = $this->source_n_destination($src . '/' . $file, $dst. '/' . $file, $list, $index );// Reads the list of files inside the folder recursively
                        $index += count($tempList); //Adds the count to previous index value
                        $list = array_merge($list, $tempList); //It merge one or more than one array into one                   
                    }
                    else{    
                    // Deletes the file that are to be copied in a dir are already exist                    
                        if( file_exists($dst . '/' . $file)){  
                            unlink($dst . '/' . $file);                     
                        }
                     // copies the file from source to destination and assign success value as true or false                           
                        $success = copy($src . '/' . $file, $dst . '/' . $file);   
                          $this->info('Success:'. $file); 
                    // Creating an array of files information
                        $listItem = [
                            "sn" => ++$index,
                            "source" => $src."/".$file,
                            "destination" => $dst."/".$file,
                            "status" => $success ? "Success" : "Failure"
                        ];
                        array_push($list, $listItem);// It inserts one or two values at the end of the array above
                    }                            
                }
            }
            //close the source directory
            closedir($dir);

        }catch(Exception $e){
            // In case of failure creting an array
            $this->warn('Failure:');

            $listItem = [
                "sn" => ++$index,
                "source" => $src,
                "destination" => $dst,
                "status" => "Failure"
            ];
            array_push($list, $listItem);
        }      
                                
      
        
        return $list;       
    }
     
    public function handle()
    {   // Ask user for the source and path
        $src = $this->ask('Please enter source path.');
        $dst = $this->ask('Please enter the destination path');

        $fileArray = $this->source_n_destination($src, $dst, [], 0);
      
        
        // $csv_dest = $this->ask('Please enter source path where you want your csv file to be downloaded');
        //Copies the array into csv file and make available in Desktop
        $fp = fopen('C:\Users\utsab\Desktop\file.csv', 'w');
        fputcsv($fp, ["SN", "Source", "Destination", "Status"]);

        foreach ($fileArray as $fields){
            fputcsv($fp, $fields);
        }
        fclose($fp);
        // dd($fileArray);
        
    }
    public function show_success($file)
    {
         return  $this->info('Success:' . $file);
    }

    public function  show_failure($file)
    {
       return $this->warn('Failure:' . $file);

    }


}
