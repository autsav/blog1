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

     public function source_n_destination($src, $dst)
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
                            // recursively callign suston copy funtion for subdirectory
                        $this->source_n_destination($src . '/' . $file, $dst. '/' . $file );
                            
                        }
                        elseif( file_exists($dst . '/' . $file))
                            {
                                    
                              $this->show_failure($file);
                                // print_r($fail);
                                // exit();
                            }
                            else{
                                copy($src . '/' . $file, $dst . '/' . $file);
                            
                                $success =   $this->show_success($file);

                                
    
                                

                            }
                            // $list = array(
                            //     'SN' => 1,
                            //     'Source' =>$src ,
                            //     'Destination' =>$dst ,
                            //     'Status' => $success,
                            //     // 'Status' => $fail,
                            // );
                            // $fp = fopen('file.csv', 'w');
        
                            // foreach ($list as $fields){
                            //     fputcsv($fp, $fields);
                            // }
                            // fclose($fp);
                            

                        
                        
                            
                    }
                  

                    
                }
            }

    
        catch(Exception $e){
            $this->source_n_destination($src, $dst);
        }
        
                                    
      
            closedir($dir);
       
    }
     
    public function handle()
    { 
        $src = $this->ask('Please enter source path.');
        $dst = $this->ask('Please enter the destination path');
        $this->source_n_destination($src, $dst);
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
