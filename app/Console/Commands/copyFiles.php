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
            foreach(scandir($src, 1) as $file)
            {
                if(( $file != '.') && ($file != '..'))// Excluding "." and ".."
                {
                   
                    if( is_dir($src . '/' . $file)){
                        // dd('here');
                        // recursively calling custom copy funtion for subdirectory
                    $list=   $this->source_n_destination($src . '/' . $file, $dst. '/' . $file, $list, $index );// Reads the list of files inside the folder recursively
                     $index = count($list);
                      
                    }
                    else{    
                    // Deletes the file that are to be copied in a dir that are already exist                    
                      $this->delete_duplicate($dst,$file);
                     // copies the file from source to destination and assign success value as true or false                           
                     $success= $this->copy_files($src, $dst,$file);
                    $index++;
                    //  dd($file);
                    
                        //   print_r('...Sucess'); 
                    // Creating an array of files information
                        $listItem = $this->list_item($index,$src,$dst,$file,$success);
                      
                        array_push($list, $listItem);// It inserts one or two values at the end of the array above
                    }                            
                }
            }
            //close the source directory
            closedir($dir);

        }catch(Exception $e){
            // In case of failure creating an array
            $this->warn('Failure:');
            $listItem = $this->list_item($index,$src,$dst,$file,$success=0);
            array_push($list, $listItem);
        }      
                                
      
        
        return $list;       
    }

 
     
    public function handle()
    {   // Ask user for the source and path
      
        $src = $this->ask('Please enter source path.');
        $dst = $this->ask('Please enter the destination path');
        
        $fileArray = $this->source_n_destination($src, $dst, [], 0);
        // dd($fileArray);
        
      $this->write_to_csv($fileArray);
        
    }
    public function delete_duplicate($dst,$file)
    {
        if( file_exists($dst . '/' . $file)){  
            unlink($dst . '/' . $file);    
            // print_r('..file-ext');                  
        }
    }

    public function  copy_files($src, $dst,$file)
    {
        $success = copy($src . '/' . $file, $dst . '/' . $file);   
        $this->info('Success:'. $file); 
        return $success;

    }
    public function list_item($index,$src,$dst,$file,$success){
        $listItem = [
            "sn" => $index,
            "source" => $src."/".$file,
            "destination" => $dst."/".$file,
            "status" => $success ? "Success" : "Failure"
        ];
        return $listItem;
    }

    public function write_to_csv($fileArray){
          // $csv_dest = $this->ask('Please enter source path where you want your csv file to be downloaded');
        //Copies the array into csv file and make available in Desktop
        $fp = fopen(public_path('storage\file.csv'), 'w');
        fputcsv($fp, ["SN", "Source", "Destination", "Status"]);

        foreach ($fileArray as $fields){
            fputcsv($fp, $fields);
        }
        fclose($fp);
        // dd($fileArray);
    }


}
