<?php

function is_hash($var) 
{
   return is_array($var) && sizeof($var) > 0 && array_keys($var)!==range(0,sizeof($var)-1);
}

class Template {
    var $FileRoot = '';
    var $Template = '';
    var $FileName = '';

    function Template($strTemplate) {
            $this->Template = $strTemplate;
    }

    function Open($FileName) {
        $this->FileName = $FileName;
        $fullPath = $this->FileRoot . $this->FileName;
    
        // Verificar si el archivo existe
        if (!file_exists($fullPath)) {
            $this->Template = "<!-- [Template] El archivo '$FileName' no existe! -->\n";
            error_log("ERROR>>Open() [El archivo '$fullPath' no existe]");
            return false;
        }
    
        // Verificar si el archivo tiene tamaño válido
        $fileSize = filesize($fullPath);
        if ($fileSize <= 0) {
            $this->Template = "<!-- [Template] El archivo '$FileName' está vacío! -->\n";
            error_log("ERROR>>Open() [El archivo '$fullPath' está vacío]");
            return false;
        }
    
        // Intentar abrir el archivo
        $fd = fopen($fullPath, "r");
        if (!$fd) {
            $this->Template = "<!-- [Template] No se pudo abrir '$FileName'! -->\n";
            error_log("ERROR>>Open() [No se pudo abrir el archivo '$fullPath']");
            return false;
        }
    
        // Leer el contenido del archivo
        $this->Template = fread($fd, $fileSize);
        fclose($fd);
    
        return true;
    }
    


    function setFileRoot($FileRoot) {
            $this->FileRoot = $FileRoot;
    }


    function setTemplate($strTemplate) {
            $this->Template = $strTemplate;
    }


    function addTemplate($objTemplate) {
            $this->Template .= $objTemplate->Template;
    }


    function setVar($VarName, $VarValue) 
{
    // Convertir a string si es un array o un objeto
    if (is_array($VarValue) || is_object($VarValue)) {
        $VarValue = json_encode($VarValue);
    } elseif (!is_string($VarValue)) {
        $VarValue = (string) $VarValue;
    }

    // Realizar la sustitución en la plantilla
    $this->Template = str_replace($VarName, $VarValue, $this->Template);
}
	function setVars(&$vars, $prepend)
	{
		if (is_hash($vars)) 
		{
			foreach($vars as $key => $value) 
			{
				if (is_array($value)) 
					$this->setVars($value, $prepend.'.'.$key);
				else
					$this->setVar('{'.$prepend.'.'.$key.'}', $value);
			}
		} 
		elseif (is_array($vars)) 
		{
			$rstblock = '';
			$strblock = $this->getBlock("$prepend.Row", "<!-- BLOCK $prepend.Row -->");
			if ($strblock) 
			{
				$idx = 0;
				$total = sizeof($vars);
				foreach($vars as $key => $value) 
				{
					$tpl = new Template($strblock);

					$tpl->setVar('{'.$prepend.'.__idx}', $idx);
					$tpl->setVar('{'.$prepend.'.__parity}', ($idx % 2 == 0) ? 0 : 1);
					if ($idx == 0) 
						$tpl->setvar('{'.$prepend.'.__state}', "FIRST");
					elseif ($idx == $total-1)
						$tpl->setvar('{'.$prepend.'.__state}', "LAST");
					else
						$tpl->setvar('{'.$prepend.'.__state}', "BODY");
					
					if (is_array($value))
						$tpl->setVars($value, $prepend);
					else
						$tpl->setVar('{'.$prepend.'}', $value);

					$rstblock .= $tpl->Template;
					$idx++;
				}
			}
	
			$this->setVar("<!-- BLOCK $prepend.Row -->", $rstblock);
		}
		return 1;
	}

    function getBlock($BlockName, $VarName) {
            $BeginPos = 0;
            $EndPos   = 0;
            $BeginStr = '';
            $EndStr   = '';
            $BeginLen = 0;
            $EndLen   = 0;
            $strBlock = '';

            $BeginStr = "<!-- BEGIN $BlockName -->";
            $BeginLen = strlen($BeginStr);
            $BeginPos = strpos($this->Template, $BeginStr, 0);
            if (!($BeginPos===false)) {
                    $EndStr = "<!-- END $BlockName -->";
                    $EndLen = strlen($EndStr);
                    $EndPos = strpos($this->Template, $EndStr, $BeginPos);
                    if (!($EndPos===false)) {
                            $strBlock = substr($this->Template, $BeginPos, $EndPos + $EndLen - $BeginPos);
                            $this->setVar($strBlock, $VarName);
                            $tplBlock = new Template('');
                            $tplBlock->setTemplate($strBlock);
                            $tplBlock->setVar($BeginStr, '');
                            $tplBlock->setVar($EndStr, '');

                            return $tplBlock->Template;
                    }
            }
    }

    function isTag($TagName) {
        if (strpos($this->Template, $TagName, 0)===false) {
            return 0;
        } else {
            return 1;
        }
    }

}

?>