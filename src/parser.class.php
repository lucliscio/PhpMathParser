<?
	/* 
	 * PhpMathParser ver. 1.2 PHP5
       	 * ---------------------------
       	 * License: GNU General Public Licence(GPL)
	 * Autor: LucLiscio  
	 * Description:
	 * IT : Questa classe php traduce espressioni matematiche in istruzioni php
	 * EN : This php class translate mathematical expressions in php instructions
	 *
	 */

	class phpmathparser
	{
		private $fun;
		private $pfun;
		
		function phpmathparser ($fun)
		{
			$this->set_fun($fun);
		}
		
		public function set_fun($fun)
		{		
			$this->fun = $fun;
			$this->pfun = '';
		}
		
		public function get_pfun()
		{
			if ($this->pfun=='')
			{
				$this->pfun = $this->str2php($this->parser($this->fun));
			}
			return $this->pfun;
		}
		

		public function get_fun()
		{
			return $this->fun;
		}

		private function parser($tfun)
		{
			$lenfun = strlen($tfun);
			$tmpfun = "(";
			$c = $tfun;
			
			for($i=0;$i<$lenfun;$i++)
			{
				
				if ($c[$i] == 'x')
				{
					if($c[$i+1] == '^')
					{
						if ($c[$i+2]=='(')
						{
							$ptc = $this->whereis(')',$c,$i+2)-1;
							$tmpfun .= '^($i,'.$this->parser(substr($c,$i+3,$ptc)).')';
							$i += $ptc+3;
						}
						else 
						{
							$tmpfun .= '^($i,'.$c[$i+2].')';
							$i+=2;
						}
					}
					else 
					{
						$tmpfun .='$i';
					}
				}
				else if (is_numeric($c[$i]))
				{
					if(($c[$i+1] == 'x') or ($c[$i+1] == '(') or ($c[$i+1] == 't') or ($c[$i+1] == 'c') or ($c[$i+1] == 's') or ($c[$i+1] == 'l') or ($c[$i+1] == 'r'))
					{
						if($c[$i+2] == '^')
						{
							$tmpfun .= '('.$c[$i].'*^($i,'.$c[$i+3].'))';
							$i+=3;
						}
						else 
						{
							$tmpfun .= $c[$i].'*';
						}
					}
					else if($c[$i+1] == '^')
					{
												
						if ($c[$i+2]=='(')
						{
							$ptc1 = $this->whereis(')',$c,$i+2)-1;
							$tmpfun .= '^('.$c[$i].','.$this->parser(substr($c,$i+3,$ptc1)).')';
							$i = $ptc1+3;
						}
						else 
						{
							if ($c[$i+2]=='x')
							{
								$tmpfun .= '^('.$c[$i].',$i)';
							}
							else 
							{
								$tmpfun .= '^('.$c[$i].','.$c[$i+2].')';
							}
							$i= $i+3;
						}			
						
					}
					else
					{
							$tmpfun .= $c[$i];
					}
				}
				else if ($c[$i]=='e')
				{
					if(($c[$i+1] == 'x') or ($c[$i+1] == '(') or ($c[$i+1] == 't') or ($c[$i+1] == 'c') or ($c[$i+1] == 's') or ($c[$i+1] == 'l') or ($c[$i+1] == 'r'))
					{
						$tmpfun .= 'e<>p(1)*';
					}
					else if($c[$i+1] == '^')
					{
												
						if ($c[$i+2]=='(')
						{
							$ptc1 = $this->whereis(')',$c,$i+2)-1;
							$tmpfun .= 'e<>p('.$this->parser(substr($c,$i+3,$ptc1)).')';
							$i = $ptc1+3;
						}
						else 
						{
							if ($c[$i+2]=='x')
							{
								$tmpfun .= 'e<>p($i)';
							}
							else 
							{
								$tmpfun .= 'e<>p('.$c[$i+2].')';
							}
							$i= $i+2;
						}			
						
					}
					else
					{
							if ($c[$i+1]!='n')
							{
								$tmpfun .= 'e<>p(1)';
							}
							else 
							{
								$tmpfun .= $c[$i];
							}
					}
				}
				else if ($c[$i]=="(")
				{
					$ptc = $this->whereis(')',$c,$i)-1;
					
					$pos = $i+$ptc+2;
					
					if ($c[$pos]=='^')
					{				
						if ($c[$ptc+3]=='(')
						{
							$ptc1 = $this->whereis(')',$c,$ptc+3)-1;
							$tmpfun .= '^('.$this->parser(substr($c,$i+1,$ptc)).','.$this->parser(substr($c,$ptc+4,$ptc1)).')';
							$i = $ptc+$ptc1+3;
						}
						else 
						{
							$tmpfun .= '^('.$this->parser(substr($c,$i+1,$ptc)).','.$c[$i+$ptc+3].')';
							$i+= $ptc+3;
						}				
					}
					else 
					{
						$tmpfun .= $c[$i];
					}
				}
				else
				{		
					$tmpfun .= $c[$i];		
				}
			}
			$tmpfun.=')';
			return $tmpfun;
		}
		
		private function whereis($s,$str,$p)
		{
			$c = $p+1;
			$pa = 1;
			$find = false;
			
			while (!$find)
			{
				if ($str[$c]=='(')
				{
					$pa++;
				}
				else if ($str[$c]==')')
				{
					$pa--;
					
					if ($pa == 0)
					{
						$find = true;
					}
				}
				$c++;
			}
			
			return $c-($p+1);
		}
		
		private function str2php($str)
		{
			$str = str_replace('^','pow',$str);
			$str = str_replace('sen','sin',$str);
			$str = str_replace('r','sqrt',$str);
			$str = str_replace('x','$i',$str);
			$str = str_replace('<>','x',$str);
			
			return $str;
		}
		
		public function evalfun($x)
		{
			$y;
			$val;
			if (is_array($x))
			{
				$a=array();
				$p=0;
				foreach ($x as $i) 
				{
 					eval("\$val=".$this->get_pfun().";");
					$a[$p]=$val;
					$p++;
				}
				$y=$a;
			}
			else
			{
				$i=$x;				
				eval("\$y=".$this->get_pfun().";");
			}
			return $y;
		}
	}
?>