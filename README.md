# lang
Language switch easy to use within php applications

 * Quick start:
 * 1) copy / paste dir "lang" into the root of your application
 * 2) open bin/config.php and edit to your needs
 * 3) paste in your /index.php
 *          require __DIR__ . "/lang/lang.php";
 *          $lang = new lang(__DIR__);
 * 4) edit language files in /lang/lang and add new languages to your $conf in config.php
 * Do write a text from a language-file edit $lang->write("STRING") with STRING = Your expression-static
 * Do return a text for later use edit $lang->get("STRING")
 * To use dynamic elements:
 * a) edit string in language files and set {} for the dynamic element
 * b) use $lang->write or $lang->get to use the text. Edit offset with "(STRING_TO_REPLACE|DYNAMIC_VALUE)" e.g. "(AMOUNT|4)"
 * c) to use formatted values follow b) with offset "(STRING(FORMAT|DYNAMIC_VALUE))" e.g. "(APPOINTMENT(DATE|" . time() . "))"
