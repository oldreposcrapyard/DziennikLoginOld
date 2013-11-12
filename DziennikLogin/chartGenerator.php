<?php
/* Include all the classes */
include("classes/pChart/class/pDraw.class.php");
include("classes/pChart/class/pImage.class.php");
include("classes/pChart/class/pData.class.php");

/* Create your dataset object */
$myData = new pData();
 
/* Add data in your dataset */
$myData->addPoints(array(VOID,3,4,3,5));

/* Create a pChart object and associate your dataset */
$myPicture = new pImage(700,230,$myData);

/* Define the boundaries of the graph area */
$myPicture->setGraphArea(60,40,670,190);

/* Choose a nice font */
$myPicture->setFontProperties(array("FontName"=>"fonts/Forgotte.ttf","FontSize"=>11));

/* Draw the scale, keep everything automatic */
$myPicture->drawScale();

/* Draw the scale, keep everything automatic */
$myPicture->drawSplineChart();

/* Build the PNG file and send it to the web browser */
$myPicture->Stroke();

?>
