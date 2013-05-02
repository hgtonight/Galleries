<?php if (!defined('APPLICATION')) exit();

$this->Head->Title('Gallery');


$this->AddCssFile('custom.css');
$this->AddCssFile('gallery.css', 'plugins/Galleries');



echo '<div class="Tabs"><h19>'.T('Picture Galleries').'</h19></div>';

$dir = 'uploads/picgal/';

$path = $_SERVER['REQUEST_URI'];

$ex = explode('/', $path);
$show = 'forum';
if(isset($ex[2])){

// first check if it is a directory
  
if(is_dir($dir.$ex[2])){
		$show = 'gallery';
		$doi = str_replace(array('-','_'),'x', $ex[2]);
		$folder = $ex[2];
	} 
}

switch($show){
	
case 'forum':

if ($dh = opendir($dir)) {
	while (($file = readdir($dh)) !== false) {
		if($file != '.' && $file != '..' && filetype($dir.$file) == 'dir'){
			$folders[] = $file;
		}
	}
	closedir($dh);
}

foreach($folders as $vv){

if(is_file($dir.$vv.'/notes.dat.php')){
		$title = str_replace('_', ' ', $vv);
		echo '<div class="picGals"><h3>'.$title.'</h3>';

// now lets go through the folder grabbing 100 from it to display as preview..you can choose a different number
		
$fi = file($dir.$vv.'/notes.dat.php');
		$cc = count($fi);
		unset($fi[0]);
		shuffle($fi);
		for($i=0;$i<100;$i++){
			if(isset($fi[$i])){
				$pg = explode('|', $fi[$i]);
				echo '<div class="pics"><a class="fancybox" target="_blank" rel="group" href="/forum/'.$dir.$folder.$title.'/'.$pg[0].'" title="'.stripslashes(htmlentities(urldecode($pg2[1]))).'"><img src="/forum/'.$dir.$vv.'/'.$pg[0].'" alt="User Images"/></a></div>';
			}

		} 


echo '<div class="clr"></div><div class="linkBar"><a class="Button" href="/forum/gallery/'.$vv.'">View or Upload More Pictures From '.$title.'</a></div>
		</div>';
	}
}
	break;
	case 'gallery':
	echo '<div class="picGals"><h3>'.$doi.'</h3>';
		
                $fi = file($dir.$folder.'/notes.dat.php');
		$cc = count($fi);

// browse the pictures..
		$bb = 0;
		for($i=1; $i<$cc;$i++){
			$pg2 = explode('|', $fi[$i]);
			
			if(is_int($bb/3)){ echo '<div class="clr"></div>'; }
			$bb++;
			echo '<div class="pics"><img src="/forum/'.$dir.$folder.'/'.$pg2[0].'" alt="UserImages" /><br /></div>';
		}
		echo '<div class="clr"></div>';
		?>

		<?php
	echo '</div>';
	
	break;
}
?>

<?php 
echo '</br>';

$UserHasPermission = Gdn::Session()->CheckPermission('Plugins.Attachments.Upload.Allow', TRUE); if($UserHasPermission) {

$dir = 'uploads/picgal/';
$PATH2IM = ''; // with a trailing slash -- /usr/local/bin/ for liquidweb.com servers.


// making a new folder..


if(isset($_POST['PGFolder'])){
// creating a new folder.
	$dir = 'uploads/picgal/';
	$Fname = trim($_POST['PGNewFolder']);
	if($Fname != ''){
		$doi = preg_replace("/[^A-Za-z0-9\s+]/", "",$Fname);
		$doi = str_replace('  ', ' ', $doi);
		$folder = str_replace(' ', '_', $doi);
// make directory
		$go = mkdir($dir.$folder, 0775);
		$go2 = mkdir($dir.$folder.'/th/', 0775);
//echo '<b>'.$doi.'</b><br />'.$folder.'<br />';
		if($go == true){
			$pg = 'upload';
		} else { $pg = 'error'; }
	} else {
		echo '<h20>You do not have the permission to do that...Please Log In</h20>';
		$error[] = '<h21>You need a folder name to save a new folder..</h21>';
	}
}
}
// uploading a new picture..
$UserHasPermission = Gdn::Session()->CheckPermission('Plugins.Attachments.Upload.Allow', TRUE); if($UserHasPermission) {

if(isset($_POST['PGUpload'])){
	
$theFile = uploadNresizeGallery($_FILES, $dir.$_POST['PGWhichFolder'].'/');
	if(isset($theFile['small'])){

// gotta find the file name..
		$blow = explode('/', $theFile['large']);
		$cc = count($blow);
		$write2file = new FileWriting;
		$gogo = $write2file->add2File($dir.$_POST['PGWhichFolder'].'/notes.dat.php', $blow[$cc-1].'|'.urlencode($_POST['PGCaption']));
		if(is_array($gogo)) echo $gogo[0];
 else echo '<b><h3>Success File has been uploaded!... </h3></b><br /><a href="/forum/gallery" title="Go Back To Main Gallery">Go Back to main Gallery</a>....<br />';
	} 
else{
// couldnt upload the picture.
		$pg = 'error';
		echo '<h21>Error</h21>';
		print_r($theFile);
	}
}
}
//figuring out what page we're on

