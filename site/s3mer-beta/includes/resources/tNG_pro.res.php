<?php
/*
 * ADOBE SYSTEMS INCORPORATED
 * Copyright 2007 Adobe Systems Incorporated
 * All Rights Reserved
 * 
 * NOTICE:  Adobe permits you to use, modify, and distribute this file in accordance with the 
 * terms of the Adobe license agreement accompanying it. If you have received this file from a 
 * source other than Adobe, then your use, modification, or distribution of it requires the prior 
 * written permission of Adobe.
 */

$res = array(
'BADWORDS_SQL_ERROR' => 'Error reading bad word list from database!',
'BADWORDS_SQL_ERROR_D' => 'Error reading bad word list from database! %s, %s',
'BADWORDS_FILE_ERROR' => 'Error reading bad words from file.',
'BADWORDS_FILE_ERROR_D' => 'Error reading bad words from file: %s',
'FOLDER_DEL_ERROR' => 'Error deleting folder. Server error is: %s',
'FOLDER_DEL_ERROR_D' => 'Error deleting folder. Server error is: %s',
'FORBIDDEN_FIELD_ERROR' => 'Field has forbidden words.',
'FORBIDDEN_FIELD_ERROR_D' => '',
'LOGIN_LOGGER_ERROR' => '\'An error occured when logging in.',
'LOGIN_LOGGER_ERROR_D' => 'An error occured when saving log information: %s, %s',
'LOGIN_MESSAGE__MAXTRIES' => 'The account has been disabled because you reached the maximum allowed failed login attempts.',
'LOGIN_MESSAGE__MAXTRIES_D' => '',
'LOGIN_MESSAGE__ACCOUNT_EXPIRE' => 'Your account has expired.',
'LOGIN_MESSAGE__MAXTRIES_DENIED' => 'The account has been temporarily disabled (%s minutes) because you have reached the maximum allowed failed login attempts (%s)',
'LOGIN_MESSAGE__MAXTRIES_DENIED_D' => '',
'LOGIN_MESSAGE__MAXTRIES_DENIED_PERMANENT' => 'The account has been permanently disabled (%s minutes) because you have reached the maximum allowed failed login attempts (%s)',
'LOGIN_MESSAGE__MAXTRIES_DENIED_PERMANENT_D' => '',
'LOGIN_MESSAGE__MAXTRIES_ERROR_D' => 'Error executing the SQL %s, %s',
'TRIGGER_MESSAGE__CHECK_FORBIDDEN_WORDS' => 'Forbidden words found for the field(s) \'%s\'.',
'ERR_DOWNLOAD_FILE_D' => 'Error downloading file! %s, %s',
'LOGIN_MESSAGE__MAXTRIES_ERROR_D' => 'Error executing the SQL %s, %s',
'LOGIN_MESSAGE__EXP_ACCOUNT_ERROR' => 'Error executing the SQL',
'LOGIN_MESSAGE__EXP_ACCOUNT_ERROR_D' => 'Error executing the SQL %s, %s',
'LOGIN_MESSAGE__EXP_ACCOUNT' => 'Your Account has expired!',
'LOGIN_MESSAGE__EXP_ACCOUNT_D' => '',
'INCREMENTER_ERROR' => 'Error incrementing the counter field!',
'INCREMENTER_ERROR_D' => 'Error incrementing the counter field! %s, %s',
'INCREMENTER_ERROR_FK' => 'You cannot download any files unless you have logged in!',
'INCREMENTER_ERROR_FK_D' => 'You cannot use the Download Files behavior if you are not logged in!<br/>WARNING: You should apply a Restrict Access to Page behavior on all files where you use Download Files with limit per user! %s',
'INCREMENTER_ERROR_PK' => 'Error downloading file! Primary key has no value.',
'INCREMENTER_ERROR_PK_D' => 'Error downloading file! Primary key has no value! %s',
'CHECK_COUNTER_ERROR' => 'Error incrementing the download counter! %s',
'CHECK_COUNTER_ERROR_D' => 'Error incrementing the download counter! %s',
'CHECK_COUNTER_ERROR_MAX' => 'You have reached the maximum number of downloads %s',
'CHECK_COUNTER_ERROR_MAX_D' => '',
'TRIGGER_MESSAGE__CHECK_FORBIDDEN_WORDS_D' => '',
'EMAIL_ERROR_FOLDER' => 'An error occured when reading the folder with the attachment.',
'EMAIL_ERROR_FOLDER_D' => 'An error occured when reading the folder with the attachment %s, %s.',
'MAX_FILES_NO_REACHED' => 'Maximum number of files has been reached (%s)',
'FOLDER_DEL_SECURITY_ERROR' => 'Folder error. Security Error.',
'FOLDER_DEL_SECURITY_ERROR_D' => 'Folder error. Security Error. Folder \'%s\' is out of base folder \'%s\'.',
'FLASH_MAX_SIZE_REACHED' => 'Skipping file %s. File size is %s kB and maximum allowed file size is %s kB.',
'FLASH_MAX_FILES_REACHED' => 'Skipping file %s. You cannot upload more than %s files.',
'FLASH_EMPTY_FILE' => 'Skipping file %s. File size is 0 kB. You cannot upload empty files.',
'DELETE' => 'Delete',
'UPLOAD' => 'Upload',
'CLOSE' => 'Close Window',
'FILES' => 'files',
'MAXFILES' => 'of max',
'FLASH_SKIPPING' => 'Skipping file',
'FLASH_HTTPERROR' => 'Error communicating with the server to send: %s. Error: %s.',
'FLASH_HTTPERROR_HEAD' => 'HTTP Error',
'FLASH_IOERROR' => 'Error reading/writing: %s.',
'FLASH_IOERROR_HEAD' => 'IO Error',
'FLASH_COMPLETE_MSG' => 'Now closing flash upload. If the browser does not refresh automatically, click the Reload button',
'FLASH_UPLOAD_BATCH' => 'Uploaded %s of %s',
'FLASH_UPLOAD_SINGLE' => 'Uploading file',
'CLICK_ENLARGE' => 'Click to enlarge',
'EMPTY_MUP_POPUP' => 'There are no files uploaded to the server.<br /> Click the Upload button to select the files to upload.',
);
?>