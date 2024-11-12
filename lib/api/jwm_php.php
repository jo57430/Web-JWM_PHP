<?php
    header('Content-Type: application/json; charset=utf-8');

    /* [Global] JWMPHP_readJSONfromFile(): Used to read a json file and decode it to be used
     * @parm file {string} The path to the file to read.
     * 
     * @return {Object} Return a object.
     */
    function JWMPHP_readJSONfromFile($str_file) {
        if( !empty($str_file) && file_exists($str_file) ){
            $data = file_get_contents($str_file); 
            if( !empty($data) ){
                $objFromJSON = json_decode($data, true);
                if( !empty($objFromJSON) ){
                    return $objFromJSON; 
                };
            }; 
        };
        return array();
    };
    /* [Global] JWMPHP_loadFromFolder() : Used to create a pagesList/aliasList of all page inside this folder using there respective json file.
     * @parm file {string} The path to the folder to explore and include.
     * 
     * @return {Array} Return a config array.
     */
    function JWMPHP_loadFromFolder($str_path) {
        $obj_return = Array("pagesList"=>Array(), "aliasList"=>Array());
        if( !empty($str_path) && file_exists($str_path) ){
            if(substr($str_path, -1) != "/") {$str_path = $str_path . "/"; };
            $obj_files = array_diff(scandir($str_path), array('.', '..'));

            foreach($obj_files as $file) {
                $info = pathinfo($file);
                $str_id = basename($file,'.'.$info['extension']); 

                if($info['extension'] == "json"){
                    $obj_config = JWMPHP_readJSONfromFile("$str_path$file");

                    $obj_return["pagesList"][$str_id] = Array(
                        "title" => $obj_config["title"],
                        "contentType" => $obj_config["contentType"],
                        "content" => $obj_config["content"],
                        "options" => $obj_config["options"]
                    );
                    
                    if(!empty($obj_config["aliasList"])){
                        foreach($obj_config["aliasList"] as $str_alias) {
                            $obj_return["aliasList"][$str_alias] = $str_id;
                        };
                    };
                };
            };
        };

        return $obj_return;
    }

    // Load config
    $obj_config = Array("pagesList"=>Array(), "aliasList"=>Array());
    $obj_config = array_merge($obj_config, JWMPHP_readJSONfromFile("./jwm_php.json"));
    if(!empty($obj_config["loadFromFolder"])){
        $obj_config = array_replace_recursive($obj_config, JWMPHP_loadFromFolder($obj_config["loadFromFolder"]));
    };
    if(!empty($obj_config["loadFromFolders"])){
        foreach ($obj_config["loadFromFolders"] as $str_path) {
            $obj_config = array_replace_recursive($obj_config, JWMPHP_loadFromFolder($str_path));
        }
    };

    if(!empty($_GET['menu'])){$_POST['menu'] = $_GET['menu']; };
    if(!empty($_POST['menu']) and !empty($obj_config["aliasList"]) and !empty($obj_config["aliasList"][$_POST['menu']])){
        $_POST['menu'] = $obj_config["aliasList"][$_POST['menu']];
    };
    
    if(!empty($_POST['menu'])){
        if(!empty($obj_config["pagesList"] and !empty($obj_config["pagesList"][$_POST['menu']]))){
            $str_id = $_POST['menu'];
            $obj_menu = $obj_config["pagesList"][$_POST['menu']];
            $obj_send = array(
                "ok" => true,
                "error" => "Data found and returned",

                "identifier" => $str_id,
                "title" => $obj_menu["title"],
                "body" => "",
                "options" => $obj_menu["options"],
            );

            switch ($obj_menu["contentType"]) {
                case 1:
                    try {
                        $obj_send["body"] = file_get_contents($obj_menu["content"]);
                    } catch (\Exception $th) {
                        $obj_send["body"] = "Can't not read the requested file.";
                    }
                    break;
                case 2:
                    try {
                        $obj_send["body"] = include($obj_menu["content"]);
                    } catch (\Exception $th) {
                        $obj_send["body"] = "Can't not read the requested file.";
                    }
                    break;
                case 3:
                    $obj_send["body"] = '<iframe width="100%" height="100%" src="' . $obj_menu["content"] . '" title="' . $obj_menu["title"] . '"></iframe>';
                    break;
                default:
                    $obj_send["body"] = $obj_menu["content"];
                    break;
            }

            echo json_encode($obj_send);
        }else{
            echo json_encode( array( "ok" => false, "error" => "The requested data has not been found !", ) );
        }
    }else{
        echo json_encode( array( "ok" => false, "error" => "No identification details has been send !", ) );
    }
?>