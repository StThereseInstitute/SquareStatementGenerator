# SquareStatementGenerator
I needed to generate a printable statement for my organization's Square account. Since Square does not offer this functionality *nor* have they made a move to add it to their dashboard in more than 4 years (see thread https://www.sellercommunity.com/t5/Questions-How-To/How-do-I-download-or-otherwise-generate-monthly-statements/td-p/2473), I decided to route my frustration into action instead of brooding, and created my own way of handling it.

This project is written in PHP and requires the code to be loaded onto a webserver.

Instructions: Go to squareup.com/dashboard/sales/transactions, and select [EXPORT > Transactions CSV] then [Open with > Notepad] (on Windows; for other OS's, use your built-in text editor). 
When the CSV displays in NotePad, [Select All > Copy] then [Paste] the contents of the CSV into the textbox that displays when you view index.php in your web browser.

This is a WORK IN PROGRESS and made to fill MY SPECIFIC needs. The code is not polished, not always commented, and possibly full of coding faux pas... so use at your own risk! If you can improve the code or add to the functionality, please do and submit your updates! And lastly, I am not able to customize the code for anyone -- sorry :) 

I provide it to y'all in hopes of making your lives a bit easier, and maybe giving Square a push to implement something similar into their Dashboard so that users don't have to hack together work-around for something so simple as creating a printable statement <nudge nudge, wink wink> :)