$path = $_SERVER['REQUEST_URI'];
$ex = explode('/', $path);
if(isset($ex[3])){
	
// first check if it is a directory
	if(is_dir($dir.$ex[3])){
		$pg = 'upload';
		$doi =str_replace(array('-','_'),'x', $ex[3]);
		$folder = $ex[3];
	} 
}

// editing the image
$UserHasPermission = Gdn::Session()->CheckPermission('Plugins.Attachments.Upload.Allow', TRUE); if($UserHasPermission) {
  if(isset($_POST['imgName']) && isset($_POST['imgSave'])) {
    echo 'Editing the image..';
    switch($_POST['imgSave']) {
        case 'Delete Image':
            echo 'deleting the image...<br /><a href="/forum/gallery" title="Go Back To Main Gallery">Go Back to main Gallery...</a><br/>';
            unlink($dir.$folder.'/'.$_POST['imgName']);
            unlink($dir.$folder.'/th/'.$_POST['imgName']);
            $write2file = new FileWriting;
            $gogo = $write2file->editLine($dir.$folder.'/notes.dat.php', array('where' => '0', 'id' => $_POST['imgName'], 'doW' => 'delete'));
            echo $gogo;
        break;
        case 'Save Image';
            echo 'saving the caption on the image..<br /><a href="/forum/gallery" title="Go Back To Main Gallery">Go Back to main Gallery</a>....<br />';
            $write2file = new FileWriting;
            $gogo = $write2file->editLine($dir.$folder.'/notes.dat.php', array('where' => '0', 'id' => $_POST['imgName'], 'doW' => 'edit'), $_POST['imgName'].'|'.urlencode($_POST['imgCap']));
            echo $gogo;
        break;
    }
  } 
}
else {
  echo '<h20>You do not have the permission to do that...Please Log In</h20>';
} 


switch($pg){
	case 'upload':
		
 echo '<h3>Upload to '.$doi.'</h3></br>';
		?>

<div class="Info">

		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" enctype="multipart/form-data">
			<div><input type="hidden" name="PGWhichFolder" value="<?php echo $folder; ?>" />
			File - <input type="file" name="image"/><br />
			Picture Caption - <input class="InputBox" type="text" size="20" name="PGCaption" />
			<div class="Buttons"><input id="Form_Save" name="PGUpload" value="Upload New Picture" class="Button" type="submit"></div>
			</div>
		</form>
		</div>
		<div>
			<h3>Current pictures in this folder</h3></div>
			<div id="PicGallery">
				

		<?php
// open and read the notes file..
		
$fi = file($dir.$folder.'/notes.dat.php');
		$cc = count($fi);

// browse the pictures..
		for($i=1; $i<$cc;$i++){
			$pg2 = explode('|', $fi[$i]);
			echo '
		<form action="'. $_SERVER['REQUEST_URI'].'" method="post">
			<div class="picbox">
			<img src="/forum/'.$dir.$folder.'/'.$pg2[0].'" alt="image" /><br />
			<input type="hidden" name="imgName" value="'.$pg2[0].'" />
			<input type="text" size="20" name="imgCap" value="'.urldecode($pg2[1]).'" /><br />
			<input type="submit" name="imgSave" value="Save Image"  class="Button" /><br />
			<input type="submit" name="imgSave" value="Delete Image"  class="Button" />
			</div>
		</form>';
			if(is_int($i/1000)) echo '
				<div class="clr"></div>
			</div>
			<div>';
		}
		echo '</div><div class="clr"></div>
			</div>
			<div class="clr"></div>
			<div style="height:50px;">&nbsp;</div>
		</div>';
	break;
	case 'error':
		echo '<b><h21>Some errors occured,Name of folder already exists....</h21></b>';
	break;
	default:

?>
<div class="Info">
	<b><h3>Here you can add new Albums to the Gallery</h3></b><br />
	
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<div>Create new Album, Please Pick a Name  - No Spaces <input class="InputBox" stype="text" size="20" name="PGNewFolder" /></div>
		<div class="Buttons"><input id="Form_Save" name="PGFolder" value="Create New Album" class="Button" type="submit"></div>
	</form>
</div>
	
	<?php } 

