<?php
    class Files {
		/*
        public function move_to ($source, $destination, $random_rename) {
            
        }
        
        public function copy_from_site_to ($source, $destination, $random_rename) {
            $extension = Files::getExtension($source);
            $basename = basename($source, '.'.$extension);
            if ( $random_rename == 1 ) {
                $newname = md5($basename.date("Y-m-d").date("H:i:s"));
            } else {
                $newname = $basename;
            }
            
            if (copy($source, $destination.$newname.'.'.$extension)) {
               // return $destination.$newname.'.'.$extension;
               return str_replace("/home/m/myzaprosru/sp-kubani/public_html", "", $destination.$newname.'.'.$extension);
            } else {
                return 'Error';
            }
        }
        
        public function getExtension($filename) {
            return end(explode(".", $filename));
        }
		*/
		public function CheckFiles($files, $numFiles = 10, $allowext = array("jpg", "png", "gif", "txt", "doc", "csv"), $disallowext = array("php", "html", "sh", "js", "cmd", ".htaccess"))
		{
			$stripstr = array(".", "?", ":", "`", "'", "\"", "passwd", "htaccess");
			// $numFiles max upload files(max files from file form)
			// $allowext Allow file extensions
			// $disallowext block file extensions
			foreach ($_FILES as $postfiles) {
				
				if (count($postfiles['name']) > $numFiles) {
					echo 'Max upload files '.$numFiles;
				} else {
					$files = $postfiles;
					
					for ($i = 0; $i < count($files['name']); $i++) {
						if ($files['error'][$i] == 0 && is_uploaded_file($files['tmp_name'][$i])) {
							$fileinfo = pathinfo($files['name'][$i]);
							
							if (!empty(strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension'])))) && in_array(strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension']))), $allowext) == true && !in_array(strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension']))), $disallowext) && base64_decode($fileinfo['filename'], true) == false) {
								
								if (mime_content_type($files['tmp_name'][$i]) == $files['type'][$i] && $files['size'][$i] !== 0) {
									$clearFiles['name'][$i] = strip_tags(trim(str_ireplace($stripstr, "", mb_convert_encoding($fileinfo['filename'], "UTF-8", mb_list_encodings())))) . '.' . strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension'])));
									$clearFiles['type'][$i] = $files['type'][$i];
									$clearFiles['tmp_name'][$i] = $files['tmp_name'][$i];
									$clearFiles['error'][$i] = $files['error'][$i];
									$clearFiles['size'][$i] = $files['size'][$i];
									
								}
							} elseif (!empty(strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension'])))) && in_array(strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension']))), $allowext) == true && !in_array(strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension']))), $disallowext) && base64_decode($fileinfo['filename'], true) !== false) {
								
								if (mime_content_type($files['tmp_name'][$i]) == $files['type'][$i] && $files['size'][$i] !== 0) {
									$clearFiles['name'][$i] = strip_tags(trim(str_ireplace($stripstr, "", mb_convert_encoding(base64_decode($fileinfo['filename']), "UTF-8", mb_list_encodings())))) . '.' . strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension'])));
									$clearFiles['type'][$i] = $files['type'][$i];
									$clearFiles['tmp_name'][$i] = $files['tmp_name'][$i];
									$clearFiles['error'][$i] = $files['error'][$i];
									$clearFiles['size'][$i] = $files['size'][$i];
								}
							}
						} else {
							$uperros[$i] = $files['error'][$i];
						}
					}
				}
			}
			return $clearFiles;
		}
		
		public function CheckDir($Directory)
		{
			if (file_exists($Directory)) {
				chmod($Directory, 0755);
				return $Directory;
			} elseif (!file_exists($Directory)) {
				mkdir($Directory, 0755);
				return $Directory;
			} else {
				return false;
			}
		}
		
		public function UploadFiles($Files, $Directory, $numFiles, $allowext, $disallowext)
		{
			$ClearFiles = Files::CheckFiles($Files, $numFiles, $allowext, $disallowext);
			if (!empty($ClearFiles)) {
				$FullDir = $_SERVER['DOCUMENT_ROOT'] . $Directory;
				for ($i = 0; $i < count($ClearFiles['name']); $i++) {
					if (Files::CheckDir($FullDir) !== false) {
						$fileinfo = pathinfo($ClearFiles['name'][$i]);
						$Upflname = base64_encode(date("Y-m-d H:i:s")) . '.' . $fileinfo['extension'];
						$Updir = Files::CheckDir($FullDir) . DIRECTORY_SEPARATOR;
						if (move_uploaded_file($ClearFiles['tmp_name'][$i], $Updir . $Upflname)) {
							return $Directory . DIRECTORY_SEPARATOR . $Upflname;
						} else {
							//return false;
						}
					}
				}
			}
		}
		
		public function UploadFile($File, $Directory, $allowext = array("csv", "doc"), $disallowext = array("php", "html"))
		{
			$stripstr = array(".", "?", ":", "`", "'", "\"", "passwd", "htaccess");
			$fileinfo = pathinfo($File['name']);
			//print_r($File);
			if ($File['error'] == 0 && is_uploaded_file($File['tmp_name'])) {
				
				if (!empty(strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension'])))) && in_array(strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension']))), $allowext) == true && !in_array(strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension']))), $disallowext) && base64_decode($fileinfo['filename'], true) == false) {
					//if (mime_content_type($File['tmp_name']) == $File['type'] && $File['size'] !== 0) {
						$clearFile['name'] = strip_tags(trim(str_ireplace($stripstr, "", mb_convert_encoding($fileinfo['filename'], "UTF-8", mb_list_encodings())))) . '.' . strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension'])));
						$clearFile['type'] = $File['type'];
						$clearFile['tmp_name'] = $File['tmp_name'];
						$clearFile['error'] = $File['error'];
						$clearFile['size'] = $File['size'];
					//}
				} elseif (!empty(strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension'])))) && in_array(strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension']))), $allowext) == true && !in_array(strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension']))), $disallowext) && base64_decode($fileinfo['filename'], true) !== false) {
					//if (mime_content_type($File['tmp_name']) == $File['type'] && $File['size'] !== 0) {
						$clearFile['name'] = strip_tags(trim(str_ireplace($stripstr, "", mb_convert_encoding(base64_decode($fileinfo['filename']), "UTF-8", mb_list_encodings())))) . '.' . strip_tags(trim(str_ireplace($stripstr, "", $fileinfo['extension'])));
						$clearFile['type'] = $File['type'];
						$clearFile['tmp_name'] = $File['tmp_name'];
						$clearFile['error'] = $File['error'];
						$clearFile['size'] = $File['size'];
					//}
				}
			} else {
				$uperros = $File['error'];
			}
			//return $clearFile;
			if (!empty($clearFile)) {
				$FullDir = $_SERVER['DOCUMENT_ROOT'] . $Directory;
				if (Files::CheckDir($FullDir) !== false) {
					$Upflname = base64_encode(uniqid($fileinfo['filename'])) . '.' . $fileinfo['extension'];
					$Updir = Files::CheckDir($FullDir) . DIRECTORY_SEPARATOR;
					if (move_uploaded_file($clearFile['tmp_name'], $Updir . $Upflname)) {
						return $Directory . DIRECTORY_SEPARATOR . $Upflname;
					} else {
						//return false;
					}
				}
			}
		}
		
		public function DeleteFile($File)
		{
			$FullDir = $_SERVER['DOCUMENT_ROOT'] . $File;
			if (file_exists($FullDir)) {
				unlink($FullDir);
				return true;
			} else {
				return false;
			}
		}

    }
?>