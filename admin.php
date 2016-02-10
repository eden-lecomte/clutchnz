<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "clutchgamingnz@gmail.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "29f98d" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'85A0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WANEQxmmMLQii4lMEWlgCGWY6oAkFtAq0sDo6BAQgKouhLUh0EEEyX1Lo6YuXboqMmsakvtEpjA0uiLUQc0DioWii4kA1QWg2cHaytoQgOIW1gBGoL0BKG4eqPCjIsTiPgBB3s1W582FYgAAAABJRU5ErkJggg==',
			'F685' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUMDkMQCGlhbGR0dHRhQxEQaWRsC0cUagOpcHZDcFxo1LWxV6MqoKCT3BTSIAs1zAKpGNc8VbAK6WKCDCIZbHAJQ3QdyM8NUh0EQflSEWNwHABNazGgFCOteAAAAAElFTkSuQmCC',
			'B3A7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgNYQximMIaGIIkFTBFpZQhlaBBBFmtlaHR0dEAVm8LQytoQAIQI94VGrQpbuipqZRaS+6DqWhnQzHMNBcqgizUEBDCguYW1IdAB3c3oYgMVflSEWNwHAID1zgs+4iINAAAAAElFTkSuQmCC',
			'5A81' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGVqRxQIaGEMYHR2mooqxtrI2BIQiiwUGiDQ6OjrA9IKdFDZt2sqs0FVLUdzXiqIOKiYa6toQgGovUB26mMgUTL2sQHsdQhlCAwZB+FERYnEfAK/RzNTHOVriAAAAAElFTkSuQmCC',
			'79FB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA0MdkEVbWVtZGxgdAlDERBpdgWIiyGJTIGIByO6LWro0NXRlaBaS+xgdGANd0cxjbWDAME+kgQVDLKAB0y0BDUA3NzCiunmAwo+KEIv7AFaTyr4MBT4jAAAAAElFTkSuQmCC',
			'25CA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nGNYhQEaGAYTpIn7WANEQxlCHVqRxUSmiDQwOgRMdUASC2gVaWBtEAgIQNbdKhLCClQpguy+aVOXLl21MmsasvsCGBpdEerAkNEBLBYaguyWBhGgmCCKOqCtrYwOgShioaGMIQyhjihiAxV+VIRY3AcAyW/K6mcVAHkAAAAASUVORK5CYII=',
			'1090' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGVqRxVgdGEMYHR2mOiCJiTqwtrI2BAQEoOgVaXRtCHQQQXLfyqxpKzMzI7OmIbkPpM4hBK4OIdaALsbayohhBxa3hGC6eaDCj4oQi/sAyjvIvsss53YAAAAASUVORK5CYII=',
			'A2A5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2QsQ3DMAwEyYIbKPtQhfo3YBbRNHShDeQR1GhKu6SQlAlgfnf4Bw6k+XFOT8pf/Fh5p86GwATSyFhjL/V05JwXhkZH8a1o8KtjjjHftQa/u9fF4SlszQhiK0NjFd90ZeL3FljYy4rj1Af874f54ncBAfPMreH3BzYAAAAASUVORK5CYII=',
			'2615' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QsRHAIAhFoXADsg9NegpJ4TRauIFxhzhltONMyuRO6B587h3QHhVhpf7Fzwl6KKhiGBWXwSPbPcmUcGKQKfbsztav1qPVKwTrJ1uG0u+aLDIlnpiLg/WJdYluZMX6qXYT5ZMX+N+H/eJ3A2EVykEj53wUAAAAAElFTkSuQmCC',
			'643E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYWhlDGUMDkMREpjBMZW10dEBWF9DCEMrQEIgq1sDoyoBQB3ZSZNTSpaumrgzNQnJfyBSRVgZ081pFQx3QzWtlaEW3A+iWVnS3YHPzQIUfFSEW9wEAe2nK4+sF+4gAAAAASUVORK5CYII=',
			'85AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQxmmMIY6IImJTBFpYAhldAhAEgtoFWlgdHR0EEFVF8LaEAgTAztpadTUpUtXRWZNQ3KfyBSGRleEOqh5QLFQdDERDHUiU1hbQXYgu4U1gBFkL4qbByr8qAixuA8ACaHMWnti0K4AAAAASUVORK5CYII=',
			'E113' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMYAhimMIQ6IIkFNDAGMIQwOgSgiLEGMIYwNIigiIH1NgQguS80alXUqmmrlmYhuQ9NHYoYNvMwxVDdEhrCGsoY6oDi5oEKPypCLO4DANoqyzhhf8+fAAAAAElFTkSuQmCC',
			'EB6B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGUMdkMQCGkRaGR0dHQJQxRpdGxwdRNDUsTYwwtSBnRQaNTVs6dSVoVlI7gOrw2peILp52MQw3ILNzQMVflSEWNwHAO0NzNWnA/XrAAAAAElFTkSuQmCC',
			'665F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHUNDkMREprC2sjYwOiCrC2gRacQQaxBpYJ0KFwM7KTJqWtjSzMzQLCT3hUwRbWVoCETV2yrS6IBFzBVNDOQWRkdHFDGQmxlCUd0yUOFHRYjFfQAlkcm3LkppZAAAAABJRU5ErkJggg==',
			'CDB8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WENEQ1hDGaY6IImJtIq0sjY6BAQgiQU0ijS6NgQ6iCCLNQDFEOrATopaNW1lauiqqVlI7kNThxBDNw+LHdjcgs3NAxV+VIRY3AcAGDbOiJaJdKcAAAAASUVORK5CYII=',
			'244F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYWhkaHUNDkMREpjBMZWh1dEBWF9DKEMowFVWMoZXRlSEQLgZx07SlS1dmZoZmIbsvQKSVtRFVL6ODaKhraCCKGGsD2C0oYiJYxEJDMcUGKvyoCLG4DwADi8mSNnmougAAAABJRU5ErkJggg==',
			'C1EC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7WEMYAlhDHaYGIImJtDIGsDYwBIggiQU0sgLFGB1YkMWAakBiyO6LAqKloSuzkN2Hpg63WCMDhh0irQwYbmENYQ1Fd/NAhR8VIRb3AQCw8siWYpwk3wAAAABJRU5ErkJggg==',
			'86E6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHaY6IImJTGFtZW1gCAhAEgtoFWlkbWB0EEBRJ9IAEkN239KoaWFLQ1emZiG5T2SKKNA8RgzzXIF6RQiIYXMLNjcPVPhREWJxHwDZuMs6z6XtFwAAAABJRU5ErkJggg==',
			'ED6C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGaYGIIkFNIi0Mjo6BIigijW6Njg6sGCIMToguy80atrK1Kkrs5DdB1bn6OjAgKE3EKsYmh0YbsHm5oEKPypCLO4DAFkkzSZW7atTAAAAAElFTkSuQmCC',
			'FEA8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMEx1QBILaBBpYAhlCAhAE2N0dHQQQRNjbQiAqQM7KTRqatjSVVFTs5Dch6YOIRYaiMU8bGLoekVDgWIobh6o8KMixOI+ABx2zf6sB4yCAAAAAElFTkSuQmCC',
			'F2F9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDA6Y6IIkFNLC2sjYwBASgiIk0ujYwOoigiDEgi4GdFBq1aunS0FVRYUjuA6qbAjRvKpreAKBYA6oYowNQDM0O1gZMt4iGugLNQ3bzQIUfFSEW9wEAnlLMh8sVRrAAAAAASUVORK5CYII=',
			'BD2F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgNEQxhCGUNDkMQCpoi0Mjo6OiCrC2gVaXRtCEQVmyLS6IAQAzspNGrayqyVmaFZSO4Dq2tlxDDPYQoWsQBGdDuAOlHFQG5mDUV1y0CFHxUhFvcBAI5yy3zUx8xDAAAAAElFTkSuQmCC',
			'B998' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGaY6IIkFTGFtZXR0CAhAFmsVaXRtCHQQQVEHEguAqQM7KTRq6dLMzKipWUjuC5jCGOgQEoBmHkOjA7p5rSyNjhh2YLoFm5sHKvyoCLG4DwCMPs4OstKU5AAAAABJRU5ErkJggg==',
			'2EA3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQxmmMIQ6IImJTBFpYAhldAhAEgtoFWlgdHRoEEHWDRRjbQhoCEB237SpYUtXRS3NQnZfAIo6MGR0AIqFBqCYx9oAUYcsJgIWC0RxS2ioaChQHYqbByr8qAixuA8AcKrMn6cCnrQAAAAASUVORK5CYII=',
			'9594' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCGRoCkMREpog0MDo6NCKLBbSKNLACSTSxEKDYlAAk902bOnXpysyoqCgk97G6MjQ6hAQ6IOtlaAWKNQSGhiCJCbSKNDoCXYLqFtZWoFtQxFgDGEPQ3TxQ4UdFiMV9AAxHzaoM8/xdAAAAAElFTkSuQmCC',
			'6731' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WANEQx1DGVqRxUSmMDS6NjpMRRYLaGFodGgICEURawDqa3SA6QU7KTJq1bRVU1ctRXZfyBSGACR1EL2tjA4gElWMtQFdTGSKSAMrml7WAJEGxlCG0IBBEH5UhFjcBwDOA81sSZWyuwAAAABJRU5ErkJggg==',
			'C7D2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2QMQ6AMAhF6cAN6n1Y3GtSlp6GDr2BegOXntI/0uioSfkJwwuEF6g/ymim/OLHedFV6RDHYqO6VknJsVTBbJPomVFj9Oj8Su/nhV6cH+YS5qoMu0HAGg032MB2GlyiMVxGZzANmif434d58bsBWhHNvDQcYpgAAAAASUVORK5CYII=',
			'5A53' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7QkMYAlhDHUIdkMQCGhhDWBsYHQJQxFhbWYG0CJJYYIBIo+tUkBzCfWHTpq1MzcxamoXsvlaRRgegKmTzGFpFQ0FiyOYFANW5oomJTBFpdHR0RHELK9Beh1AGFDcPVPhREWJxHwDiXc23ECr/pQAAAABJRU5ErkJggg==',
			'D506' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMEx1QBILmCLSwBDKEBCALNYq0sDo6OgggCoWwtoQ6IDsvqilU5cuXRWZmoXkvoBWhkbXhkA088BiDiKo5jU6Au1AEZvC2orultAAxhB0Nw9U+FERYnEfAMGFzYJulTcqAAAAAElFTkSuQmCC',
			'2F08' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQx2mMEx1QBITmSLSwBDKEBCAJBbQKtLA6OjoIIKsGyjG2hAAUwdx07SpYUtXRU3NQnZfAIo6MGR0AIkFopjH2oBph0gDpltCQ4FiaG4eqPCjIsTiPgByQcuR2um5TgAAAABJRU5ErkJggg==',
			'2302' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2Quw2AMAxEjyIbeKCkoHeRUDCNU2SDwAY0mRICzUlQgoSve/LnyWi3Mvwpn/g5dREViycmVQoSVIlpQQ4heOHpguJMTdhvbdPW5iPkp2df5huDRx77VnaxfsNXZmKXC7OUuvOQ4g/+92Ie/HaBmMubuZQPuwAAAABJRU5ErkJggg==',
			'562E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGUMDkMQCGlhbGR0dHRhQxEQaWRsCUcQCA0RAJEwM7KSwadPCVq3MDM1Cdl+raCtDKyOKXoZWkUaHKahiASCxAFQxkSlAtzigirEGMIawhgaiuHmgwo+KEIv7APH0yXIlIOyiAAAAAElFTkSuQmCC',
			'FED6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDGaY6IIkFNIg0sDY6BASgizUEOghgEUN2X2jU1LClqyJTs5DcB1WH1TwRYsQw3ILp5oEKPypCLO4DANOQzZYQa399AAAAAElFTkSuQmCC',
			'249A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WAMYWhlCgRhJTGQKw1RGR4epDkhiAUBVrA0BAQHIulsZXVkbAh1EkN03benSlZmRWdOQ3Rcg0soQAlcHhowOoqEODYGhIchuAZnYgKpOBCTm6IgiFgpybygjithAhR8VIRb3AQAQMMpcKhAc5AAAAABJRU5ErkJggg==',
			'958A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCGVqRxUSmiDQwOjpMdUASC2gVaWBtCAgIQBULYXR0dBBBct+0qVOXrgpdmTUNyX2srgyNjgh1ENjK0OjaEBgagiQm0CoCEkNRJzKFtZURTS9rAGMIQygjqnkDFH5UhFjcBwAAIssOKtblmQAAAABJRU5ErkJggg==',
			'DB4C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQxgaHaYGIIkFTBFpZWh1CBBBFmsVAapydGBBFWtlCHR0QHZf1NKpYSszM7OQ3QdSx9oIVwc3zzU0EEPMoRHNDpBbGlHdgs3NAxV+VIRY3AcArXLOMmaJliwAAAAASUVORK5CYII=',
			'1B1A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQximMLQii7E6iLQyhDBMdUASE3UQaXQMYQgIQNELVDcFSCK5b2XW1LBV01ZmTUNyH5o6mFijwxTG0BBMMXR1GHpFQ0RDGEMdUcQGKvyoCLG4DwCLV8h0NrEt+gAAAABJRU5ErkJggg==',
			'0D69' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGaY6IImxBoi0Mjo6BAQgiYlMEWl0bXB0EEESC2gFiTHCxMBOilo6bWXq1FVRYUjuA6tzdJiKqTegQQTDjgAUO7C5BZubByr8qAixuA8AOgTMUttK1h8AAAAASUVORK5CYII=',
			'949F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYWhlCGUNDkMREpjBMZXR0dEBWFwBUxdoQiCbG6IokBnbStKlLl67MjAzNQnIfq6tIK0MIql6GVtFQBzTzBFoZWhnRxIBuaUV3C9TNqOYNUPhREWJxHwAfUMjI3Uz1EgAAAABJRU5ErkJggg==',
			'336F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7RANYQxhCGUNDkMQCpoi0Mjo6OqCobGVodG1AE5vC0MrawAgTAztpZdSqsKVTV4ZmIbsPpA6reYEExbC5BepmVL0DFH5UhFjcBwChpclEVsscJgAAAABJRU5ErkJggg==',
			'E263' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGUIdkMQCGlhbGR0dHQJQxEQaXRscGkRQxBiAYmAa7r7QqFVLl05dtTQLyX1A+Smsjg4NqOYxBLCCTEURY3TAFGNtQHdLaIhoqAOamwcq/KgIsbgPAEOLzdfEHyJvAAAAAElFTkSuQmCC',
			'A86C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGaYGIImxBrC2Mjo6BIggiYlMEWl0bXB0YEESC2hlbWUFmoDsvqilK8OWTl2Zhew+sDpHRwdke0NDQeYFoogFtELE0O1Ad0tAK6abByr8qAixuA8AG2rLqSO/aaAAAAAASUVORK5CYII=',
			'5A90' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGVqRxQIaGEMYHR2mOqCIsbayNgQEBCCJBQaINLo2BDqIILkvbNq0lZmZkVnTkN3XKtLoEAJXBxUTDXVoQBULAKpzRLNDZApQDM0trEB7HdDcPFDhR0WIxX0A85XM+WlrT3UAAAAASUVORK5CYII=',
			'F9B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGVqRxQIaWFtZGx2mOqCIiTS6NgQEBKCLNTo6iCC5LzRq6dLU0JVZ05DcF9DAGIikDirGADQvEE2MBYsd2NyC6eaBCj8qQizuAwAWoM6PKZsLjQAAAABJRU5ErkJggg==',
			'D4E3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgMYWllDHUIdkMQCpjBMZW1gdAhAFmtlCGUF0iIoYoyuILEAJPdFLQWC0FVLs5DcF9Aq0oqkDiomGuqKYR5DK4YdU0BiqG7B5uaBCj8qQizuAwDSgs2IQitgwwAAAABJRU5ErkJggg==',
			'B431' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgMYWhlDGVqRxQKmMExlbXSYiiLWyhAKJENR1TG6MjQ6wPSCnRQatXTpqqmrliK7L2CKSCuSOqh5oqEOIFNR7WhlQBebwtDKiqYX6ubQgEEQflSEWNwHAJL3zjNPS6CVAAAAAElFTkSuQmCC',
			'599E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGUMDkMQCGlhbGR0dHRhQxEQaXRsCUcQCA1DEwE4Km7Z0aWZmZGgWsvtaGQMdQlD1MrQyNDqgmRfQytLoiCYmMgXTLawBmG4eqPCjIsTiPgAeesphs31l2QAAAABJRU5ErkJggg==',
			'2B09' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANEQximMEx1QBITmSLSyhDKEBCAJBbQKtLo6OjoIIKsu1WklbUhECYGcdO0qWFLV0VFhSG7LwCkLmAqsl5GB5FG14aABmQx1gaQHQ4odog0YLolNBTTzQMVflSEWNwHAG9Ry6lo4DL5AAAAAElFTkSuQmCC',
			'9A8F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUNDkMREpjCGMDo6OiCrC2hlbWVtCEQTE2l0RKgDO2na1Gkrs0JXhmYhuY/VFUUdBLaKhrqimScANA9dTGQKpl7WAJFGh1BGVPMGKPyoCLG4DwAJzcnJ228HlwAAAABJRU5ErkJggg==',
			'D74B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgNEQx0aHUMdkMQCpjA0OrQ6OgQgi7UCxaY6OoigirUyBMLVgZ0UtXTVtJWZmaFZSO4DqgtgbUQ3j9GBNTQQzTzWBoZGNDumiIDFkPWGBoDFUNw8UOFHRYjFfQCdDs38+gLiZgAAAABJRU5ErkJggg==',
			'879D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7WANEQx1CGUMdkMREpjA0Ojo6OgQgiQW0MjS6NgQ6iKCqa2VFiIGdtDRq1bSVmZFZ05DcB1QXwBCCqjegldGBoQFdjLWBEcMOkQZGNLewBgBVoLl5oMKPihCL+wAqMstBpsX6KQAAAABJRU5ErkJggg==',
			'BC37' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgMYQ0EwBEksYApro2ujQ4MIslirSINDQwCq2BQgrxEkinBfaNS0VaumrlqZheQ+qLpWBjTzQDLoYkDTAhgw3OLogMXNKGIDFX5UhFjcBwDdfc7iAH0gTgAAAABJRU5ErkJggg==',
			'297D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2QwQmAMAxFGzAbdKC4QQp2iU6RHrqBdIMedEojIqToUdH82yM/POLWy4j7U17xQ4YJY4hkmJ+xOAnEhnHxmZR5295ZHk92ONXWUltStX4MgWboukAuE/cMZdBrPfOCBXXbusSozgKd81f/ezA3fhvshcrpc9yGiAAAAABJRU5ErkJggg==',
			'466E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpI37pjCGMIQyhgYgi4WwtjI6Ojogq2MMEWlkbUAVY50i0sDawAgTAztp2rRpYUunrgzNQnJfwBTRVlY080JDRRpdGwIdUN2CTQzTLVjdPFDhRz2IxX0AomfJi2VMsoAAAAAASUVORK5CYII=',
			'13F9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDA6Y6IImxOoi0sjYwBAQgiYk6MDS6AlWLoOhlAKqDi4GdtDJrVdjS0FVRYUjug6hjmIqmF2geQwMWMTQ7sLglBOhmoHnIbh6o8KMixOI+AFi6yGPEi0h+AAAAAElFTkSuQmCC',
			'6D1B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WANEQximMIY6IImJTBFpZQhhdAhAEgtoEWl0BIqJIIs1iDQ6TIGrAzspMmrayqxpK0OzkNwXMgVFHURvK0RMhIAY2C1oekFuZgx1RHHzQIUfFSEW9wEABO3MKEjfU2gAAAAASUVORK5CYII=',
			'F937' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMZQxhDGUNDkMQCGlhbWRsdGkRQxESAIgGYYmBRhPtCo5YuzZq6amUWkvsCGhgDgepaGVD0MoB0TkEVYwGJBTBguMXRAVUM7GYUsYEKPypCLO4DADBLzmFAFksqAAAAAElFTkSuQmCC',
			'BF25' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGUMDkMQCpog0MDo6OiCrC2gVaWBtCEQVA6pjaAh0dUByX2jU1LBVKzOjopDcB1bXytAggmYewxQsYgGMDiJodjA6MAQguy80AOiW0ICpDoMg/KgIsbgPAF9/zGizSL8JAAAAAElFTkSuQmCC',
			'65FB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA0MdkMREpog0sDYwOgQgiQW0QMREkMUaREKQ1IGdFBk1denS0JWhWUjuC5nC0OiKbl4rRAzFvFYRDDGRKayt6G5hDWAE2Yvi5oEKPypCLO4DAPt0yzs8/e7MAAAAAElFTkSuQmCC',
			'E209' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMYQximMEx1QBILaGBtZQhlCAhAERNpdHR0dBBBEWNodG0IhImBnRQatWrp0lVRUWFI7gOqm8LaEDAVTW8AK8hUFDFGB0ZHBzQ7WBvQ3RIaIhrqgObmgQo/KkIs7gMAEebM52WwqWQAAAAASUVORK5CYII=',
			'1E47' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1EQxkaHUNDkMRYHUQaGFodGkSQxERBYlNRxRhBYoEODQFI7luZNTVsZWbWyiwk94HUsTY6tDKg6WUNDZiCLsbQ6BCAKebogCwmGgJ2M4rYQIUfFSEW9wEA9srJbS0QiFwAAAAASUVORK5CYII=',
			'E568' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkNEQxlCGaY6IIkFNIg0MDo6BASgibE2ODqIoIqFsDYwwNSBnRQaNXXp0qmrpmYhuQ8o3+iKYR5QrCEQ3TwsYqyt6G4JDWEMQXfzQIUfFSEW9wEA3yfNmpGsfoYAAAAASUVORK5CYII=',
			'74AE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZWhmmMIYGIIu2MkxlCGV0YEAVC2V0dEQVm8LoytoQCBODuClq6dKlqyJDs5Dcx+gg0oqkDgxZG0RDXUNRxUSAtqCrC8AthurmAQo/KkIs7gMAHxnKGni8IXYAAAAASUVORK5CYII=',
			'ABC0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQxhCHVqRxVgDRFoZHQKmOiCJiUwRaXRtEAgIQBILaBVpZQWaIILkvqilU8OWrlqZNQ3JfWjqwDA0FGQeqhhQHVY70N0S0Irp5oEKPypCLO4DALe9zOtsPpzzAAAAAElFTkSuQmCC',
			'F48B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMZWhlCGUMdkMQCGhimMjo6OgSgioWyNgQ6iKCIMboiqQM7KTRq6dJVoStDs5DcF9Ag0oppnmioK4Z5DK2YdjBg0Yvp5oEKPypCLO4DAMqPy/wD/bvAAAAAAElFTkSuQmCC',
			'E2E2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nM3QsRHAIAhAUSzcwOxDCnsKaZwGi2xgRrBhypBOLimTO6V7x+k/QR9HYKX5pY9LKJHxxMlI4hEFiJylliVgcgZmIGnq46pjsGqd+myv233NvwFkdoCzgGbdW5S7xTdvnHnnssD/fTgvfRcOnszFXJvcWQAAAABJRU5ErkJggg==',
			'51A0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMYAhimMLQiiwU0MAYwhDJMdUARYw1gdHQICEASCwxgCGBtCHQQQXJf2LRVUUtXRWZNQ3ZfK4o6hFgoqlgAWF0Aih0iU8BiKG5hBepkBakeBOFHRYjFfQA6LMq61+xWDQAAAABJRU5ErkJggg==',
			'0D13' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQximMIQ6IImxBoi0MoQwOgQgiYlMEWl0DAHSSGIBrSKNDlOANJL7opZOW5k1bdXSLCT3oalDERNBswNdDOyWKahuAbmZMdQBxc0DFX5UhFjcBwDLX8zJI9LEWgAAAABJRU5ErkJggg==',
			'B32B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGUMdkMQCpoi0Mjo6OgQgi7UyNLo2BDqIoKhjaGUAigUguS80alXYqpWZoVlI7gOra2XEMM9hCiOqeSCxADQxkFscUPWC3MwaGoji5oEKPypCLO4DAFQXzD4CR3q6AAAAAElFTkSuQmCC',
			'03C6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB1YQxhCHaY6IImxBoi0MjoEBAQgiYlMYWh0bRB0EEASC2hlaGUFmoDsvqilq8KWrlqZmoXkPqg6FPOAYkDzGB1EsNghQsAt2Nw8UOFHRYjFfQCtfcsJvfrW5QAAAABJRU5ErkJggg==',
			'4A30' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37pjAEMIYytKKIhTCGsDY6THVAEgOKANUEBAQgibFOEWl0aHR0EEFy37Rp01ZmTV2ZNQ3JfQGo6sAwNFQ01KEhEEWMAaQOzQ6QmCuaW0BijuhuHqjwox7E4j4AckzNnb5YutcAAAAASUVORK5CYII=',
			'035F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDHUNDkMRYA0RaWYEyyOpEpjA0uqKJBbQytLJOhYuBnRS1dFXY0szM0Cwk94HUMTQEouttdEATg9iBKgZyC6OjI4oYyM0MoahuGajwoyLE4j4AMkPI6Ig+y28AAAAASUVORK5CYII=',
			'3B5E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RANEQ1hDHUMDkMQCpoi0sjYwOqCobBVpdEUXA6mbChcDO2ll1NSwpZmZoVnI7gOqY2gIxDDPAYuYK5oYyC2Mjo4oYiA3M4Qyorh5oMKPihCL+wB7HMoKJQkTJgAAAABJRU5ErkJggg==',
			'51E7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMYAlhDHUNDkMQCGhgDWIG0CIoYK4ZYYAADWCwAyX1h01ZFLQ1dtTIL2X2tYHWtKDZDxKYgiwVAxAKQxUSmgMQYHZDFgC4JBboZRWygwo+KEIv7AKfTyPwzQuqMAAAAAElFTkSuQmCC',
			'4EC1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpI37poiGMoQ6tKKIhYg0MDoETEUWYwSKsTYIhCKLsU4BiTHA9IKdNG3a1LClq1YtRXZfAKo6MAwNxRRjAKsTwBADugVNDOzm0IDBEH7Ug1jcBwD2oMtch3UkYAAAAABJRU5ErkJggg==',
			'A55B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDHUMdkMRYA0QaWIEyAUhiIlMgYiJIYgGtIiGsU+HqwE6KWjp16dLMzNAsJPcFtDI0OjQEopgXGgoRQzOv0RVDjLWV0dERRW9AK2MIQygjipsHKvyoCLG4DwB9M8v0PFpx+AAAAABJRU5ErkJggg==',
			'3691' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGVqRxQKmsLYyOjpMRVHZKtLI2hAQiiI2RaQBKAbTC3bSyqhpYSszo5aiuG+KaCtDSEArunkODZhijmhiULegiEHdHBowCMKPihCL+wBr/suz6y4CeQAAAABJRU5ErkJggg==',
			'6D48' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WANEQxgaHaY6IImJTBFpZWh1CAhAEgtoEQGqcnQQQRZrAIoFwtWBnRQZNW1lZmbW1Cwk94VMEWl0bUQzrxUoFhqIah5QzKER1Q6wW9D0YnPzQIUfFSEW9wEALyvOnRHncgcAAAAASUVORK5CYII=',
			'4C1B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpI37pjCGgrADslgIa6NDCKNDAJIYY4hIgyNQTARJjHWKCEg/TB3YSdOmTQMSK0OzkNwXgKoODENDIWIiKG4RaXDAEAO6BU0vyL2MoY6obh6o8KMexOI+AMvIy0Eelm6QAAAAAElFTkSuQmCC',
			'D92B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGUMdkMQCprC2Mjo6OgQgi7WKNLo2BDqIoIk5AMUCkNwXtXTp0qyVmaFZSO4LaGUMdGhlRDOPodFhCiOaeSyNDgFoYiC3OKDqBbmZNTQQxc0DFX5UhFjcBwAQ8szJweZMOgAAAABJRU5ErkJggg==',
			'37F0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RANEQ11DA1qRxQKmMDS6NjBMdUBW2QoWCwhAFpvC0MrawOggguS+lVGrpi0NXZk1Ddl9UxgCkNRBzWN0wBRjbWBFsyNgighIDMUtogFgMRQ3D1T4URFicR8AJtHLLt0KGBsAAAAASUVORK5CYII=',
			'DC3E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVUlEQVR4nGNYhQEaGAYTpIn7QgMYQ0EwAEksYApro2ujowOyuoBWkQaHhkAMMQaEOrCTopZOW7Vq6srQLCT3oalDiGExD8MOLG7B5uaBCj8qQizuAwCQts1FPsOVkQAAAABJRU5ErkJggg==',
			'8FC7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WANEQx1CHUNDkMREpog0MDoENIggiQW0ijSwNgigiIHUsYLkkNy3NGpq2NJVq1ZmIbkPqq6VAcM8himYYgIBDBhuCXRAdTPQFaGOKGIDFX5UhFjcBwAnEMvmHnYDzAAAAABJRU5ErkJggg==',
			'298F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUNDkMREprC2Mjo6OiCrC2gVaXRtCEQRYwCKOSLUQdw0benSrNCVoVnI7gtgDHREM4/RgQHDPNYGFgwxkQZMt4SGgt2M6pYBCj8qQizuAwBng8kKFQaABwAAAABJRU5ErkJggg==',
			'C6F8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDA6Y6IImJtLK2sjYwBAQgiQU0ijSyNjA6iCCLNYg0IKkDOylq1bSwpaGrpmYhuS+gQRTTvAaRRld08xoxxbC5BezmBgYUNw9U+FERYnEfAOd5y/oNrvX7AAAAAElFTkSuQmCC',
			'0255' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHUMDkMRYA1hbWYEyyOpEpog0uqKJBbQyNLpOZXR1QHJf1NJVS5dmZkZFIbkPqG4KkGwQQdUbgC4mMoXRgbUh0EEE1S0NjI4OAcjuY3QQDXUIZZjqMAjCj4oQi/sAJq7KsoYjabIAAAAASUVORK5CYII=',
			'CA24' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGRoCkMREWhlDGB0dGpHFAhpZW1kbAlpRxBpEGh0aAqYEILkvatW0lVkrs6KikNwHVtfK6ICqVzTUYQpjaAiKHUB1AehuEWl0dEAVYw0RaXQNDUARG6jwoyLE4j4A1QfOjLAdykoAAAAASUVORK5CYII=',
			'05A4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QsRGAIAxFk4INcB8s7MMdaZgmFtkAR6BhSikDWuppfvfu5/Iu0C4j8Ke84odhYSggZJgjL8CwW+aLF1yDWkbqkxMqZPxyPWptOWfjRwr7JjGMu51x5DTe6D2aXJy6iWHANLOv/vdgbvxOdgvOT0kf2ioAAAAASUVORK5CYII=',
			'1DD7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGUNDkMRYHURaWRsdGkSQxEQdRBpdGwJQxBihYgFI7luZNW1l6qqolVlI7oOqa2XA1DsFi1gAmhjQLY4OyGKiIWA3o4gNVPhREWJxHwCjGsrFYXeCkQAAAABJRU5ErkJggg==',
			'EDD0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDGVqRxQIaRFpZGx2mOqCKNbo2BAQEYIgFOogguS80atrK1FWRWdOQ3IemjoAYhh0YbsHm5oEKPypCLO4DAOHAzywu5jNsAAAAAElFTkSuQmCC',
			'FBDB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVUlEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDGUMdkMQCGkRaWRsdHQJQxRpdGwIdRNDVAcUCkNwXGjU1bOmqyNAsJPehqcNnHnY7MNyC6eaBCj8qQizuAwBfys4DdU6JFwAAAABJRU5ErkJggg==',
			'CB59' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WENEQ1hDHaY6IImJtIq0sjYwBAQgiQU0ijS6NjA6iCCLAVWyToWLgZ0UtWpq2NLMrKgwJPeB1AHJqWh6Gx1AJIYdASh2gNzC6OiA4haQmxlCGVDcPFDhR0WIxX0Axb7MvDVaGhQAAAAASUVORK5CYII=',
			'0B0A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB1EQximMLQii7EGiLQyhDJMdUASE5ki0ujo6BAQgCQW0CrSytoQ6CCC5L6opVPDlq6KzJqG5D40dTCxRteGwNAQDDscUdRB3MKIIgZxM6rYQIUfFSEW9wEAF8jLL9jkL2UAAAAASUVORK5CYII=',
			'540B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMYWhmmMIY6IIkFNDBMZQhldAhAFQtldHR0EEESCwxgdGVtCISpAzspbNrSpUtXRYZmIbuvVaQVSR1UTDTUFSiGbF5AK0Mruh0iU4DuQ3MLawCmmwcq/KgIsbgPAMa5yvkPxA7gAAAAAElFTkSuQmCC',
			'AF1C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB1EQx2mMEwNQBJjDRBpYAhhAJFwKDJFpIExhNGBBUksoBWoYgqjA7L7opZODVs1bWUWsvvQ1IFhaCimGEwdph2obgGJMYY6oLh5oMKPihCL+wBzTssjpLZvWAAAAABJRU5ErkJggg==',
			'4076' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpI37pjAEsIYGTHVAFgthDGFoCAgIQBJjDGFtZWgIdBBAEmOdItLo0OjogOy+adOmrcxaujI1C8l9ASB1UxhRzAsNBYoFMDqIoLiFtZXRAV0MaHMDA4pesJsbGFDdPFDhRz2IxX0AG9/LO16naTUAAAAASUVORK5CYII=',
			'E0A2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMYAhimMEx1QBILaGAMYQhlCAhAEWNtZXR0dBBBERNpdAWRSO4LjZq2MnVVFBAi3AdV1+iArjc0oJUBzQ7WhoApDGhuAYoFoLuZtSEwNGQQhB8VIRb3AQAsps311fpiAAAAAABJRU5ErkJggg==',
			'5C9F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMYQxlCGUNDkMQCGlgbHR0dHRhQxEQaXBsCUcQCA0QaWBFiYCeFTZu2amVmZGgWsvtaRRoYQlD1gsXQzAsAijmiiYlMwXQLawDYzajmDVD4URFicR8ALx3KUgU8CVwAAAAASUVORK5CYII=',
			'E242' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkMYQxgaHaY6IIkFNLC2MrQ6BASgiIkAVTk6iKCIAXUGOjSIILkvNGrV0pWZWauikNwHVDeFtdGhEdUOhgDW0IBWBhQxRgegiVNQxVhBtgSgulk01KHRMTRkEIQfFSEW9wEAEWXOZcrmbl0AAAAASUVORK5CYII=',
			'E5E2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDHaY6IIkFNIg0sDYwBARgiDE6iKCKhQDVNYgguS80aurSpaGrVkUhuQ9oTqMrEKPaARZrZUA1DyQ2BVWMtRXkFlQ3M4awhjqGhgyC8KMixOI+AFhZzRdUjIGDAAAAAElFTkSuQmCC'        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>