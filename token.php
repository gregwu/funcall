<?php
//$source = file_get_contents('example.php');
$source = file_get_contents('Main.php');
$tokens = token_get_all($source);



$functions = array();
$hasFunc = false;
$stack = array();
$name = '|';
$current_stack_count = 0;
foreach ($tokens as $token) {
   if (is_string($token)) {
       // simple 1-character token

       //echo "token: $token \n";

	if($token == '{')
	{
		$stack[] = $name;
		$c = count($stack);
//echo "{ $c: $name  \n";
	}
	else if($token == '}')
	{
		$c = count($stack);
		$nm = array_pop($stack);
//echo "} $c: '$nm'  current_stack_count: $current_stack_count   \n";
		if(($c - 1) == $current_stack_count)
		{
			if($nm == $name)
			{
				 $name = '|';
			}
			else
			{
//echo "ERROR: $name \n";
				exit;
			}
		}
	}


   } else {
       // token array
       list($id, $text) = $token;

       switch ($id) { 
           case T_COMMENT: 
           case T_DOC_COMMENT:
               // no action on comments
               break;

           default:
               // anything else -> output "as is"
//echo "id: $id \n";
                if(empty(trim($text)) ) continue;
//echo "$text \n";
		if($text == '{')
		{
			$stack[] = $name;
			$c = count($stack);
//echo "{ $c: $name  \n";
		}
        else if($text == '}')
        {
                $c = count($stack);
                $nm = array_pop($stack);
//echo "} $c: '$nm'  current_stack_count: $current_stack_count   \n";
                if(($c - 1) == $current_stack_count)
                {
                        if($nm == $name)
                        {
                                 $name = '|';
                        }
                        else
                        {
//echo "ERROR: $name \n";
                                exit;
                        }
                }
        }
		else if($text == 'function' && $name == '|')
		{
			$hasFunc = true;
			$current_stack_count = count($stack);
		}
		else 
		{  
			if($hasFunc == true )
			{
				$name = $text;
				$hasFunc = false;
//				$functions[$name] = array();
//echo "----------------------------FUNCTION :$name ".count($stack)." \n";
			}
			else if($name != '')
			{
//echo "----------------------------TOKEN :$name : '$text'  ".count($stack)." \n";
				$functions[$name][$text] = array();
			}
		}
               break;
       }
   }

}



$funcs = array_keys($functions);
$result = array();
foreach($functions as $k=>$tokens)
{
	$result[$k] = array();
	foreach($tokens as $n=>$t)
	{
		if(in_array($n, $funcs))
		{
//echo "delete  $k $t\n";
		//	unset($functions[$k][$n]);
			$result[$k][$n] = '';
		} 
	}
}

//var_dump($result);


foreach($result as $k=>$tokens)
{
        foreach($tokens as $n=>$t)
        {       
//echo "$k :$n \n";
		$result[$k][$n] = $result[$n];
        }
}


foreach($result as $k=>$tokens)
{
        foreach($tokens as $n=>$t)
        {       
//echo "$k :$n \n";
		foreach($t as $tk=>$tv)
		{
			$result[$k][$n][$tk] = $result[$tk];
		}
        }
}

echo json_encode($result); exit;
//var_dump($functions);
{
	echo " \n$k \n";
        foreach($tokens as $n=>$t)
        {
		echo "       $t \n";
		
        }
}


