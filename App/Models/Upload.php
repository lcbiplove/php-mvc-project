<?php

namespace App\Models;

use PDO;
use App\Models\User;
use App\Models\Title;
/**
 * Class Upload
 * 
 * PHP 7.3
 */
class Upload extends \Core\Model
{

    /**
     * Constructor of the class
     * 
     * @param array $data  Initial property values (optional)
     * 
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Control upload
     * 
     * @return boolean if success, false otherwise
     */
    public function controlUpload()
    {
        $count = count($this->name);
        
        if ($this->titleSave() !== false) {
            $title_id = Title::getLatestTitleId($this->id)->title_id;;
            $username = User::findByUserId($this->id)->username;
            if (!file_exists("public/uploads/$username")) {
                mkdir("public/uploads/$username");
            }

            for ($i = 0; $i < $count; $i++) {
                $filename = $this->name[$i];
                
                $sql = 'INSERT INTO files (filename, title_id) VALUES (:filename, :title_id)';
                $db = static::getDB();
                $stmt = $db->prepare($sql);
                    
                $stmt->bindValue(':filename', $filename, PDO::PARAM_STR);
                $stmt->bindValue(':title_id', $title_id, PDO::PARAM_STR);
                $stmt->execute();
                
                move_uploaded_file($this->tmp_name[$i], "public/uploads/$username/$filename");
            } 
            return true;
        }
        return false;  
    }

    /**
     * Title save
     * 
     * @return mixed string $title_id, false otherwise
     */
    public function titleSave()
    {
        $this->is_private = empty($this->is_private) ? 0 : 1;
        if ($this->title_name == "") {
            return false;
        }
        $title = new Title($this->title_name, $this->is_private);
        if ($title->save($this->id)) {
            return true;
        }
        return false;
    }

    /**
     * Get all files from title_id
     * 
     * @param string $title_id, from which all files are taken
     * 
     * @return objects
     */
    public static function getAllFiles($title_id)
    {
        $db = static::getDB();

        $sql = 'SELECT * FROM files
                WHERE title_id = ' . $db->quote($title_id);

        return $db->query($sql, PDO::FETCH_CLASS, get_called_class());
    }
}