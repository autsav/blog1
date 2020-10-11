<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage as FacadesStorage;
use Illuminate\Support\Facades\Storage;

class copyFiles extends Command
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
        // open the source dir
        try{
            $dir = opendir($src);

            //Make the destination directory if not exist
            @mkdir($dst);

            //Loop through the file in source directory
            foreach(scandir($src) as $file)
            {
                if(( $file != '.') && ($file != '..'))
                {
                    if( is_dir($src . '/' . $file)){
                        // recursively callign custom copy funtion for subdirectory
                        $tempList = $this->source_n_destination($src . '/' . $file, $dst. '/' . $file, $list, $index );
                        $index += count($tempList);
                        $list = array_merge($list, $tempList);                    
                    }
                    else{                        
                        if( file_exists($dst . '/' . $file)){  
                            unlink($dst . '/' . $file);                     
                        }
                                                
                        $success = copy($src . '/' . $file, $dst . '/' . $file);    

                        $listItem = [
                            "sn" => ++$index,
                            "source" => $src."/".$file,
                            "destination" => $dst."/".$file,
                            "status" => $success ? "Success" : "Failure"
                        ];
                        array_push($list, $listItem);
                    }                            
                }
            }
            
            closedir($dir);

        }catch(Exception $e){
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
    { 
        $src = $this->ask('Please enter source path.');
        $dst = $this->ask('Please enter the destination path');
        $fileArray = $this->source_n_destination($src, $dst, [], 0);
      
        
      
        $fp = fopen('file.csv', 'w');
        fputcsv($fp, ["SN", "Source", "Destination", "Status"]);

        foreach ($fileArray as $fields){
            fputcsv($fp, $fields);
        }
        fclose($fp);
        dd($fileArray);
        
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
