<?php
/*
Класс позволяет работать с комплексными числами.
Комплексные числа можно передаватьб либо в виде строки, либо в виде массива, первым элементом которого является действительная часть, а вторым - мнимая.
Операции можно прооизводить как с экземплярами объекта класса, так и вызывая методы статически и передавая 2 комплексных числа.
Поддерживается 4 операции: add, substract, multiply и divide.

Примеры:
	$c1 = new Complex([1.23,2.11]);
	
	try{$c2 = new Complex('-4,231+1,112i');}
	catch (Exception $e) {echo $e->getMessage();}
	
	echo $c1->multiply($c2);
	echo '<hr>';
	echo Complex::multiply('-4,231+1,112i', [1.23,2.11]);	
*/
class Complex {
	public $a; // действительная часть
	public $b; // мнимая часть
	
    public function __construct($c=[0,0]) {
		if(is_string($c)) {
			$c = str_replace(' ', '', $c);
			$c = str_replace(',', '.', $c);
			preg_match('/(-?\d+\.?\d*)\+?(-?\d+\.?\d*)/', $c, $matches);
			if(!isset($matches[2])) {
				throw new Exception('Неверный формат комплексного числа');
			}
			$c = [$matches[1],$matches[2]];
		}
		if(is_array($c) && isset($c[0]) && isset($c[1])) {
			$this->a = $c[0];
			$this->b = $c[1];
		}
    }
    
	public function __toString() {
		return $this->a . ($this->b>0?'+':'') . $this->b . 'i';
	}
    
    public function __call($method, $args) {
    	$c = $args[0];
    	if(!($c instanceof self)) {
    		try{$c = new Complex($c);}
			catch (Exception $e) {return $e->getMessage();}
		}
		
		$c3 = [];
		
		switch($method) {
			case 'add':
				$c3 = [
					$this->a + $c->a,
					$this->b + $c->b,
				];
				break;
			case 'substract':
				$c3 = [
					$this->a - $c->a,
					$this->b - $c->b,
				];
				break;
			case 'multiply':
				$c3 = [
					$this->a*$c->a - $this->b*$c->b,
					$this->a*$c->b - $this->b*$c->a
				];
				break;
			case 'divide':
				$c3 = [
					($this->a*$c->a + $this->b*$c->b) / ($c->a*$c->a + $c->b*$c->b),
					($this->b*$c->a + $this->a*$c->b) / ($c->a*$c->a + $c->b*$c->b)
				];
				break;
		}
		
		return new Complex($c3);
	}
	
	public static function __callStatic($method, $args) {
    	$c1 = $args[0];
    	$c2 = $args[1];
    	if(!($c1 instanceof self)) {
    		try{$c1 = new Complex($c1);}
			catch (Exception $e) {return $e->getMessage();}
		}
		if(!($c2 instanceof self)) {
    		try{$c2 = new Complex($c2);}
			catch (Exception $e) {return $e->getMessage();}
		}
		
		return $c1->$method($c2);
	}
}