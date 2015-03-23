<?php
/**
 * Class for searching photos
 *
 * @category   MT
 * @package    Admin
 */
abstract class MT_Admin_Model_PhotoSearch {

	/**
	 * Supported photo extensions
	 *
	 * @var array
	 */
	public static $__photoExtensions = array( "jpg", "jpeg", "png" );
	
	/**
	 * Search new photos on webspace
	 *
	 * @param	string	$dir		Directory
	 * @param	string	$startTime	Start time
	 * @return	void
	 */
	public static function search($dir) { 
		if (!is_dir($dir)) {
			return FALSE;
		}
		$fp = opendir( $dir );
		while($basename = readdir($fp)) {
			// Folder	
			if( is_dir($dir.'/'.$basename) && $basename != "." && $basename != "..") {
				//echo '<b>Ordner: '.$dir.'/'.$file.'</b><br>';
				self::search($dir.'/'.$basename);
			}

			if(self::isPhoto($dir.'/'.$basename)) {
				$dbDirname = str_replace(MT_Photo::$__photoPath, '', $dir).'/';
				$dbFile = $dbDirname.$basename;
		
				// Ueberpruefen ob das Bild bereits in der Datenbank gespeichert ist
				if(!MT_Photo::checkPhotoIsInDb($dbFile)) {
					MT_Photo::insert(array(
						'path'        => $dbFile,
						'name_old'    => $basename,
						'gallery'     => MT_Gallery::getIdFromPath($dbDirname),
						'date'        => time(),
						'show'        => 0
					));
				}	
			}
		}
		closedir($fp);
	}
	
	private static function isPhoto($file) {
		$fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		return is_file($file) && in_array($fileExtension, self::$__photoExtensions);
	}
}
?>