<?php 
class block_er_anfrage   extends block_base 
{
    var  $srvpath = "";
	
    function init() 
    {
       if   ( isset( $_SERVER[ 'SERVER_NAME' ] ) AND ( $_SERVER[ 'SERVER_NAME' ] )   == 'localhost' )   
            { $this->srvpath = 'http://localhost/haw/ERanfrageAPP/index.php';                       /* Dev-Server */   }  
       else { $this->srvpath = 'http://lernserver.el.haw-hamburg.de/haw/ERanfrageAPP/index.php';    /* Live-Server */  }  
	   
       # $this->srvpath = 'http://localhost/haw/er_anfrage/index.php';
	   
       $this->title =  'EMIL-RAUM-Anfrage';
    }

    function hide_header() 	            {  return false;  }
    function instance_allow_multiple()  {  return false;  }
    function has_config()               {  return false;   }	

    function applicable_formats() 
    {
      return array
      (
        'all' => true ,
        'course*' => false
       );
    }
    function get_content() 
    {
        global $USER;    
        global $COURSE;   
        global $CFG;  

        $isEditor = false; 

        $context = context_course::instance($COURSE->id);               /* hat der Nutzer Editierrechte ?  */
        if ( has_capability( 'moodle/course:update', $context )  )     {  $isEditor = true; }   
     
        $isNoStudi = 0 ;                                                        /* ist der Nutzer NICHT Student ?? (= Mitarbeiter, Externer) */
        if ( isset( $USER->phone2 ) )    
        {
          if      ( stristr( $USER->phone2, 'Student'    ) || stristr( $USER->phone2, 'Studentin' ) )  { $isNoStudi = 0; }
          else if ( stristr( $USER->phone2, 'Mitarbeiter') || stristr( $USER->phone2, 'Externer'  ) )  { $isNoStudi = 1; }
        }

        /* Ermittelt ob die aktuelle Seite 'meine Startseite' ist */
        if  (  strpos($_SERVER['SCRIPT_FILENAME'], '/my/') != '' ||   strpos($_SERVER['SCRIPT_FILENAME'], '\my\\') != '' )  { $currendSideIsMyEMIL = true;   $frameHeight = 120; }
        else                                                                                                                { $currendSideIsMyEMIL = false;  $frameHeight = 250; }
        
        /* Ermittelt, ob der aktuelle Kurs in einer Kurskategorie liegt, die in der Kurskategorieliste vorkommt */
        $visibleCourseCat = array(); 
        if (! empty($this->config->text))  {  $visibleCourseCat = explode("," , $this->config->text);  } # Nur in Kursen, aus einer der Kurskategorien, die in der Block-Config definiert wurden 
        else                               {  $visibleCourseCat = array();                             } # wird dieser Block auch angezeigt
  
        #if ( $isEditor && in_array( $COURSE->category  , $visibleCourseCat ) || $currendSideIsMyEMIL)
        {                      
        if ( $currendSideIsMyEMIL ) { $cid =  '-1'; }
        else                        { $cid =  $COURSE->id; } 
          
		if ( !isset( $COURSE->id        ) ) { $COURSE->id        =  ''; }
		if ( !isset( $COURSE->category  ) ) { $COURSE->category  =  ''; }
		if ( !isset( $COURSE->sortorder ) ) { $COURSE->sortorder =  ''; }
		if ( !isset( $COURSE->fullname  ) ) { $COURSE->fullname  =  ''; }
		if ( !isset( $COURSE->shortname ) ) { $COURSE->shortname =  ''; }
  
		if ( !isset( $USER->username    ) ) { $USER->username    =  ''; } 
		if ( !isset( $USER->firstname   ) ) { $USER->firstname   =  ''; }
		if ( !isset( $USER->lastname    ) ) { $USER->lastname    =  ''; }
		if ( !isset( $USER->email       ) ) { $USER->email       =  ''; }
		if ( !isset( $USER->address     ) ) { $USER->address     =  ''; }
		if ( !isset( $USER->institution ) ) { $USER->institution =  ''; }
		if ( !isset( $USER->department  ) ) { $USER->department  =  ''; }
		if ( !isset( $USER->id          ) ) { $USER->id          =  ''; }
										
		$idm = ""; 
		$idm .= "?uun=" .rawurlencode( base64_encode( $USER->username    ));
		$idm .= "&ufn=" .rawurlencode( base64_encode( $USER->firstname   ));
		$idm .= "&uln=" .rawurlencode( base64_encode( $USER->lastname    ));
		$idm .= "&uem=" .rawurlencode( base64_encode( $USER->email       ));
		$idm .= "&ufa=" .rawurlencode( base64_encode( $USER->address     ));
		$idm .= "&uin=" .rawurlencode( base64_encode( $USER->institution ));
		$idm .= "&ude=" .rawurlencode( base64_encode( $USER->department  ));
		$idm .= "&uid=" .rawurlencode( base64_encode( $USER->id          ));
  
		$idm .= "&cid=" .rawurlencode( base64_encode( $cid               ));
		$idm .= "&cca=" .rawurlencode( base64_encode( $COURSE->category  ));
		$idm .= "&cso=" .rawurlencode( base64_encode( $COURSE->sortorder ));
		$idm .= "&cfn=" .rawurlencode( base64_encode( $COURSE->fullname  ));
		$idm .= "&csn=" .rawurlencode( base64_encode( $COURSE->shortname ));
		$idm .= "&svn=" .rawurlencode( base64_encode( sha1($_SERVER['SERVER_NAME']) ));

		$idm .= "&ist=$isNoStudi";  
				
		$btitle = 'Manage NEW EMIL Rooms';
		$btitle = 'Ihre EMIL Räume verwalten';
        
		$con   = "";
		$con  .= "<button style='width:100%;' id='create-user' onclick='return false;' >".$btitle."</button>";
		$con  .= "<div id='dialog-form2' title='". $btitle."'>";
		$con  .= "<iframe id='mrooms' name='mrooms' scrolling='auto' width='95%;' height='600px;' src='".  $this->srvpath.$idm. "&x=2'></iframe></div>";
		$con  .= '<link href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" rel="stylesheet" >';
		$con  .= '<script src="http://code.jquery.com/jquery-1.10.2.js"></script>';
		$con  .= '<script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>';
		$con  .= '<script> 
		$( function()        { var dialog, dialog = $( "#dialog-form2" ).dialog( { autoOpen: false, width: 850, modal: true, });
		$( "#create-user"   ).button().on( "click", function() { dialog.dialog( "open" );  });}); 
        </script>';
		$con  .= '</head>';

		$contentA = "";               
		if ( ( !$currendSideIsMyEMIL  ) && $isEditor && $isNoStudi )                        /* Block erscheint nur bei: */
		{
		$contentA .= "<iframe    scrolling    = \"auto\"  
						marginheight          = \"0\" 
						marginwidth           = \"0\" 
						frameborder           = \"0\" 
						width                 = \"100%\"    
						height                = \"".$frameHeight."px\" 
						src                   =".$this->srvpath.$idm.">
			   </iframe>";
		}
	
		else
		{
		}

		$con .= $contentA;

		if ( !isset ( $con ) OR  $cid == 1     )     { $con = '';        }
		if ( $this->content !== NULL  ) 	{ return $this->content; }
		
		$this->content = new stdClass;    
		$this->content->text = $con;    

		return $this->content;	
       }	
} 
}
?>