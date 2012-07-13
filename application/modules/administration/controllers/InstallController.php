<?php
class Administration_InstallController extends Zend_Controller_Action {

	public function indexAction() {
	
	}
	
	public function addusersAction() {
		set_time_limit(0);
		
		$users = new Users();
		$users->delete(array("id > 1"));
		
		$thu = new TreatmentsHasUsers();
		
		for($i = 1; $i <= 500; $i++) {
			
			$pw = $this->generatePassword(5);
			$data = array("id" => $i+1, "userhash" => "user" . $i, "password" => sha1($pw), "role" => "member", "grp" => $i, "email" => "", "verified" => "1");
			$row = $users->createRow($data);
			$row->save();
			
			echo "user" . $i . " - " . $pw . "<br>";
			
			if ($i % 2 == 0) {
				$row = $thu->createRow(array("treatments_id" => 11, "users_id" => $i+1));
				$row2 = $thu->createRow(array("treatments_id" => 14, "users_id" => $i+1));
			} else {
				$row = $thu->createRow(array("treatments_id" => 12, "users_id" => $i+1));
				$row2 = $thu->createRow(array("treatments_id" => 13, "users_id" => $i+1));
			}
			
			$row->save();
			$row2->save();
		}
	}

	public function noaccessAction() {

	}
	
	private function generatePassword($length)
	{
	
		// start with a blank password
		$password = "";
	
		// define possible characters - any character in this string can be
		// picked for use in the password, so if you want to put vowels back in
		// or add special characters such as exclamation marks, this is where
		// you should do it
		$possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
	
		// we refer to the length of $possible a few times, so let's grab it now
		$maxlength = strlen($possible);
	
		// check for length overflow and truncate if necessary
		if ($length > $maxlength) {
			$length = $maxlength;
		}
	
		// set up a counter for how many characters are in the password so far
		$i = 0;
	
		// add random characters to $password until $length is reached
		while ($i < $length) {
	
			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, $maxlength-1), 1);
	
			// have we already used this character in $password?
			if (!strstr($password, $char)) {
				// no, so it's OK to add it onto the end of whatever we've already got...
				$password .= $char;
				// ... and increase the counter by one
				$i++;
			}
	
		}
	
		// done!
		return $password;
	
	}
	
}
