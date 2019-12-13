<?
	include "../src/parser.class.php";
	
	$p = new phpmathparser("log((x^2+5x+2),5)");

	echo "<h1>PhpMathParser ver. 1.2 PHP5 - Test</h1><br><br>";
	echo "<b>Funzione 1</b>:<br>".$p->get_fun()."<br>In php: ".$p->get_pfun()."<br>";
	$x = array(1, 3, 4, 10, 13);
	echo "Questa funzione nei punti: 1,3,4,10,13 assume valure: <br><pre>";
	var_dump($p->evalfun($x));
	echo "</pre><br><br>";
	
	$p->set_fun("x^(log(x))");
	echo "<b>Funzione 2</b>:<br>".$p->get_fun()."<br>In php: ".$p->get_pfun()."<br><br>";
	echo "Questa funzione nei punti: 1,3,4,10,13 assume valure: <br><pre>";
	var_dump($p->evalfun($x));
	echo "</pre><br><br>";
?>	
	