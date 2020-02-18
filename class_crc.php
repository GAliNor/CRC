<?php

	class Crc
	{
		private $_msg;
		private $_controle;

		function __construct($m,$c)
		{
			$this->_msg = $m;
			$this->_controle = $c;
		}

		public function afficher()
		{
			echo 'msg : '.$this->_msg.' code : '.$this->_controle.'<br/>';
		}
 
 		//Revois puissances polynome d'une série de bits
		private function polynome_cast($bits)
		{
			$pows = array();
			$j = -1; //indice tbleau $pows
			$bits = strrev($bits); // ordre de lecture : bits = 1110 -> ordre_lecture = 0111

			for($i = 0; $i < strlen($bits) ; $i++)
			{
				if($bits[$i] == '1')
				{

					$j = $j + 1;
					$pows[$j] = $i;
				}
			}
			return $pows;
		} 

		//multiplie polynome par un nombre donné
		private function polynome_multiplication(&$pows,$nbr)
		{
			for($i = 0; $i < sizeof($pows); $i++)
			{
				$pows[$i] += $nbr;
				/*if($pows[$i] == 0)
					$pows[$i] = -1; //pour ne pas supprimmer pow(x,0) dans la fonction reminder*/
			}
		}

		//opération XOR entre deux polynomes
		private function reminder($p1,$p2)
		{
			$size_p2 = sizeof($p2); //taille stocker dans variable car utilisé deux fois - pour ne pas refaire le calcul
			$nbr_zero_p2 = 0;
			$end_of_p2 = false;

			for($i = sizeof($p1) - 1; $i >= 0 && !$end_of_p2; $i--)
			{
				if($nbr_zero_p2 == $size_p2) //si tout les éléments de p2 sont à zéro
					$end_of_p2 = true;

				$elmt_found = false;
				for($j = $size_p2 - 1; $j >= 0 && !$elmt_found; $j--)
				{
					if($p2[$j] == $p1[$i])
					{
						$p1[$i] = -1; //0 avant
						$p2[$j] = -1; //0 avant
						$elmt_found = true;
						$nbr_zero_p2 += 1;
					}
				}
			}
			$p3 = array_merge($p1, $p2);
			$p3 = array_diff($p3, [-1]);
			sort($p3);
			return $p3;
		}

		//Fonction récursive qui revoie le reste de la division euclidienne de deux polynomes
		private function polynome_division($p_x,$q_x)
		{	
			if(sizeof($p_x)-1 >= 0 && $q_x[sizeof($q_x)-1] <= $p_x[sizeof($p_x)-1])
			{
				$q_x_bis = $q_x;
				$multiplicator = $p_x[sizeof($p_x)-1] - $q_x[sizeof($q_x)-1];
				$this->polynome_multiplication($q_x_bis,$multiplicator);
				$p_x = $this->reminder($p_x,$q_x_bis);
				return $this->polynome_division($p_x,$q_x);
			}
			else
			{
				return $p_x;
			}
		}

		//Renvoie la suite binaire d'un polynome
		private function binary_cast($p_x)
		{
			$taille = sizeof($p_x) - 1;
			if($taille == -1)
				$pos_pow  = 0;
			else
				$pos_pow = $p_x[$taille];
			$binary = '';
			$i = $taille;

			do{

				if($i >= 0 && ($p_x[$i] == $pos_pow /*|| $p_x[$i] == -1 */))
				{
					$binary = $binary.'1';
					$i -= 1;
				}
				else
				{
					$binary = $binary.'0';
				}
				$pos_pow -= 1;
			
			}while($pos_pow >= 0);
			
			return $binary;
		}

		public function r_x()
		{
			//cast bits en polynome
			$p_x = $this->polynome_cast($this->_msg);
			$q_x = $this->polynome_cast($this->_controle);
			
			//forme finale dividende f_x selon règle de hashage
			$this->polynome_multiplication($p_x,$q_x[sizeof($q_x) - 1]); //$q_x[sizeof($q_x) - 1] -> dernier élément du tableau q_x - puissance la plus élevé
			
			//forme polynomiale de la clé (reste division euclidienne polynomiale)
			$r_x = $this->polynome_division($p_x,$q_x);
			$r_x = $this->binary_cast($r_x);

			return $r_x;
		}
	}

	//$objet_crc_1 = new Crc('10001110','1101');
	//$objet_crc_2 = new Crc('1101011011','10011');

	//$objet_crc_1->afficher();
	//echo $objet_crc_1->r_x();

	//$objet_crc_2->afficher();
	//echo $objet_crc_2->r_x();	

?>
