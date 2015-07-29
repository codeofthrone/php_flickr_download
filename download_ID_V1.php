#!/usr/bin/php
<?php

require_once( "phpFlickr.php" );

$filepath = $argv[1];

function photo_list ($tags , $page, $per_page){
    $o = new phpFlickr( '6791ccf468e1c2276c1ba1e0c41683a4' );
    $d = $o->photos_search( array(
                'user_id' => "$tags" ,
                'content_type' => 1 ,
                'sort' => 'date-posted-asc' ,
                'extras' => 'url_m,url_z',
                'page' => $page ,
                'per_page' =>  $per_page 
                )   
            );
    if($per_page >= 500 ){
        $per_page = 499;
    }else{
        $per_page -= 1 ;
    }
    for($index = 0 ; $index <= $per_page ; $index++){
        if(!empty( $d['photo'][$index]['url_m'] )){
            print_r( $d['photo'][$index]['url_m'] ."\n");
            $downfile =  $d['photo'][$index]['url_m'] ;
	    $dir = str_replace(" ","_",$tags);
            system( "wget $downfile -P $dir");
        }elseif(!empty( $d['photo'][$index]['url_z'] )){
            print_r( $d['photo'][$index]['url_z'] ."\n");
            $downfile =  $d['photo'][$index]['url_z'] ;
            $path = `pwd`;
	    $dir = str_replace(" ","_",$tags);
            system( "wget  $downfile -P $dir");
            #system( "wget $downfile ");
        }else{
            print ("orignal and large size is not available \n");
        }
    }
}


function total_photo ($tags  ){
    $o = new phpFlickr( '6791ccf468e1c2276c1ba1e0c41683a4' );
    $tags = trim( preg_replace('/\s\s+/',' ' , $tags ));	
    $d = $o->photos_search( array(
                'user_id' => $tags ,
                'content_type' => 1 ,
                'sort' => 'date-posted-asc' ,
                'extras' => 'url_m,url_z',
                'page' => 1 ,
                'per_page' => 500 
                )   
            );
    print_r ( "total page :".$d['pages'] );
    $total_page = $d['pages'] ;
    print_r ( "total photo :".$d['total'] );
    $total_photo = $d['total'] ;
    for( $page = 1 ; $total_page >= $page ; $page++ ){
        print ("page $page \n");
        if ($total_photo >= 500 ){
            $total_photo -= 500 ;
            photo_list ($tags , $page, 500) ;
        }else{
            photo_list ($tags , $page, $total_photo) ;
        }
    }
}





$handle = fopen($filepath, "r");
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        // process the line read.
	total_photo ($line) ;
	
    }
} else {
    // error opening the file.
    print "error openfile";
}



?>
