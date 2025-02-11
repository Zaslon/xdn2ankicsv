<?php
	function json_to_csv(){
		$url = "shaleian.xdn"; //url指定なら他ユーザーに対する権限が必要。相対指定ならオーナーとしてアクセスする模様。
		$arr = file($url, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $returns = array();

    	// $returns[] = ['word', 'title', 'translation', 'translation2','translation3'];

        $wordId = 0;
        $isEnglish = false;
        foreach($arr as $i => $row){
            //英語部分を無視する
            if(str_starts_with($row, "!EN")){
                $isEnglish = true;
            }elseif(str_starts_with($row, "!JA")){
                $isEnglish = false;
            }

            if(!$isEnglish){
                if(str_starts_with($row, "*")){
                    if($wordId !== 0){
                        $return = array_pad($return, 8, "");
                    }
                    $return = &$returns[$wordId];//参照渡しとすることで、returnを編集するとreturnsの各要素も変更される
                    $return[] = preg_replace('/\* @\d+ ([^\s~]+)~*/','\1',$row);
                    $wordId++;
                }elseif(strpos($row, "?")){
                    //?が含まれる行はなにもしない
                }elseif(str_starts_with($row, "+")){
                    $return[] = preg_replace('/\+ <(\S+)>/','\1',$row);
                }elseif(str_starts_with($row, "=")){
                    $return[] = preg_replace('/\= (\S+)/','\1',$row);
                }elseif(str_starts_with($row, "M:")){
                    $return[] = preg_replace('/M: (\S+)/','\1',$row);
                }elseif(str_starts_with($row, "U:")){
                    $return[] = preg_replace('/U: (\S+)/','\1',$row);
                }elseif(str_starts_with($row, "S:")){
                    $return[] = preg_replace('/S: (\S+)/','\1',$row);
                }
            }
        }

        return $returns;
    }

    $out = json_to_csv();

    $fp = fopen('out.csv', 'w');
	foreach($out as $row){
		fputcsv($fp, $row);
	}

	fclose($fp);

	echo 'done ';
    echo time();