<?php
//In this file we will generate a report to be sent over various hooks

function generateReport($xmlData,$reportType){
	$xmlDom = simplexml_load_string($xmlData);
	switch ($reportType) {
		case 'DAILY_NEW_TEXT'://Just the new grades
			//Witaj,
			//W dniu wczorajszym otrzymałeś następujące oceny.
			//foreach przedmiot: ocena1(opis), ocena2(opis), średnia
			//Z pozdrowienaimi,
			//DziennikLogin
			break;
		case 'FULL_TEXT':
		    //All grades in the register
			//Witaj,
			//oto twoje oceny:
			//foreach przedmiot: ocena1(opis), ocena2(opis), średnia
			//Z pozdrowienaimi,
			//DziennikLogin
			$returnedMessage = '';
			$returnedMessage .="Witaj,\r\noto twoje oceny:\r\n";
			$subjectsCount = $xmlDom->registerSubjects->subject->count();
			
            $subjectsDone = 0;
            $gradesDone = 0;

            while ($subjectsDone < $subjectsCount) {
            		
				$returnedMessage .= "Przedmiot:".$xmlDom->registerSubjects->subject[$subjectsDone]['subjectName']."\r\n";
				//$returnedMessage .= 'Średnia:'.$xmlDom->registerSubjects->subject[$subjectsDone]->subjectAverage."\r\n"; DOESNRT WORK
				$gradesCount = count($xmlDom->registerSubjects->subject[$subjectsDone])-1;
				while ($gradesDone < $gradesCount) {
						$returnedMessage .= 'Ocena:'.$xmlDom->registerSubjects->subject[$subjectsDone]->grade->gradeValue."\r\n";
						$returnedMessage .= 'Waga:'.$xmlDom->registerSubjects->subject[$subjectsDone]->grade->gradeWeight."\r\n";
						$returnedMessage .= 'Skrót:'.$xmlDom->registerSubjects->subject[$subjectsDone]->grade->gradeAbbrev."\r\n";
						$returnedMessage .= 'Data:'.$xmlDom->registerSubjects->subject[$subjectsDone]->grade->gradeDate."\r\n";
						$returnedMessage .= 'Tytuł:'.$xmlDom->registerSubjects->subject[$subjectsDone]->grade->gradeTitle."\r\n";
						$returnedMessage .= 'Grupa:'.$xmlDom->registerSubjects->subject[$subjectsDone]->grade->gradeGroup."\r\n \r\n";
						$gradesDone++;
				}
				$gradesDone = 0;
            	$subjectsDone++; 
            }
			$returnedMessage .= "Z pozdrowieniami, \r\n DziennikLogin";
			return $returnedMessage;
			break;
		case 'DAILY_SUBJECT_NEW_TEXT'://All subjects where you got new grades
			//Witaj,
			//W dniu wczorajszym otrzymałeś następujące oceny.
			//foreach przedmiot: ocena1(opis), ocena2(opis), średnia
			//Z pozdrowienaimi,
			//DziennikLogin
			break;
		case 'FULL_SPREADSHEET'://All subjects - but in excel?
			//Witaj,
			//W dniu wczorajszym otrzymałeś następujące oceny.
			//foreach przedmiot: ocena1(opis), ocena2(opis), średnia
			//Z pozdrowienaimi,
			//DziennikLogin
			break;
	}
	
	
}


?>