function uploadNresizeGallery($file, $dir){
	global $PATH2IM;
	$Lwidth =1000;
	$Lheight =1000;
	$maxSize = 6656000; // KBs in size
	$mode = 0666;
	$countinue = true;
	if(!is_dir($dir)){
		return 'Cannot save because the folder wasnt found.';
	}

	if($countinue == true){
		if($file['image']['error']){ $errors[] = "Sorry nothing was uploaded..or"; echo '<h21>Sorry nothing was uploaded</h21>'; }
		if($file['image']['size'] > $maxSize) $errors[] = "The image size you uploaded was too big .... or";
		if($file['image']['type'] != "image/jpeg" && $file['image']['type'] != "image/pjpeg" && $file['image']['type'] != "image/gif" && $file['image']['type'] != "image/png" && $file['image']['type'] != 'image/x-png') $errors[] = "Invalid Image format, supported image types are .jpg (.jpeg), .gif, and .png. -- ".$file['image']['type'];
		if(empty($errors)){
			$nr = 0;
			switch($file['image']['type']){
				case "image/jpeg": $ext = '.jpg'; break;
				case "image/jpg": $ext = '.jpg'; break;
				case "image/pjpeg": $ext = '.jpg'; break;
				case "image/gif": $ext = '.gif'; break;
				case "image/png": $ext = '.png'; break;
				case "image/x-png": $ext = '.png'; break;
			}
			for(;;){
				$nr++;
				if(!file_exists($dir.'image'.$nr.$ext))
				break; 
			}
			$SfilePath = $dir."/th/".$nr.$ext;
			$filePath = $dir."/image".$nr.$ext;

			$file = $_FILES['image']['tmp_name'];
			move_uploaded_file($file, $filePath);
			chmod ($filePath, octdec(666));
			$blah = getimagesize($filePath);
			if($blah[0] > $Lwidth || $blah[1] > $Lheight){
				$cmp = $PATH2IM."convert ".$filePath." -resize ".$Lwidth."x".$Lheight." ".$filePath;
				$resized = system($cmp);
				$cmp2 = $PATH2IM."convert ".$filePath." -resize 200x200 ".$SfilePath;
				$resized = system($cmp2);
			} else {
				$copy1 = copy($filePath, $SfilePath); // /usr/local/bin/
				if($blah[0] > 150 || $blah[1] > 150) $resized = system($PATH2IM."convert ".$SfilePath." -resize 200x200 ".$SfilePath);
			}
			$files['small'] = $SfilePath;
			$files['large'] = $filePath;
			return $files;
		} else { $errors[] = "Image error"; }
	} else { $errors[] = "something happened to the directories permission"; }
	return $errors;
}

class FileWriting {
	
// do not include \n at the end of the lines to add to the file
	
function add2File($file, $line, $where='bottom', $check=''){
		// $check = array('where' => 0|1 etc, find => what would b in where);
		if(is_file($file)){
			$nono = false;
			$gotcha = '';
			if(is_array($check)){
				$data = file($file);
				$fows = count($data);
				for($i=1;$i< $fows;$i++){
					$parts = explode("|", $data[$i]);
					$gotcha .= trim($data[$i])."\n";
					if(urldecode($parts[$check['where']]) == $check['find']){
						$nono = true;
						break;
					}
				}
			}
// if the id was not repeated in the file..
			
                                if($nono == false){
				if(!isset($gotcha) && $where == 'top'){
					$data = file($file);
					$fows = count($data);
					for($i=1;$i< $fows;$i++){
						$gotcha .= trim($data[$i])."\n";
					}
				}
				switch($where){
					case 'bottom':
					$mode = 'a';
					$TTlines = $line."\n";
					break;
					case 'top':
					$mode = 'w+';
					$TTlines = "<?php die('missing data..'); ?>\n".$line."\n".$gotcha;
					break;
					
				}
				if(isset($TTlines)){
					$gogo = $this->write2File($file, $TTlines, $mode);
					return $gogo;
				} else {
					return array('couldnt figue out where to put the line..');
				}
			} else {
				return array('already found this id in the file');
			}
		} else {
// gotta write the new file
			

                   return $this->write2File($file, "<?php die('missing data..'); ?>\n".$line."\n", 'a');
		}
	}
	
	private function write2File($file, $lines, $mode){
		$fp = fopen($file, $mode);
			flock($fp, 2);
			fwrite($fp, $lines);
			flock($fp, 3);
			fclose($fp);
		return 'done';
	}
	
// do not include \n at the end of the line..
	function editLine($file, $id, $line=''){

// $id = array('where' => 0, 'id' => $newId, 'doW' => 'edit|delete');
		$data = file($file);
		$fows = count($data);
		$write = '';
		for($i=0;$i< $fows;$i++){
			$parts = explode("|", $data[$i]);
			if(is_array($parts)){
				if($parts[$id['where']] == $id['id']){
					$write .= ($id['doW'] == 'edit') ? $line."\n" : '';
				} else { $write .= trim($data[$i])."\n"; }
			}
		}
		return $this->write2File($file, $write, 'w+');
	}
}}
