<?php
		if($totalrows > $shownumrows){
			$params = ""; // reset the params variable
			// We need to limit the number returned based on what $shownumrows is and the currentrow...
			if($colsort == 1){
					$currentrow -= $shownumrows;
				}
			if($currentrow > 0){

				$params = "LIMIT $currentrow, $shownumrows";
				// need to set previous row...
				$previousrow = $currentrow - $shownumrows;
				// update $currentrow...
				$checknext = $totalrows - $currentrow;
				$currentrow += $shownumrows;
				// We gotta show the previous link...
				$showprev = 1;
				// do we need to show next???
				if($checknext > 0 && $checknext > $shownumrows){
					// We gotta show the next button
					$shownext = 1;
				}
				else{
					$endrow=$checknext;
				}
			}
			else{
				// the current row !> 0
				$params = "LIMIT $shownumrows";
				//Do we need to show next?
				if($totalrows > $shownumrows){
					$shownext = 1;
					$currentrow += $shownumrows;
				}
			}
		}
?>
