<?php

    // Fonts formatting    
    $fntNormal =& $xls->addFormat(array('Size' => 10));
    $fntNormal->setLocked();

    $fntNormalCenter =& $xls->addFormat(array('Size' => 10));
    $fntNormalCenter->setAlign("center");
    $fntNormalCenter->setLocked();

    $fntHalfDayCenter =& $xls->addFormat(array('Size' => 10));
    $fntHalfDayCenter->setAlign("center");
    $fntHalfDayCenter->setColor("red");
    $fntHalfDayCenter->setBgColor("yellow");
    $fntHalfDayCenter->setFgColor("yellow");
    $fntHalfDayCenter->setLocked();
    
    $fntEarlyDismissal =& $xls->addFormat(array('Size' => 10));
    $fntEarlyDismissal->setAlign("center");
    $fntEarlyDismissal->setColor("black");
    $fntEarlyDismissal->setBgColor("yellow");
    $fntEarlyDismissal->setFgColor("yellow");
    $fntEarlyDismissal->setLocked();
    
    $fntTardyCenter =& $xls->addFormat(array('Size' => 10));
    $fntTardyCenter->setAlign("center");
    $fntTardyCenter->setColor("red");
    $fntTardyCenter->setLocked();

    $fntHolidayCenter =& $xls->addFormat(array('Size' => 10));
    $fntHolidayCenter->setAlign("center");
    $fntHolidayCenter->setBgColor("grey");
    $fntHolidayCenter->setFgColor("grey");
    $fntHolidayCenter->setLocked();
    
    $fntFailCenter =& $xls->addFormat(array('Size' => 10));
    $fntFailCenter->setAlign("center");
    $fntFailCenter->setBgColor("yellow");
    $fntFailCenter->setFgColor("yellow");
    $fntFailCenter->setLocked();

    $fntNormalUnderlined =& $xls->addFormat(array('Size' => 10));
    $fntNormalUnderlined->setBottom(1);
    $fntNormalUnderlined->setLocked();

    $fntTitles =& $xls->addFormat(array('Size' => 10));
    $fntTitles->setBold();
    $fntTitles->setAlign("center");
    $fntTitles->setLocked();

    $fntTitleNormal =& $xls->addFormat(array('Size' => 10));
    $fntTitleNormal->setAlign("center");
    $fntTitleNormal->setLocked();

    $fntColT =& $xls->addFormat(array('Size' => 8));
    $fntColT->setBorder(2);
    $fntColT->setAlign("center");
    $fntColT->setBgColor(11);
    $fntColT->setFgColor(11);
    $fntColT->setLocked();

    $fntcolNum =& $xls->addFormat(array('Size' => 8));
    $fntcolNum->setNumFormat("#,##0.00");
    $fntcolNum->setBorder(1);
    $fntcolNum->setAlign("center");

    $fntMsg =& $xls->addFormat(array('Size' => 8));
    $fntMsg->setBorder(1);
    $fntMsg->setAlign("center");
    $fntMsg->setLocked();

    $fntBig =& $xls->addFormat(array('Size' => 12));
    $fntBig->setLocked();

    $fntBigBold =& $xls->addFormat(array('Size' => 12));
    $fntBigBold->setBold();
    $fntBigBold->setLocked();
 
    $fntBigBoldCenter =& $xls->addFormat(array('Size' => 12));
    $fntBigBoldCenter->setBold();
    $fntBigBoldCenter->setAlign("center");
    $fntBigBoldCenter->setLocked();
 
    $fntBold =& $xls->addFormat(array('Size' => 8));
    $fntBold->setBold();
    $fntBold->setLocked();
 
    $fntBoldCenter =& $xls->addFormat(array('Size' => 8));
    $fntBoldCenter->setAlign("center");
    $fntBoldCenter->setBold();
    $fntBoldCenter->setLocked();

    $fntBoldRight =& $xls->addFormat(array('Size' => 8));
    $fntBoldRight->setAlign("right");
    $fntBoldRight->setBold();
    $fntBoldRight->setLocked();

    $fntAmt =& $xls->addFormat(array('Size' => 8));
    $fntAmt->setNumFormat("#,##0.00");
    $fntAmt->setLocked();

    $fntAmtBold =& $xls->addFormat(array('Size' => 8));
    $fntAmtBold->setNumFormat("#,##0.00_);\(#,##0.00\)");
    $fntAmtBold->setAlign("center");
    $fntAmtBold->setBold();
    $fntAmtBold->setLocked();

    $fntNum =& $xls->addFormat(array('Size' => 8));
    $fntNum->setNumFormat("#,##0");
    $fntNum->setLocked();

    $fntNumBold =& $xls->addFormat(array('Size' => 8));
    $fntNumBold->setNumFormat("#,##0");
    $fntNumBold->setBold();
    $fntNumBold->setLocked();

    $fntDate =& $xls->addFormat(array('Size' => 8));
    $fntDate->setNumFormat("D-MMM-YYYY");
    $fntDate->setLocked();
    
    $fntTime =& $xls->addFormat(array('Size' => 8));
    $fntTime->setNumFormat("h:mm:ss AM/PM");
    $fntTime->setLocked();
